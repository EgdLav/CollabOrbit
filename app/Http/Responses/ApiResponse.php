<?php

namespace App\Http\Responses;

class ApiResponse
{
    public static function error(string $message, int $code, mixed $errors)
    {
        $data = [
            'message' => $message
        ];
        if ($errors) {
            $data['errors'] = $errors;
        }
        return response()->json($data, $code);
    }
}
