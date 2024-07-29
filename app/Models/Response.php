<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Response extends Model
{
    /**
     * Send a response with the given result and message.
     *
     * @param mixed $result The result to be sent in the response.
     * @param string $message The message to be sent in the response.
     * @return \Illuminate\Http\JsonResponse The JSON response containing the result and message.
     */
    public static function sendResponse($result, $message)
    {
        $response = [
            'status' => 'ok',
            'data'    => $result,
            'message' => $message,
        ];

        return response()->json($response, 200);
    }

    /**
     * Send an error response.
     *
     * @param string $error The error message.
     * @param array $errorMessages Additional error messages (optional).
     * @param int $code The HTTP status code (default: 404).
     * @return \Illuminate\Http\JsonResponse
     */
    public static function sendError($error, $errorMessages = [], $code = 404)
    {
        $response = [
            'status' => 'error',
            'message' => $error,
        ];

        if (!empty($errorMessages)) {
            $response['data'] = $errorMessages;
        }

        return response()->json($response, $code);
    }
}
