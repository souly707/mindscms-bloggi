<?php

namespace App\Http\Controllers\Backend;

use App\Models\Post;
use App\Models\Category;
use App\Models\PostMedia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;
use Intervention\Image\Facades\Image;
use Stevebauman\Purify\Facades\Purify;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
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

        if (!\auth()->user()->ability('admin', 'manage_posts,show_posts')) {
            return redirect('admin/index');
        }

        $keyword = (isset(\request()->keyword) && \request()->keyword != '') ? \request()->keyword : null;
        $categoryId = (isset(\request()->category_id) && \request()->category_id != '') ? \request()->category_id : null;
        $status = (isset(\request()->status) && \request()->status != '') ? \request()->status : null;
        $sort_by = (isset(\request()->sort_by) && \request()->sort_by != '') ? \request()->sort_by : 'id';
        $order_by = (isset(\request()->order_by) && \request()->order_by != '') ? \request()->order_by : 'desc';
        $limit_by = (isset(\request()->limit_by) && \request()->limit_by != '') ? \request()->limit_by : '10';

        $posts = Post::with(['user', 'category', 'comments'])->wherePostType('post');
        if ($keyword != null) {
            $posts = $posts->search($keyword);
        }
        if ($categoryId != null) {
            $posts = $posts->whereCategoryId($categoryId);
        }
        if ($status != null) {
            $posts = $posts->whereStatus($status);
        }

        $posts = $posts->orderBy($sort_by, $order_by);
        $posts = $posts->paginate($limit_by);

        $categories = Category::orderBy('id', 'desc')->pluck('name', 'id');
        return view('backend.posts.index', compact('categories', 'posts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!\auth()->user()->ability('admin', 'create_posts')) {
            return redirect('admin/index');
        }

        $categories = Category::orderBy('id', 'desc')->pluck('id', 'name');
        return view('backend.posts.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!\auth()->user()->ability('admin', 'create_posts')) {
            return redirect('admin/index');
        }

        $validated = Validator::make($request->all(), [
            'title'         => 'required',
            'description'   => 'required|min:10',
            'status'        => 'required',
            'comment_able'  => 'required',
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
        $data['post_type']      = 'post';
        $data['comment_able']   = $request->comment_able;
        $data['category_id']    = $request->category;

        $post = auth()->user()->posts()->create($data);

        // if post has images

        if ($request->images && count($request->images) > 0) {
            $file_count = 1;

            foreach ($request->images as $file) {
                $file_name = $post->slug . '-' . time() . '-' . $file_count . '.' . $file->getClientOriginalExtension();
                $file_size = $file->getSize();
                $file_type = $file->getMimeType();
                $file_path = public_path('assets/posts/' . $file_name);

                Image::make($file->getRealPath())->resize(800, null, function ($constraint) {
                    $constraint->aspectRatio();
                })->save($file_path, 100);

                $post->media()->create([
                    'file_name' => $file_name,
                    'file_size' => $file_size,
                    'file_type' => $file_type,
                ]);

                $file_count++;
            }
        }

        if ($request->status == 1) {
            Cache::forget('recent_posts');
        }

        return redirect()->route('admin.posts.index')->with([
            'message' => 'Post Created Successfully',
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
        if (!\auth()->user()->ability('admin', 'display_posts')) {
            return redirect('admin/index');
        }

        $post = Post::with(['category', 'media', 'comments', 'user'])
            ->where('id', $id)->post()->first();

        return view('backend.posts.show', compact('post'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!\auth()->user()->ability('admin', 'update_posts')) {
            return redirect('admin/index');
        }

        $categories = Category::orderBy('id', 'desc')->pluck('id', 'name');
        $post = Post::with('media')->where('id', $id)->post()->first();
        return view('backend.posts.edit', compact('categories', 'post'));
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
        if (!\auth()->user()->ability('admin', 'update_posts')) {
            return redirect('admin/index');
        }

        // Valdate the request
        $valdator =  Validator::make($request->all(), [
            'title'         => 'required|min:5',
            'description'   => 'required|min:20',
            'status'        => 'required',
            'comment_able' => 'required',
            'category'      => 'required',
            'images.*'      => 'nullable|mimes:jpg,jpeg,png',
        ]);

        if ($valdator->fails()) {
            return redirect()->back()->withErrors($valdator)->withInput();
        }
        // fetch the post
        $post = Post::where('id', $id)->post()->first();
        if ($post) {
            // Update the data request
            $data['title']          = Purify::clean($request->title);
            $data['slug']           = null;
            $data['description']    = Purify::clean($request->description);
            $data['status']         = $request->status;
            $data['comment_able']   = $request->comments_able;
            $data['category_id']    = $request->category;

            $post->update($data);

            // check if ther is images
            if ($request->images && $request->images > 0) {
                $file_count = 1;

                foreach ($request->images as $file) {
                    $file_name = $post->slug . '-' . \time() . '-' . $file_count . '.' . $file->getClientOriginalExtension();
                    $file_size = $file->getSize();
                    $file_type = $file->getMimeType();
                    $file_path = public_path('assets/posts/' . $file_name);

                    Image::make($file->getRealPath())->resize(800, null, function ($constraint) {
                        $constraint->aspectRatio();
                    })->save($file_path, 100);

                    $post->media()->create([
                        'file_name' => $file_name,
                        'file_size' => $file_size,
                        'file_type' => $file_type,
                    ]);

                    $file_count++;
                }
            }

            return redirect()->route('admin.posts.index')->with([
                'message' => 'Post Updated Successfully',
                'alert-type' => 'success',
            ]);
        }

        return redirect()->route('admin.posts.index')->with([
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
        if (!\auth()->user()->ability('admin', 'delete_posts')) {
            return redirect('admin/index');
        }

        $post = Post::Where('id', $id)->post()->first();

        if ($post) {
            if ($post->media->count() > 0) {
                foreach ($post->media as $media) {
                    if (File::exists('assets/posts/' . $media->file_name)) {
                        unlink('assets/posts/' . $media->file_name);
                    }
                }
            }

            $post->delete();
            return redirect()->back()->with([
                'message' => 'Post Deleted Successfully',
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
        if (!\auth()->user()->ability('admin', 'delete_posts')) {
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