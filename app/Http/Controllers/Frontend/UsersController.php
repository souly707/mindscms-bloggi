<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Post;
use App\Models\Comment;
use App\Models\Category;
use App\Models\PostMedia;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Intervention\Image\Facades\Image;
use Stevebauman\Purify\Facades\Purify;
use Illuminate\Support\Facades\Validator;

class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function index()
    {
        $posts = auth()->user()->posts()->with(['category', 'media', 'user'])
            ->withCount('comments')->orderBy('id', 'desc')->paginate(10);

        return view('frontend.users.dashboard', compact('posts'));
    }

    public function edit_info()
    {
        return view('frontend.users.edit_info');
    }

    public function update_info(Request $request)
    {
        $valdator = Validator::make($request->all(), [
            'name'          => 'required|string|max:30',
            'email'         => 'required|email',
            'mobile'        => 'required|numeric',
            'bio'           => 'nullable|min:10',
            'receive_email' => 'required',
            'user_image'    => 'nullable|image|max:20000,memes:jpeg,jpg,png'

        ]);

        if ($valdator->fails()) {
            return redirect()->back()->withErrors($valdator)->withInput();
        }


        $data['name']           = $request->name;
        $data['email']          = $request->email;
        $data['mobile']         = $request->mobile;
        $data['bio']            = $request->bio;
        $data['receive_email']  = $request->receive_email;

        if ($image = $request->file('user_image')) {
            if (auth()->user()->user_image != '') {
                if (File::exists('assets/users/' . auth()->user()->user_image)) {
                    \unlink('assets/users/' . auth()->user()->user_image);
                }
            }

            $file_name = Str::slug(auth()->user()->username) . '.' . $image->getClientOriginalExtension();
            $file_path = public_path('assets/users/' . $file_name);

            Image::make($image->getRealPath())->resize(300, 300, function ($constraint) {
                $constraint->aspectRatio();
            })->save($file_path, 100);

            $data['user_image'] = $file_name;
        }

        $update = auth()->user()->update($data);

        if ($update) {
            return redirect()->back()->with([
                'message' => 'Information Updated Successfully',
                'alert-type' => 'success',
            ]);
        } else {
            return redirect()->back()->with([
                'message' => 'somethin was wrong',
                'alert-type' => 'danger',
            ]);
        }
    }

    public function update_password(Request $request)
    {
        $valdator = Validator::make($request->all(), [
            'current_password' => 'required',
            'password'         => 'required|confirmed',
        ]);

        if ($valdator->fails()) {
            return redirect()->back()->withErrors($valdator)->withInput();
        }

        if (Hash::check($request->current_password, auth()->user()->password)) {
            $update = auth()->user()->update([
                'password' => bcrypt($request->password),
            ]);
            if ($update) {
                return redirect()->back()->with([
                    'message' => 'Information Updated Successfully',
                    'alert-type' => 'success',
                ]);
            } else {
                return redirect()->back()->with([
                    'message' => 'somethin was wrong',
                    'alert-type' => 'danger',
                ]);
            }
        } else {
            return redirect()->back()->with([
                'message' => 'somethin was wrong',
                'alert-type' => 'danger',
            ]);
        }
    }

    public function create_post()
    {
        $categories = Category::where('status', 1)->pluck('id', 'name');
        return view('frontend.users.create_post', compact('categories'));
    }

    public function store_post(Request $request)
    {
        // Valdate the request
        $valdator = Validator::make($request->all(), [
            'title'         => 'required|min:5',
            'description'   => 'required|min:20',
            'status'        => 'required',
            'comments_able' => 'required',
            'category'      => 'required',
        ]);

        //dd($request->all());

        if ($valdator->fails()) {
            return redirect()->back()->withErrors($valdator)->withInput();
        }

        $data['title']          = Purify::clean($request->title);
        $data['description']    = Purify::clean($request->description);
        $data['status']         = $request->status;
        $data['comment_able']  = $request->comments_able;
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

        return redirect()->back()->with([
            'message' => 'Post Created Successfully',
            'alert-type' => 'success',
        ]);
    }

    public function edit_post($post_id)
    {
        // get the post
        $post = Post::whereSlug($post_id)->orWhere('id', $post_id)->whereUserId(auth()->id())->first();

        if ($post) {
            // get the categories
            $categories = Category::where('status', 1)->pluck('id', 'name');
            return view('frontend.users.edit_post', compact('post', 'categories'));
        }

        return redirect()->route('frontend.index');
    }

    public function update_post(Request $request, $post_id)
    {
        // Valdate the request
        $valdator =  Validator::make($request->all(), [
            'title'         => 'required|min:5',
            'description'   => 'required|min:20',
            'status'        => 'required',
            'comments_able' => 'required',
            'category'      => 'required',
        ]);

        if ($valdator->fails()) {
            return redirect()->back()->withErrors($valdator)->withInput();
        }
        // fetch the post
        $post = Post::whereSlug($post_id)->orWhere('id', $post_id)->whereUserId(auth()->user()->id)->first();
        if ($post) {
            // Update the data request
            $data['title']          = Purify::clean($request->title);
            $data['description']    = Purify::clean($request->description);
            $data['status']         = $request->status;
            $data['comment_able']  = $request->comments_able;
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

            return redirect()->back()->with([
                'message' => 'Post Updated Successfully',
                'alert-type' => 'success',
            ]);
        }

        return redirect()->back()->with([
            'message' => 'somthin wont wrong',
            'alert-type' => 'danger',
        ]);
    }

    public function destroy_post($post_id)
    {
        $post = Post::where('slug', $post_id)->orWhere('id', $post_id)
            ->where('user_id', auth()->id())->first();

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
                'message' => 'Post Updated Successfully',
                'alert-type' => 'success',
            ]);
        }

        return redirect()->back()->with([
            'message' => 'somthin wont wrong',
            'alert-type' => 'danger',
        ]);
    }

    public function destroy_post_media($media_id)
    {
        $media = PostMedia::whereId($media_id)->first();
        if ($media) {
            if (File::exists('assets/posts/' . $media->file_name)) {
                unlink('assets/posts/' . $media->file_name);
            }

            $media->delete();
            return true;
        }

        return false;
    }

    // comments
    public function show_comments(Request $request)
    {

        $comments = Comment::query();

        if (isset($request->post) && $request->post != '') {
            $comments = $comments->wherePostId($request->post);
        } else {
            $posts_id = auth()->user()->posts->pluck('id')->toArray();
            $comments = $comments->whereIn('post_id', $posts_id);
        }
        $comments = $comments->orderBy('id', 'desc');
        $comments = $comments->paginate(10);

        return view('frontend.users.comments', compact('comments'));
    }

    public function edit_comment($comment_id)
    {
        //dd($comment_id);
        $comment = Comment::whereId($comment_id)->whereHas('post', function ($query) {
            $query->where('posts.user_id', auth()->id());
        })->first();
        //dd($comment);

        if ($comment) {
            return view('frontend.users.edit_comment', compact('comment'));
        } else {
            return redirect()->back()->with([
                'message' => 'Something was wrong',
                'alert-type' => 'danger',
            ]);
        }
    }
    public function update_comment(Request $request, $comment_id)
    {
        $validator = Validator::make($request->all(), [
            'name'          => 'required',
            'email'         => 'required|email',
            'url'           => 'nullable|url',
            'status'        => 'required',
            'comment'       => 'required',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $comment = Comment::whereId($comment_id)->whereHas('post', function ($query) {
            $query->where('posts.user_id', auth()->id());
        })->first();

        if ($comment) {
            $data['name']          = $request->name;
            $data['email']         = $request->email;
            $data['url']           = $request->url != '' ? $request->url : null;
            $data['status']        = $request->status;
            $data['comment']       = Purify::clean($request->comment);

            $comment->update($data);

            if ($request->status == 1) {
                Cache::forget('recent_comments');
            }

            return redirect()->back()->with([
                'message' => 'Comment updated successfully',
                'alert-type' => 'success',
            ]);
        } else {
            return redirect()->back()->with([
                'message' => 'Something was wrong',
                'alert-type' => 'danger',
            ]);
        }
    }
    public function destroy_comment($comment_id)
    {
        $comment = Comment::whereId($comment_id)->whereHas('post', function ($query) {
            $query->where('posts.user_id', auth()->id());
        })->first();

        if ($comment) {
            $comment->delete();

            Cache::forget('recent_comments');

            return redirect()->back()->with([
                'message' => 'Comment deleted successfully',
                'alert-type' => 'success',
            ]);
        } else {
            return redirect()->back()->with([
                'message' => 'Something was wrong',
                'alert-type' => 'danger',
            ]);
        }
    }
}