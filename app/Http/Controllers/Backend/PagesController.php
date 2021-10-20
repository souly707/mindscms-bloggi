<?php

namespace App\Http\Controllers\Backend;

use App\Models\Post;
use App\Models\Category;
use App\Models\PostMedia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;
use Intervention\Image\Facades\Image;
use Stevebauman\Purify\Facades\Purify;
use Illuminate\Support\Facades\Validator;

class PagesController extends Controller
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

        if (!\auth()->user()->ability('admin', 'manage_pages,show_pages')) {
            return redirect('admin/index');
        }

        $keyword = (isset(\request()->keyword) && \request()->keyword != '') ? \request()->keyword : null;
        $categoryId = (isset(\request()->category_id) && \request()->category_id != '') ? \request()->category_id : null;
        $status = (isset(\request()->status) && \request()->status != '') ? \request()->status : null;
        $sort_by = (isset(\request()->sort_by) && \request()->sort_by != '') ? \request()->sort_by : 'id';
        $order_by = (isset(\request()->order_by) && \request()->order_by != '') ? \request()->order_by : 'desc';
        $limit_by = (isset(\request()->limit_by) && \request()->limit_by != '') ? \request()->limit_by : '10';

        $pages = Page::wherePostType('page');
        if ($keyword != null) {
            $pages = $pages->search($keyword);
        }
        if ($categoryId != null) {
            $pages = $pages->whereCategoryId($categoryId);
        }
        if ($status != null) {
            $pages = $pages->whereStatus($status);
        }

        $pages = $pages->orderBy($sort_by, $order_by);
        $pages = $pages->paginate($limit_by);

        $categories = Category::orderBy('id', 'desc')->pluck('name', 'id');
        return view('backend.pages.index', compact('categories', 'pages'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!\auth()->user()->ability('admin', 'create_pages')) {
            return redirect('admin/index');
        }

        $categories = Category::orderBy('id', 'desc')->pluck('id', 'name');
        return view('backend.pages.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!\auth()->user()->ability('admin', 'create_pages')) {
            return redirect('admin/index');
        }

        $validated = Validator::make($request->all(), [
            'title'         => 'required',
            'description'   => 'required|min:10',
            'status'        => 'required',
            'category'      => 'required',
            'images.*'      => 'nullable|mimes:jpg,jpeg,png'
        ]);

        //\dd($request);
        if ($validated->fails()) {
            return redirect()->back()->withErrors($validated)->withInput();
        }

        // dd($request);

        $data['title']          = Purify::clean($request->title);
        $data['description']    = Purify::clean($request->description);
        $data['status']         = $request->status;
        $data['post_type']      = 'page';
        $data['comment_able']   = 0;
        $data['category_id']    = $request->category;

        $page = auth()->user()->posts()->create($data);

        // if page has images

        if ($request->images && count($request->images) > 0) {
            $file_count = 1;

            foreach ($request->images as $file) {
                $file_name = $page->slug . '-' . time() . '-' . $file_count . '.' . $file->getClientOriginalExtension();
                $file_size = $file->getSize();
                $file_type = $file->getMimeType();
                $file_path = public_path('assets/posts/' . $file_name);

                Image::make($file->getRealPath())->resize(800, null, function ($constraint) {
                    $constraint->aspectRatio();
                })->save($file_path, 100);

                $page->media()->create([
                    'file_name' => $file_name,
                    'file_size' => $file_size,
                    'file_type' => $file_type,
                ]);

                $file_count++;
            }
        }

        return redirect()->route('admin.pages.index')->with([
            'message' => 'Page Created Successfully',
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
        if (!\auth()->user()->ability('admin', 'display_pages')) {
            return redirect('admin/index');
        }

        $page = Page::with(['media'])->where('id', $id)->where('post_type', 'page')->first();

        return view('backend.pages.show', compact('page'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!\auth()->user()->ability('admin', 'update_pages')) {
            return redirect('admin/index');
        }

        $categories = Category::orderBy('id', 'desc')->pluck('id', 'name');
        $page = Page::with('media')->where('id', $id)->wherePostType('page')->first();
        return view('backend.pages.edit', compact('categories', 'page'));
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
        if (!\auth()->user()->ability('admin', 'update_pages')) {
            return redirect('admin/index');
        }

        // Valdate the request
        $valdator =  Validator::make($request->all(), [
            'title'         => 'required|min:5',
            'description'   => 'required|min:20',
            'status'        => 'required',
            'category'      => 'required',
            'images.*'      => 'nullable|mimes:jpg,jpeg,png',
        ]);

        if ($valdator->fails()) {
            return redirect()->back()->withErrors($valdator)->withInput();
        }
        // fetch the Page
        $page = Page::where('id', $id)->wherePostType('page')->first();
        if ($page) {
            // Update the data request
            $data['title']          = Purify::clean($request->title);
            $data['slug']           = null;
            $data['description']    = Purify::clean($request->description);
            $data['status']         = $request->status;
            $data['category_id']    = $request->category;

            $page->update($data);

            // check if ther is images
            if ($request->images && $request->images > 0) {
                $file_count = 1;

                foreach ($request->images as $file) {
                    $file_name = $page->slug . '-' . \time() . '-' . $file_count . '.' . $file->getClientOriginalExtension();
                    $file_size = $file->getSize();
                    $file_type = $file->getMimeType();
                    $file_path = public_path('assets/posts/' . $file_name);

                    Image::make($file->getRealPath())->resize(800, null, function ($constraint) {
                        $constraint->aspectRatio();
                    })->save($file_path, 100);

                    $page->media()->create([
                        'file_name' => $file_name,
                        'file_size' => $file_size,
                        'file_type' => $file_type,
                    ]);

                    $file_count++;
                }
            }

            return redirect()->route('admin.pages.index')->with([
                'message' => 'Page Updated Successfully',
                'alert-type' => 'success',
            ]);
        }

        return redirect()->route('admin.pages.index')->with([
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
        if (!\auth()->user()->ability('admin', 'delete_pages')) {
            return redirect('admin/index');
        }

        $page = Page::Where('id', $id)->wherePostType('page')->first();

        if ($page) {
            if ($page->media->count() > 0) {
                foreach ($page->media as $media) {
                    if (File::exists('assets/posts/' . $media->file_name)) {
                        unlink('assets/posts/' . $media->file_name);
                    }
                }
            }

            $page->delete();
            return redirect()->back()->with([
                'message' => 'Page Deleted Successfully',
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
        if (!\auth()->user()->ability('admin', 'delete_pages')) {
            return redirect('admin/index');
        }

        $media = PostMedia::whereId($id)->first();
        if ($media) {
            if (File::exists('assets/posts/' . $media->file_name)) {
                unlink('assets/posts/' . $media->file_name);
            }

            $media->delete();
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