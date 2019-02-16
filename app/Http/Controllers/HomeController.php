<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
		//if(auth()->user()->isAdmin == 1) {
			$posts = DB::table('users')->leftjoin('posts', 'users.id', '=', 'posts.author')->paginate(10);
		//}else{
			//$posts = DB::table('users')->leftjoin('posts', 'users.id', '=', 'posts.author')->where('users.id', '=', Auth::user()->id)->paginate(10);
		//}
		//echo "<pre>";
		//print_r($posts);exit;
        return view('home', ['posts' => $posts]);
    }
	
	public function admin() {
		return view('admin');
	}

    public function getPostForm() {
        return view('post/post_form');
    }

    public function createPost(Request $request){
        $post = Post::create(array(
            'title' => Input::get('title'),
            'description' => Input::get('description'),
            'author' => Auth::user()->id
        ));
        return redirect()->route('home')->with('success', 'Post has been successfully added!');
    }

    public function getPost($id){
		$post = Post::find($id);
        return view('post/post_detail', ['post' => $post, 'author'=>$_REQUEST['author']]);
    }
	
	public function editPost($id) {
		if(auth()->user()->isAdmin == 1){
			$post = Post::find($id);
		}else{
			$post = DB::table('users')->join('posts', 'users.id', '=', 'posts.author')->where('posts.id', '=', $id)->first();
		}
        return view('post/edit_post', ['post' => $post]);
    }
	
	 public function updatePost(Request $request, $id) {
        $post = Post::find($id);
        $post->title = $request->title;
        $post->description = $request->description;
        $post->save();
        return redirect()->route('home')->with('success', 'Post has been updated successfully!');
    }
	
	public function deletePost($id) {
        $post = Post::find($id);
        $post->delete();
        return redirect()->route('home')->with('success', 'Post has been deleted successfully!');
    }
}