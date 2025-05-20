<?php

declare(strict_types=1);

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Services\ReviewServices;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
	public function __construct(public ReviewServices $service) {}

	public function all(Request $request): JsonResponse
	{
		$request->all();
	    return Success();
	}

	public function create(Request $request)
	{
	    $validator = Validator::make($request->all(),[
	    	'object_id'=>['required','numeric',],
	    	'object_type'=>['required','string',],
	    	'content'=>['nullable','string','max:700',],
	    	'rate'=>['required','numeric','min:0','max:10',],
	    ]);
	    $object_id = $validator->safe()->integer('object_id');
	    $object_type = $validator->safe()->string('object_type');
	    if(!class_exists((string)$object_type)){
	    	return Error(msg:'invalid object type');
	    }

	    $object = $object_type::find($object_id);
	    if(!$object){

	    }
	    $this->service->create($object,$validator->safe()->only(['content','rate']));

	    return Success(msg:'review created');
	}

}
