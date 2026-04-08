<?php

namespace App\Http\Responses;

class ApiResponse
{
    public static function error(string $message = 'Validation error', int $code = 422, mixed $errors = null)
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
    public static function success(string $message = null, int $code = 200, mixed $data = null)
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
