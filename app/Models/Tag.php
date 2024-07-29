<?php

namespace App\Models;

use App\Models\Response;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Http\JsonResponse;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
    ];

    /**
     * Get the posts associated with the tag.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function posts()
    {
        return $this->belongsToMany(Post::class);
    }

    /**
     * Show the tag with the given slug.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public static function show(Request $request, $slug): JsonResponse
    {
        $tag = Tag::where('slug', $slug)->first();

        if (is_null($tag)) {
            return Response::sendError('Tag not found.');
        }

        $limit = 10;
        if ($request->filled('limit')) $limit = $request->input('limit');

        $posts = $tag->posts()->paginate($limit);

        return Response::sendResponse($posts, 'Post from specified author retrieved successfully.');
    }
}
