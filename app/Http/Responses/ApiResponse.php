<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    public static function error(string $message = 'Validation error', int $code = 422, mixed $errors = null): JsonResponse
    {
        $json = [
            'success' => false,
            'message' => $message
        ];
        if ($errors) {
            $json['errors'] = $errors;
        }
        return response()->json($json, $code);
    }
    public static function success(string $message = 'Success', int $code = 200, mixed $data = null): JsonResponse
    {

        $json = [
            'success' => true,
        ];
        if ($message) {
            $json['message'] = $message;
        }
        if ($data) {
            $json['data'] = $data;
        }
        return response()->json($json, $code);
    }
}
