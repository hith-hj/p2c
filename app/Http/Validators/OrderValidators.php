<?php

declare(strict_types=1);

namespace App\Http\Validators;

use App\Enums\OrderDeliveryTypes;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class OrderValidators
{
    public static function find($data)
    {
        return Validator::make($data, [
            'order_id' => ['required', 'exists:orders,id'],
        ]);
    }

    public static function checkCost($data)
    {
        return Validator::make($data, [
            'branch_id' => ['required', 'exists:branches,id'],
            'delivery_type' => ['required', 'string', Rule::in(OrderDeliveryTypes::cases())],
            'weight' => ['required', 'numeric', 'min:1', 'max:5000'],
            'dest_long' => ['required', 'regex:/^[-]?((((1[0-7]\d)|(\d?\d))\.(\d+))|180(\.0+)?)$/'],
            'dest_lat' => ['required', 'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
            'attrs' => ['sometimes', 'array', 'max:5'],
            'attrs.*' => ['required', 'exists:attrs,id'],
            'items' => ['sometimes', 'array', 'max:5'],
            'items.*' => ['required', 'exists:items,id'],
        ]);
    }

    public static function create($data)
    {
        return Validator::make($data, [
            'branch_id' => ['required', 'exists:branches,id'],
            'customer_name' => ['required', 'string', 'max:30'],
            'customer_phone' => ['required', 'regex:/^09[1-9]{1}\d{7}$/'],
            'delivery_type' => ['required', Rule::in(OrderDeliveryTypes::cases())],
            'dest_long' => ['required', 'regex:/^[-]?((((1[0-7]\d)|(\d?\d))\.(\d+))|180(\.0+)?)$/'],
            'dest_lat' => ['required', 'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
            'distance' => ['required', 'numeric', 'min:200', 'max:50000'],
            'weight' => ['required', 'numeric', 'min:1', 'max:5000'],
            'goods_price' => ['required', 'numeric'],
            'cost' => ['required', 'numeric'],
            'attrs' => ['sometimes', 'array', 'max:5'],
            'attrs.*' => ['required', 'exists:attrs,id'],
            'items' => ['sometimes', 'array', 'max:5'],
            'items.*' => ['required', 'exists:items,id'],
            'note' => ['sometimes', 'string', 'max:200'],
        ]);
    }

    public static function accept($data)
    {
        return Validator::make($data, [
            'order_id' => ['required', 'exists:orders,id'],
        ]);
    }

    public static function picked($data)
    {
        return Validator::make($data, [
            'order_id' => ['required', 'exists:orders,id'],
        ]);
    }

    public static function delivered($data)
    {
        return Validator::make($data, [
            'order_id' => ['required', 'exists:orders,id'],
            'code' => ['required', 'exists:codes,code'],
        ]);
    }

    public static function finish($data)
    {
        return Validator::make($data, [
            'order_id' => ['required', 'exists:orders,id'],
        ]);
    }

    public static function cancel($data)
    {
        return Validator::make($data, [
            'order_id' => ['required', 'exists:orders,id'],
        ]);
    }

    public static function forceCancel($data)
    {
        return Validator::make($data, [
            'order_id' => ['required', 'exists:orders,id'],
        ]);
    }

    public static function reject($data)
    {
        return Validator::make($data, [
            'order_id' => ['required', 'exists:orders,id'],
        ]);
    }
}
