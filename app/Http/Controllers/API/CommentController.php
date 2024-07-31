<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController;
use Illuminate\Http\JsonResponse;

class CommentController extends BaseController
{
    /**
     * Retrieve all comments.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        $comments = Comment::join('posts', 'post_id', '=', 'posts.id')
            ->select('comments.*', 'posts.title')
            ->orderBy('comments.created_at', 'desc')
            ->paginate(10);

        return $this->sendResponse($comments, 'Comments retrieved successfully.');
    }

    /**
     * Store a newly created comment in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        return Comment::store($request);
    }

    /**
     * Display the specified comment.
     *
     * @param string $id The ID of the comment to display.
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        $comment = Comment::find($id);
        if (!$comment) {
            return $this->sendError('Comment not found.');
        }

        return $this->sendResponse($comment, 'Comment retrieved successfully.');
    }

    /**
     * Update a comment.
     *
     * @param  Request  $request
     * @param  Comment  $comment
     * @return JsonResponse
     */
    public function update(Request $request, Comment $comment): JsonResponse
    {
        return Comment::updateComment($request, $comment);
    }

    /**
     * Remove the specified comment from storage.
     *
     * @param  Comment  $comment
     * @return JsonResponse
     */
    public function destroy(Comment $comment): JsonResponse
    {
        $comment->delete();
        return $this->sendResponse([], 'Comment deleted successfully.');
    }
}
