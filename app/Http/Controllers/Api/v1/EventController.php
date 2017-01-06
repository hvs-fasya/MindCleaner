<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use Validator;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Event;
use App\EventType;
use App\Sphere;
use DB;

class EventController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    public function add_event_remote(Request $request)
    {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return response()->json(['user_not_found'], 404);
        }
        $validator = Validator::make($request->only('description'), [
            'description' => ['required','max:255',Rule::unique('event_types')->where(function ($query) use($user) {
                $query->whereNull('user_id')->orWhere('user_id', $user->id);
            })],
        ]);
        if ($validator->fails()) {

            return response()->json(['error' => $validator->errors()->messages()], 422);
        }
        try{
            $event_type = new EventType();
            $event_type->description = $request->description;
            $event_type->user_id = $user->id;
            $event_type->save();
            $result = array(
                'id' => $event_type->id,
                'description' => $event_type->description,
                'common' => false,
            );
        } catch (\Exception $e) {
            // something went wrong
            return response()->json(['error' => 'could_not_add_event_type'], 500);
        }
        return response()->json(['event_type' => $result]);
    }
}