<?php

namespace App;

trait ApiRes
{
    public function success($payload = [], $msg = 'Success', $code = 200)
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

    public function error($payload = [], $msg = 'Error', $code = 400)
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
