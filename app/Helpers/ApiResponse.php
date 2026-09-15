<?php

// Helper class untuk format respon API JSON

namespace App\Helpers; // file ini berada di folder app\Helpers

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;

class ApiResponse
{
    // respon success
    public static function success (
        JSONResource|array|null $data = null, // data yang dikirim
        string $message = 'Success', // pesan success
        int $status = Response::HTTP_OK // kode http status
    ){
        // respon yang dikembalikan
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data // ambil dari JSONResource|array|null $data = null
        ], $status);
    }

    // error
    public static function error (
        string $message,
        int $status = Response::HTTP_BAD_REQUEST,
        array $errors = []
    ){
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => $errors,
        ], $status);
    }
}
