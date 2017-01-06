<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
        $validator = Validator::make($request->only('description','selected','sphere_description','event_type_descriptions'), [
//            'description' => ['required','string',Rule::unique('events')->where(function ($query) use($user) {
//                $query->whereNull('user_id')->orWhere('user_id', $user->id);
//            })],
            'selected' => 'boolean|nullable',
            'sphere_description' => 'string|nullable',
            'event_type_descriptions' => 'array|nullable'
        ]);
        if ($validator->fails()) {

            return response()->json(['error' => $validator->errors()->messages()], 422);
        }
        $event_type_descriptions = $request->input('event_type_descriptions',null);
        $sphere_description = $request->input('sphere_description', null);
        $description = $request->description;
        $selected = $request->input('selected', false);
        $responce = array();
        $event = new Event();
        //try to attach sphere if is set
        if(!is_null($sphere_description)){
            try{
                $sphere = DB::table('spheres')->select('id')
                    ->where('user_id',$user->id)
                    ->orWhere('user_id',null)
                    ->where('description', $sphere_description)
                    ->first();
                if(is_null($sphere)){
                    $responce['sphere_warning'] = 'could_not_attach_sphere';
                } else {
                    $event->sphere_id = $sphere->id;
                }
            } catch (\Exception $e) {
                // something went wrong
                Log::error($e->getMessage());
                $responce['sphere_warning'] = 'could_not_attach_sphere';
            }
        }
        //try to save new event
        try {
            $event->description = $description;
            $event->user_id = $user->id;
            $event->selected = $selected;
            $event->save();
            $responce['event'] = array(
                'id' => $event->id,
                'description' => $event->description,
                'selected' => $event->selected,
                'sphere_id' => $event->sphere_id,
            );
        } catch (\Exception $e) {
            // something went wrong
            Log::error($e->getMessage());
            $responce['error'] = 'could_not_add_event';
            return response()->json($responce, 500);
        }
        //try to attach event_types if are set
        if(!is_null($event_type_descriptions) && !empty($event_type_descriptions)){
            $event_event_type = array();
            foreach($event_type_descriptions as $descr) {
                $event_type = DB::table('event_types')->select('id')
                    ->where('user_id',$user->id)
                    ->orWhere('user_id', null)
                    ->where('description', $descr)
                    ->first();
                if (is_null($event_type)) {
                    $responce['event_type_warning'] = 'could_not_attach_event_type ' . $descr;
                } else {
                    $event_types[] = ['id' => $event_type->id, 'description' => $descr];
                    $event_event_type[] = ['event_id'=>$event->id, 'event_type_id'=>$event_type->id];
                }
            }
            try {
                DB::table('event_event_type')->insert($event_event_type);
                $responce['event_types'] = $event_types;
            } catch (\Exception $e) {
                    // something went wrong
                Log::error($e->getMessage());
                $responce['event_type_warning'] = 'could_not_attach_event_types';
            }
        }
        return response()->json($responce, 200);
    }
}