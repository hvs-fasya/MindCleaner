<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use Validator;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\EventType;
use DB;

class EventTypeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Show all event_types for the given user.
     *
     * @return Response
     */
    public function get_event_types_remote()
    {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }

        try{
            $event_types = DB::table('event_types')->select('id','description','user_id')
                ->whereNull('user_id')
                ->orWhere('user_id',$user->id)
                ->get();
            $result = array();
            foreach($event_types as $event_type){
                $common = is_null($event_type->user_id) ? true : false;
                $result[] = array(
                    'id' => $event_type->id,
                    'description' => $event_type->description,
                    'common' => $common
                );
            }
        } catch (\Exception $e) {
            // something went wrong
            return response()->json(['error' => 'could_not_get_event_types'], 500);
        }

        return response()->json(['event_types' => $result]);
    }

    public function add_event_type_remote(Request $request)
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
