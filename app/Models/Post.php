<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Post extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'status',
        'user_id',
    ];

    /**
     * Get the user that owns the post.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the tags associated with the post.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    /**
     * Get the comments for the post.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Add tags to the post.
     *
     * @param array $tags An array of tags to be added.
     * @return array The updated array of tags ID.
     */
    public function addTags(array $tags): array
    {
        $ids = [];
        foreach ($tags as $tag) {
            $t = Tag::where('name', $tag)->firstOrCreate([
                'name' => $tag,
                'slug' => Str::slug($tag),
            ]);

            $ids[] = $t->id;
        }

        return $ids;
    }

    private static function createSlug($title)
    {
        $slug = Str::slug($title);
        $post = self::where('slug', $slug)->firstOrFail();

        if (!$post) return $slug;

        $inc = 2;
        while (true) {
            $check = self::where('slug', "$slug-$inc")->first();
            if (!$check) {
                $slug = "$slug-$inc";
                break;
            };

            $inc++;
        }

        return $slug;
    }

    /**
     * Retrieve all posts.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public static function getAllPosts(Request $request): JsonResponse
    {
        $limit = 10;
        if ($request->filled('limit')) $limit = $request->input('limit');

        $posts = Post::with('tags')->paginate($limit);
        return Response::sendResponse($posts, 'Posts retrieved successfully.');
    }

    /**
     * Store a new post.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public static function store(Request $request): JsonResponse
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'title' => 'required|string',
            'slug' => 'sometimes|string',
            'content' => 'required|string',
            'tags' => 'required|array',
            'status' => 'required|in:publish,draft',
        ]);

        if ($validator->fails()) {
            return Response::sendError('Request not valid.', $validator->errors()->all(), 422);
        }

        if (!$request->filled('slug')) {
            $input['slug'] = self::createSlug($input['title']);
        } else {
            $slug = Str::slug($input['slug']);
            $check = Post::where('slug', $slug)->first();
            if ($check) return Response::sendError('Request not valid.', ['Slug already exists.'], 422);

            $input['slug'] = $slug;
        }

        $input['user_id'] = $request->user()->id;

        $post = Post::create($input);
        $post->tags()->attach($post->addTags($input['tags']));

        return Response::sendResponse($post->fresh('tags'), 'Post created successfully.');
    }

    /**
     * Display the specified post.
     *
     * @param string $slug The slug of the post.
     * @return \Illuminate\Http\JsonResponse The JSON response containing the post data.
     */
    public static function show($slug): JsonResponse
    {
        $post = Post::where('slug', $slug)->with(['tags', 'comments'])->first();

        if (is_null($post)) {
            return Response::sendError('Post not found.');
        }

        return Response::sendResponse($post, 'Post retrieved successfully.');
    }

    /**
     * Search for posts based on the given request.
     *
     * @param  Request  $request  The request object containing search parameters.
     * @return JsonResponse  The JSON response containing the search results.
     */
    public static function search(Request $request): JsonResponse
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'query' => 'required|string',
        ]);

        if ($validator->fails()) {
            return Response::sendError('Request not valid.', $validator->errors()->all(), 422);
        }

        $posts = Post::where('title', 'like', '%' . $input['query'] . '%')->with('tags')->get();

        return Response::sendResponse($posts, 'Posts retrieved successfully.');
    }

    /**
     * Update a post.
     *
     * @param  Request  $request  The request object containing the updated post data.
     * @param  Post  $post  The post object to be updated.
     * @return JsonResponse  The JSON response indicating the success or failure of the update operation.
     */
    public static function updatePost(Request $request, Post $post): JsonResponse
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'title' => 'required|string',
            'slug' => 'sometimes|string',
            'content' => 'required|string',
            'tags' => 'required|array',
            'status' => 'required|in:publish,draft',
        ]);

        if ($validator->fails()) {
            return Response::sendError('Request not valid.', $validator->errors()->all());
        }

        if (!$request->filled('slug') && $input['title'] != $post->title) {
            $input['slug'] = self::createSlug($input['title']);
        }

        if ($request->filled('slug')) {
            if ($input['slug'] == $post->slug) {
                $input['slug'] = $post->slug;
            } else {
                $slug = Str::slug($input['slug']);
                $check = Post::where('slug', $slug)->first();
                if ($check) return Response::sendError('Request not valid.', ['Slug already exists.'], 422);

                $input['slug'] = $slug;
            }
        }

        $post->update($input);
        $post->tags()->sync($post->addTags($input['tags']));

        return Response::sendResponse($post->fresh('tags'), 'Post updated successfully.');
    }
}
