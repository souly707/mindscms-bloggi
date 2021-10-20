<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
// use App\Models\Category;
use App\Models\Comment;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;
// use Intervention\Image\Facades\Image;
use Stevebauman\Purify\Facades\Purify;
use Illuminate\Support\Facades\Validator;


class PostCommentsController extends Controller
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

        if (!\auth()->user()->ability('admin', 'manage_post_comments,show_post_comments')) {
            return redirect('admin/index');
        }

        $keyword = (isset(\request()->keyword) && \request()->keyword != '') ? \request()->keyword : null;
        $post_id = (isset(\request()->post_id) && \request()->post_id != '') ? \request()->post_id : null;
        $status = (isset(\request()->status) && \request()->status != '') ? \request()->status : null;
        $sort_by = (isset(\request()->sort_by) && \request()->sort_by != '') ? \request()->sort_by : 'id';
        $order_by = (isset(\request()->order_by) && \request()->order_by != '') ? \request()->order_by : 'desc';
        $limit_by = (isset(\request()->limit_by) && \request()->limit_by != '') ? \request()->limit_by : '10';

        $comments = Comment::query();
        if ($keyword != null) {
            $comments = $comments->search($keyword);
        }
        if ($post_id != null) {
            $comments = $comments->wherePostId($comments);
        }
        if ($status != null) {
            $comments = $comments->whereStatus($status);
        }

        $comments = $comments->orderBy($sort_by, $order_by);
        $comments = $comments->paginate($limit_by);

        $posts = Post::wherePostType('post')->pluck('title', 'id');
        return view('backend.post_comments.index', compact('comments', 'posts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!\auth()->user()->ability('admin', 'update_post_comments')) {
            return redirect('admin/index');
        }

        $comment = Comment::where('id', $id)->first();
        return view('backend.post_comments.edit', compact('comment'));
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
        if (!\auth()->user()->ability('admin', 'update_post_comments')) {
            return redirect('admin/index');
        }

        // Valdate the request
        $valdator =  Validator::make($request->all(), [
            'name'          => 'required',
            'email'         => 'required|email',
            'url'           => 'nullable|url',
            'status'        => 'required',
            'comment'       => 'required',
        ]);

        if ($valdator->fails()) {
            return redirect()->back()->withErrors($valdator)->withInput();
        }
        // fetch the post
        $comment = Comment::where('id', $id)->first();
        if ($comment) {
            // Update the data request
            $data['name']      = Purify::clean($request->name);
            $data['email']     = $request->email;
            $data['url']       = $request->url;
            $data['status']    = $request->status;
            $data['comment']   = Purify::clean($request->comment);

            $comment->update($data);

            // Reset The Cache
            Cache::forget('recent_comments');

            return redirect()->route('admin.post_comments.index')->with([
                'message' => 'Comment Updated Successfully',
                'alert-type' => 'success',
            ]);
        }

        return redirect()->route('admin.post_comments.index')->with([
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
        if (!\auth()->user()->ability('admin', 'delete_post_comments')) {
            return redirect('admin/index');
        }

        $comment = Comment::Where('id', $id)->first();

        if ($comment) {


            $comment->delete();
            return redirect()->back()->with([
                'message' => 'Comment Deleted Successfully',
                'alert-type' => 'success',
            ]);
        }

        return redirect()->back()->with([
            'message' => 'somthin wont wrong',
            'alert-type' => 'danger',
        ]);
    }
}