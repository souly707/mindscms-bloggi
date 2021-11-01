<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Contact;
use App\Models\Post;
use App\Models\User;
use App\Notifications\NewCommentForAdminNotify;
use App\Notifications\NewCommentForPostOwnerNotify;
//use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class IndexController extends Controller
{
    public function Index()
    {
        $posts = Post::with(['media', 'user'])

            ->whereHas('category', function ($query) {
                $query->whereStatus(1);
            })->whereHas('user', function ($query) {

                $query->whereStatus(1);
            })->post()->active()->orderBy('id', 'desc')->paginate(5);

        return view('frontend.index', compact('posts'));
    }

    public function search(Request $request)
    {
        $keyword = isset($request->keyword) && $request->keyword != '' ? $request->keyword : null;

        $posts = Post::with(['media', 'user'])
            ->whereHas('category', function ($query) {
                $query->whereStatus(1);
            })->whereHas('user', function ($query) {
                $query->whereStatus(1);
            });

        if ($keyword != null) {
            $posts = $posts->search($keyword, null, true);
        }

        $posts = $posts->post()->active()->orderBy('id', 'desc')->paginate(5);

        return view('frontend.index', compact('posts'));
    }

    public function post_show($slug)
    {
        $post = Post::with([
            'category', 'media', 'user',
            'approved_comments' => function ($query) {
                $query->orderBy('id', 'desc');
            }
        ]);
        $post = $post->whereHas('category', function ($query) {
            $query->whereStatus(1);
        })->whereHas('user', function ($query) {
            $query->whereStatus(1);
        });

        $post = $post->whereSlug($slug);

        $post = $post->active()->first();

        if ($post) {

            $blade = $post->post_type == 'post' ? 'post' : 'page';
            return view('frontend.' . $blade, compact('post'));
        } else {
            return redirect()->route('frontend.index');
        }
    }


    public function store_comment(Request $request, $slug)
    {
        $validation = Validator::make($request->all(), [
            'name'      => 'required|string',
            'email'     => 'required|email',
            'url'       => 'nullable|url',
            'comment'   => 'required|string|min:10',
        ]);

        if ($validation->fails()) {
            return \redirect()->back()->withErrors($validation)->withInput();
        }

        $post = Post::whereSlug($slug)->wherePostType('post')->whereStatus(1)->first();

        if ($post) {

            $userId = auth()->check() ? auth()->id() : null;

            $data['name'] = $request->name;
            $data['email'] = $request->email;
            $data['url'] = $request->url;
            $data['ip_address'] = $request->ip();
            $data['comment'] = $request->comment;
            $data['post_id'] = $post->id;
            $data['user_id'] = $userId;

            $comment = $post->comments()->create($data);

            // Send notification for comment
            if (auth()->guest() || auth()->id != $post->user_id) {
                $post->user->notify(new NewCommentForPostOwnerNotify($comment));
            }

            // Send notification for For User With Roles 
            User::whereHas('roles', function ($query) {
                $query->whereIn('name', ['admin', 'editor']);
            })->each(function ($admin, $key) use ($comment) {
                $admin->notify(new NewCommentForAdminNotify($comment));
            });

            return redirect()->back()->with([
                'message'    => 'Comment added successfully',
                'alert-type' => 'success'
            ]);
        }

        return redirect()->back()->with([
            'message'    => 'something was wrong',
            'alert-type' => 'danger'
        ]);
    }

    public function contact()
    {
        return view('frontend.contact');
    }


    public function do_contact(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'name'      => 'required',
            'email'     => 'required|email',
            'mobile'    => 'nullable|numeric',
            'title'     => 'required|string|min:5',
            'message'   => 'required|string|min:7',
        ]);

        if ($validation->fails()) {
            return redirect()->back()->withErrors($validation)->withInput();
        }

        $data['name']    = $request->name;
        $data['email']   = $request->email;
        $data['mobile']  = $request->mobile;
        $data['title']   = $request->title;
        $data['message'] = $request->message;

        Contact::create($data);

        return redirect()->back()->with([
            'message'    => 'Message sent succesfully',
            'alert-type' => 'success'
        ]);
    }

    function category($slug)
    {
        $category = Category::whereSlug($slug)->orWhere('id', $slug)->whereStatus(1)->first()->id;

        if ($category) {
            $posts = Post::with(['media', 'user'])
                ->whereCategoryId($category)
                ->post()
                ->active()
                ->orderBy('id', 'desc')
                ->paginate(5);

            return view('frontend.index', compact('posts'));
        }

        return redirect()->route('frontend.index');
    }

    function archive($date)
    {
        // format date laik 12-2020
        $exploded_date = explode('-', $date);
        $month = $exploded_date[0];
        $year = $exploded_date[1];

        // fetch the data from database 
        $posts = Post::with(['media', 'user'])
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->post()
            ->active()
            ->orderBy('id', 'desc')
            ->paginate(5);

        return view('frontend.index', compact('posts'));
    }

    function author($username)
    {
        $user = User::whereusername($username)->orWhere('id', $username)->whereStatus(1)->first()->id;

        if ($user) {
            $posts = Post::with(['media', 'user'])
                ->whereUserId($user)
                ->post()
                ->active()
                ->orderBy('id', 'desc')
                ->paginate(5);

            return view('frontend.index', compact('posts'));
        }

        return redirect()->route('frontend.index');
    }
}