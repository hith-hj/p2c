<?php

declare(strict_types=1);

namespace App\Traits;

trait ExceptionHandler
{
    /**
     * check if argument exists
     * if true throw an exception
     *
     * @param  mixed  $argument
     * @param  mixed  $name
     */
    private function Exists($argument, string $name = ''): void
    {
        if ($argument) {
            throw new \Exception($name.' '.__('main.exists'), 400);
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
        return $this->empty($argument, $name, 'not found');
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
        return $this->empty($argument, $name, 'is required');
    }

    private function empty($argument, $name = '', $msg = 'Error'): void
    {
        if (
            ! $argument ||
            $argument === null ||
            empty($argument) ||
            (is_countable($argument) && count($argument) === 0)
        ) {
            throw new \Exception(sprintf('%s %s', __("main.$name"), __("main.$msg")));
        }
    }

    /**
     * throw exception if the condition is true
     *
     * @param  bool  $condition
     * @param  string  $message
     * @param  mixed  $name
     */
    private function Truthy($condition, $message, ...$parameters)
    {
        if ($condition) {
            throw new \Exception(__("main.$message"), ...$parameters);
        }

        return $condition;
    }

    /**
     * throw exception if the condition is false
     *
     * @param  bool  $condition
     * @param  string  $message
     * @param  mixed  $name
     */
    private function Falsy($condition, $message, ...$parameters)
    {
        if (! $condition) {
            throw new \Exception(__("main.$message"), ...$parameters);
        }

        return $condition;
    }
}
