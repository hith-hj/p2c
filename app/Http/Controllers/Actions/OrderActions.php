<?php

namespace App\Http\Controllers\Actions;

use App\ExceptionHandler;
use App\Models\V1\User;

class OrderActions
{
    use ExceptionHandler;

    public function __construct() {}

    public function all(?int $id = null)
    {
        $this->Required($id, 'User ID');
        $user = User::find($id);
        $this->NotFound($user, 'User');
        $this->NotFound($user->orders, 'Orders');

        return $user->orders;
    }

}
