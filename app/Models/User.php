<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\Response;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the posts associated with the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    /**
     * Register a new user.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public static function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
            'confirm_password' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return Response::sendError('User not valid.', $validator->errors()->all(), 422);
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);

        $success = [
            'token' => $user->createToken('auth-token')->plainTextToken,
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ];

        return Response::sendResponse($success, 'User register successfully.');
    }

    /**
     * Login a user.
     *
     * @param Request $request The login request.
     * @return JsonResponse The JSON response containing the login result.
     */
    public static function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return Response::sendError('User not valid.', $validator->errors()->all(), 422);
        }

        if (auth()->attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = auth()->user();
            $success = [
                'token' => $user->createToken('auth-token')->plainTextToken,
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ];

            return Response::sendResponse($success, 'User login successfully.');
        } else {
            return Response::sendError('Unauthorized.', ['error' => 'Unauthorized'], 401);
        }
    }

    /**
     * Retrieve a user by their ID.
     *
     * @param int $id The ID of the user.
     * @return \Illuminate\Http\JsonResponse The JSON response containing the user data.
     */
    public static function user($id): JsonResponse
    {
        $user = User::find($id);

        if (is_null($user)) {
            return Response::sendError('User not found.');
        }

        return Response::sendResponse($user, 'User retrieved successfully.');
    }

    /**
     * Retrieve the author posts with the given user ID.
     *
     * @param  Request  $request  The HTTP request object.
     * @param  int  $id  The ID of the user.
     * @return JsonResponse  The JSON response containing the author information.
     */
    public static function author(Request $request, $id): JsonResponse
    {
        $author = User::find($id);

        if (is_null($author)) {
            return Response::sendError('Author not found.');
        }

        $limit = 10;
        if ($request->filled('limit')) $limit = $request->input('limit');

        $posts = $author->posts->paginate($limit);

        return Response::sendResponse($posts, 'Post from specified author retrieved successfully.');
    }
}
