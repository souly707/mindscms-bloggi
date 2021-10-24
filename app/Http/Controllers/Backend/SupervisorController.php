<?php

namespace App\Http\Controllers\Backend;

use Carbon\Carbon;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\UserPermission;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Validator;

class SupervisorController extends Controller
{
    public function __construct()
    {
        if (\auth()->check()) {
            $this->middleware('auth');
        } else {
            return view('backend.auth.login');
        }
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        if (!\auth()->user()->ability('admin', 'manage_supervisors,show_supervisors')) {
            return redirect('admin/index');
        }

        $keyword = (isset(\request()->keyword) && \request()->keyword != '') ? \request()->keyword : null;
        $status = (isset(\request()->status) && \request()->status != '') ? \request()->status : null;
        $sort_by = (isset(\request()->sort_by) && \request()->sort_by != '') ? \request()->sort_by : 'id';
        $order_by = (isset(\request()->order_by) && \request()->order_by != '') ? \request()->order_by : 'desc';
        $limit_by = (isset(\request()->limit_by) && \request()->limit_by != '') ? \request()->limit_by : '10';

        $users = User::whereHas('roles', function ($query) {
            $query->where('name', 'editor');
        });

        if ($keyword != null) {
            $users = $users->search($keyword);
        }

        if ($status != null) {
            $users = $users->whereStatus($status);
        }

        $users = $users->orderBy($sort_by, $order_by);
        $users = $users->paginate($limit_by);

        return view('backend.supervisors.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!\auth()->user()->ability('admin', 'create_supervisors')) {
            return redirect('admin/index');
        }

        $permissions = Permission::pluck('display_name', 'id');
        return view('backend.supervisors.create', compact('permissions'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!\auth()->user()->ability('admin', 'create_supervisors')) {
            return redirect('admin/index');
        }

        $validated = Validator::make($request->all(), [
            'name'          => 'required',
            'username'      => 'required|max:20|unique:users',
            'email'         => 'required|email|max:255|unique:users',
            'mobile'        => 'required|numeric|unique:users',
            'status'        => 'required',
            'password'      => 'required|min:8',
            'permissions.*' => 'required'

        ]);

        //\dd($request);
        if ($validated->fails()) {
            return redirect()->back()->withErrors($validated)->withInput();
        }

        // dd($request);

        $data['name']               = $request->name;
        $data['username']           = $request->username;
        $data['email']              = $request->email;
        $data['mobile']             = $request->mobile;
        $data['email_verified_at']  = Carbon::now();

        if (trim($request->password) != '') {
            $data['password']  = bcrypt($request->password);
        }
        $data['bio']                = $request->bio;
        $data['receive_email']      = $request->receive_email;
        $data['status']             = $request->status;

        // if User has image

        if ($user_image = $request->file('user_image')) {

            $file_name = Str::slug($request->username) . '.' . $user_image->getClientOriginalExtension();
            $file_path = public_path('assets/users/' . $file_name);

            Image::make($user_image->getRealPath())->resize(300, 300, function ($constraint) {
                $constraint->aspectRatio();
            })->save($file_path, 100);

            $data['user_image'] = $file_name;
        }

        // Create The User

        $user = User::create($data);
        $user->attachRole(Role::whereName('editor')->first()->id);

        if (isset($request->permissions) && count($request->permissions) > 0) {
            $user->permissions()->sync($request->permissions);
        }

        return redirect()->route('admin.supervisors.index')->with([
            'message' => 'User Created Successfully',
            'alert-type' => 'success',
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (!\auth()->user()->ability('admin', 'display_supervisors')) {
            return redirect('admin/index');
        }

        $user = User::where('id', $id)->withCount('posts')->first();

        if ($user) {
            return view('backend.supervisors.show', compact('user'));
        }

        return redirect()->route('admin.supervisors.index')->with([
            'message' => 'something was wrong',
            'alert-type' => 'danger',
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!\auth()->user()->ability('admin', 'update_supervisors')) {
            return redirect('admin/index');
        }

        $user = User::where('id', $id)->first();

        if ($user) {

            $permissions = Permission::pluck('display_name', 'id');
            $user_permissions = UserPermission::whereUserId($id)->pluck('permission_id');
            return view('backend.supervisors.edit', compact('user', 'permissions', 'user_permissions'));
        }

        return redirect()->route('admin.supervisors.index')->with([
            'message' => 'something was wrong',
            'alert-type' => 'danger',
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (!\auth()->user()->ability('admin', 'update_supervisors')) {
            return redirect('admin/index');
        }

        // Valdate the request
        $valdator = Validator::make($request->all(), [
            'name'          => 'required',
            'username'      => 'required|max:20|unique:users,username,' . $id,
            'email'         => 'required|email|Max:255|unique:users,email,' . $id,
            'mobile'        => 'required|numeric|unique:users,mobile,' . $id,
            'status'        => 'required',
            'password'      => 'nullable|min:8',
        ]);

        if ($valdator->fails()) {
            return redirect()->back()->withErrors($valdator)->withInput();
        }
        // fetch the user
        $user = User::where('id', $id)->first();

        if ($user) {
            $data['name']               = $request->name;
            $data['username']           = $request->username;
            $data['email']              = $request->email;
            $data['mobile']             = $request->mobile;

            if (trim($request->password) != '') {
                $data['password']  = bcrypt($request->password);
            }

            $data['bio']                = $request->bio;
            $data['bio']                = $request->bio;
            $data['receive_email']      = $request->receive_email;
            $data['status']             = $request->status;

            // if User has image
            if ($user_image = $request->file('user_image')) {
                // Deleting Past User Image If he has any 
                if ($user->user_image != '') {
                    if (File::exists('assets/users/' . $user->user_image)) {
                        unlink('assets/users/' . $user->user_image);
                    }
                }

                $file_name = Str::slug($request->username) . '.' . $user_image->getClientOriginalExtension();
                $file_path = public_path('assets/users/' . $file_name);

                Image::make($user_image->getRealPath())->resize(300, 300, function ($constraint) {
                    $constraint->aspectRatio();
                })->save($file_path, 100);

                $data['user_image'] = $file_name;
            }

            // Update The User

            $user->update($data);
            if (isset($request->permissions) && count($request->permissions) > 0) {
                $user->permissions()->sync($request->permissions);
            }

            return redirect()->route('admin.supervisors.index')->with([
                'message' => 'User Updated Successfully',
                'alert-type' => 'success',
            ]);
        }

        return redirect()->route('admin.supervisors.index')->with([
            'message' => 'somthin wont wrong',
            'alert-type' => 'danger',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!\auth()->user()->ability('admin', 'delete_supervisors')) {
            return redirect('admin/index');
        }

        $user = User::whrer('id', $id)->first();

        if ($user) {
            if ($user->user_image != '') {
                if (File::exists('assets/users/' . $user->user_image)) {
                    unlink('assets/users/' . $user->user_image);
                }
            }
            $user->delete();
            return redirect()->back()->with([
                'message' => 'Supervisor Deleted Successfully',
                'alert-type' => 'success',
            ]);
        }

        return redirect()->back()->with([
            'message' => 'somthin wont wrong',
            'alert-type' => 'danger',
        ]);
    }

    public function destroy_image($id)
    {
        if (!\auth()->user()->ability('admin', 'delete_supervisors')) {
            return redirect('admin/index');
        }

        $user = User::whereId($id)->first();
        if ($user) {
            if (File::exists('assets/users/' . $user->user_image)) {
                unlink('assets/users/' . $user->user_image);
            }

            $user->user_image = null;
            $user->save();

            return redirect()->back()->with([
                'message' => 'image deleted Successfully',
                'alert-type' => 'success',
            ]);
        }

        return redirect()->back()->with([
            'message' => 'sumthing wont wrong',
            'alert-type' => 'danger',
        ]);;
    }
}