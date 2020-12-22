<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Post;
use Illuminate\Http\Request;

class PostsController extends Controller {

    public function index(Request $request) {
        $posts = Post::with('translations');
        return $posts->orderBy('id', 'desc')->paginate(20);
    }

    public function show(Request $request, $id) {
        $post = Post::findOrFail($id);
        $post->translations;
        $post->author;
        return $post;
    }

}
