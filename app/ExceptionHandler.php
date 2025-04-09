<?php

namespace App;

use Exception;

trait ExceptionHandler
{
    /**
     * check if argument exists
     * if true throw an exception
     *
     * @param  mixed  $argument
     * @param  mixed  $name
     */
    private function Exists($argument, $name = '')
    {
        if ($argument) {
            throw new Exception("$name ".__('main.exists'), 400);
        }
    }

    /**
     * check if argument is empty
     * if true throw not found exception
     *
     * @param  mixed  $argument
     * @param  mixed  $name
     */
    private function NotFound($argument, $name = '')
    {
        return $this->empty($argument, $name, __('main.not found'));
    }

    /**
     * check if argument is empty
     * if true throw required exception
     *
     * @param  mixed  $argument
     * @param  mixed  $name
     */
    private function Required($argument, $name = '')
    {
        return $this->empty($argument, $name, __('main.is required'));
    }

    private function empty($argument, $name = '', $msg = 'Error')
    {
        if (! $argument || $argument === null || empty($argument)) {
            throw new Exception("$name $msg");
        }
    }
}
