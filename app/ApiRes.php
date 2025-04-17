<?php

declare(strict_types=1);

namespace App;

trait ApiRes
{
    public function success(array $payload = [], string $msg = 'Success', int $code = 200)
    {
        $response = [
            'success' => true,
            'message' => $msg,
        ];
        if (! empty($payload)) {
            $response['payload'] = $payload;
        }

        return response()->json($response, $code);
    }

    public function error(array $payload = [], string $msg = 'Error', int $code = 400)
    {
        $response = [
            'error' => true,
            'message' => $msg,
        ];
        if (! empty($payload)) {
            $response['payload'] = $payload;
        }

        return response()->json($response, $code);
    }
}
