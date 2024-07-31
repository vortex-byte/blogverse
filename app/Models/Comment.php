<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Http\JsonResponse;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'email', 'comment', 'reply_to', 'post_id', 'status'];

    /**
     * Get the posts associated with the comment.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function posts()
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * Store a new comment.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public static function store(Request $request): JsonResponse
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'name' => 'required|string',
            'email' => 'required|email',
            'comment' => 'required|string',
            'reply_to' => 'sometimes|numeric',
            'post_id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return Response::sendError('Request not valid.', $validator->errors()->all(), 422);
        }

        $input['status'] = 'pending';

        try {
            $addComment = self::create($input);
            return Response::sendResponse($addComment, 'Comment added successfully');
        } catch (\Exception $e) {
            return Response::sendError('Failed to add comment.', [$e->getMessage()], 422);
        }
    }

    /**
     * Update a comment.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\JsonResponse
     */
    public static function updateComment(Request $request, Comment $comment): JsonResponse
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'name' => 'required|string',
            'email' => 'required|email',
            'comment' => 'required|string',
            'reply_to' => 'sometimes|numeric',
            'post_id' => 'required|numeric',
            'status' => 'required|in:pending,publish',
        ]);

        if ($validator->fails()) {
            return Response::sendError('Request not valid.', $validator->errors()->all(), 422);
        }

        $comment->update($input);
        return Response::sendResponse($comment, 'Comment updated successfully.');
    }
}
