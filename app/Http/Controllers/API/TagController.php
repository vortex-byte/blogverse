<?php

namespace App\Http\Controllers\API;

use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\API\BaseController;

class TagController extends BaseController
{
    /**
     * Retrieve all tags.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        $tags = Tag::all();
        return $this->sendResponse($tags, 'Tags retrieved successfully.');
    }

    /**
     * Find posts from specific tag.
     *
     * @param  string  $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $slug): JsonResponse
    {
        return Tag::show($request, $slug);
    }
}
