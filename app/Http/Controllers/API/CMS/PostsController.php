<?php

namespace App\Http\Controllers\API\CMS;

use App\Http\Controllers\Controller;
use App\Post;
use App\PostTranslation;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PostsController extends Controller {

    public function index(Request $request) {
        $posts = Post::with('translations');

        if ($request->term)
            $posts = $posts->whereHas('translations', function ($q) use ($request) {
                $q->where('title', 'like', '%'.$request->term.'%');
            });

        return $posts->orderBy('id', 'desc')->paginate(20);
    }

    public function show(Request $request, $id) {
        $post = Post::findOrFail($id);
        $post->translations;
        $post->author;
        return $post;
    }

    public function create(Request $request) {
        $validate = Validator::make($request->all(), [
            'cover' => 'required',
            'cover_2'=>'required',
            'titleEN' => 'required',
            'descriptionEN' => 'required',
            'titleCH' => 'required',
            'descriptionCH' => 'required',

        ]);
        if ($validate->fails()) {
            $errors = $validate->errors()->all();
            return \response()->json(['errors' => $errors], 422);
        }

        $post = new Post();
        $post->user_id = Auth::guard('api')->id();
        $post->cover = $request->cover;
        $post->main_cover = $request->cover_2;
        $post->save();

        $englishTranslation = new PostTranslation();
        $englishTranslation->id = $post->id;
        $englishTranslation->title = $request->titleEN;
        $englishTranslation->description = $request->descriptionEN;
        $englishTranslation->lang = 'en';
        $englishTranslation->save();

        $chineseTranslation = new PostTranslation();
        $chineseTranslation->id = $post->id;
        $chineseTranslation->title = $request->titleCH;
        $chineseTranslation->description = $request->descriptionCH;
        $chineseTranslation->lang = 'zh-CN';
        $chineseTranslation->save();

        $post->translations;

        return response()->json($post, Response::HTTP_CREATED);
    }

    public function update(Request $request, $id) {
        $post = Post::findOrFail($id);

        if ($request->cover) {
            $post->cover = $request->cover;
            $post->save();
        } else if ($request->cover_2) {
            $post->main_cover = $request->cover_2;
            $post->save();
        } else {
            PostTranslation::where('id', $id)->where('lang', 'en')->update(['title' => $request->titleEN, 'description' => $request->descriptionEN]);
            PostTranslation::where('id', $id)->where('lang', 'zh-CN')->update(['title' => $request->titleCH, 'description' => $request->descriptionCH]);
        }

        $post->translations;

        return $post;
    }

    public function delete(Request $request, $id) {
        $post = Post::findOrFail($id);
        if ($post->cover)
            Storage::delete('public/'.$post->cover);
        if ($post->main_cover)
            Storage::delete('public/' . $post->main_cover);
        $post->delete();

        return respondOK();
    }

}
