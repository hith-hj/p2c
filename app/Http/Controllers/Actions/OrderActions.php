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
        $this->Required($id, __('main.user').' ID' );
        $user = User::find($id);
        $this->NotFound($user, __('main.user') );
        $this->NotFound($user->orders, __('main.orders') );

        return $user->orders;
    }

}
