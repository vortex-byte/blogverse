<?php

namespace App\Http\Controllers\API;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\API\BaseController;

class PostController extends BaseController
{
    /**
     * Retrieve all posts.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        return Post::getAllPosts($request);
    }

    /**
     * Store a newly created post in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        return Post::store($request);
    }

    /**
     * Display the post.
     *
     * @param string $slug The slug of the post.
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($slug): JsonResponse
    {
        return Post::show($slug);
    }

    /**
     * Search for posts based on the given request.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        return Post::search($request);
    }

    /**
     * Update the post in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Post $post): JsonResponse
    {
        return Post::updatePost($request, $post);
    }

    /**
     * Remove the post from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post): JsonResponse
    {
        $post->delete();

        return $this->sendResponse([], 'Post deleted successfully.');
    }
}
