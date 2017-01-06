<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use Validator;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Sphere;
use DB;

class SphereController extends Controller
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
     * Show all spheres for the given user.
     *
     * @return Response
     */
    public function get_spheres_remote()
    {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return response()->json(['user_not_found'], 404);
        }
        try{
            $spheres = DB::table('spheres')->select('id','description','user_id', 'updated_at')
                ->whereNull('user_id')
                ->orWhere('user_id',$user->id)
                ->get();
            $result = array();
            foreach($spheres as $sphere){
                $common = is_null($sphere->user_id) ? true : false;
                $result[] = array(
                    'id' => $sphere->id,
                    'description' => $sphere->description,
                    'common' => $common,
                    'updated_at' => $sphere->updated_at
                );
            }
        } catch (\Exception $e) {
            // something went wrong
            return response()->json(['error' => 'could_not_get_spheres'], 500);
        }
        return response()->json(['spheres' => $result]);
    }

    public function add_sphere_remote(Request $request)
    {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return response()->json(['user_not_found'], 404);
        }
        $validator = Validator::make($request->only('description'), [
            'description' => ['required','max:255',Rule::unique('spheres')->where(function ($query) use($user) {
                $query->whereNull('user_id')->orWhere('user_id', $user->id);
            })],
        ]);
        if ($validator->fails()) {

            return response()->json(['error' => $validator->errors()->messages()], 422);
        }
        try{
            $sphere = new Sphere();
            $sphere->description = $request->description;
            $sphere->user_id = $user->id;
            $sphere->save();
            $result = array(
                'id' => $sphere->id,
                'description' => $sphere->description,
                'common' => false,
            );
        } catch (\Exception $e) {
            // something went wrong
            return response()->json(['error' => 'could_not_add_sphere'], 500);
        }
        return response()->json(['sphere' => $result]);
    }

    public function destroy_sphere_remote($id=null, $description=null)
    {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return response()->json(['user_not_found'], 404);
        }
        $input_data = array(
            'id' => $id,
            'description' => $description
        );
        $validator = Validator::make($input_data, [
            'description' => 'required|max:255',
            'id' => 'required|integer'
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->messages()], 422);
        }
        try {
            $db_spheres = DB::table('spheres')->select('id', 'user_id')
                ->where('description', $description)
                ->get();
            //не должно быть - иначе это ошибка в работе приложения
            if ($db_spheres->isEmpty()) {
                return response()->json(['error' => 'sphere_not_found'], 404);
            }
            if (!$db_spheres->where('id', $id)->where('user_id', $user->id)->isEmpty()) {
                DB::table('spheres')->where('id', $id)->delete();
                return response()->json(['result' => 'success']);
            }
            //не должно быть - иначе это ошибка в работе приложения
            if (!$db_spheres->where('id', $id)->where('user_id', null)->isEmpty()) {
                return response()->json(['error' => 'can_not_destroy_common_sphere'], 422);
            }
            //не должно быть - иначе это ошибка в работе приложения
            if (!$db_spheres->where('id', $id)->where('user_id', '<>', $user->id)->isEmpty()) {
                return response()->json(['error' => 'can_not_destroy_alien_sphere'], 422);
            }
            //не должно быть - иначе это ошибка синхронизации сервера с приложением
            if ($sphere = $db_spheres->where('id','<>', $id)->where('user_id', $user->id)->first()) {
                DB::table('spheres')->where('id', $sphere->id)->delete();
                return response()->json(['warning' => 'need_to_synchronize_spheres', 'result' => 'success']);
            }
            //не должно быть по идее, но вдруг
            return response()->json(['error' => 'could_not_destroy_sphere'], 500);
        } catch (\Exception $e) {
            // something went wrong
            return response()->json(['error' => 'could_not_destroy_sphere'], 500);
        }
    }

    public function update_sphere_remote(Request $request)
    {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return response()->json(['user_not_found'], 404);
        }
        $input_data = $request->only('id','old_description','new_description');

        $validator = Validator::make($input_data, [
            'old_description' => 'required|max:255',
            'new_description' => 'required|max:255',
            'id' => 'required|integer'
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->messages()], 422);
        }
        $id=$request->id;
        $old_description = $request->old_description;
        $new_description = $request->new_description;

        try {
            $db_spheres = DB::table('spheres')->select('id', 'user_id')
                ->where('description', $old_description)
                ->get();
            //не должно быть - иначе это ошибка в работе приложения
            if ($db_spheres->isEmpty()) {
                return response()->json(['error' => 'sphere_not_found'], 404);
            }
            if (!$db_spheres->where('id', $id)->where('user_id', $user->id)->isEmpty()) {
                DB::table('spheres')->where('id', $id)->update(['description' => $new_description]);
                return response()->json(['result' => 'success']);
            }
            //не должно быть - иначе это ошибка в работе приложения
            if (!$db_spheres->where('id', $id)->where('user_id', null)->isEmpty()) {
                return response()->json(['error' => 'can_not_update_common_sphere'], 422);
            }
            //не должно быть - иначе это ошибка в работе приложения
            if (!$db_spheres->where('id', $id)->where('user_id', '<>', $user->id)->isEmpty()) {
                return response()->json(['error' => 'can_not_update_alien_sphere'], 422);
            }
            //не должно быть - иначе это ошибка синхронизации сервера с приложением
            if ($sphere = $db_spheres->where('id','<>', $id)->where('user_id', $user->id)->first()) {
                DB::table('spheres')->where('id', $sphere->id)->update(['description' => $new_description]);
                return response()->json(['warning' => 'need_to_synchronize_spheres', 'result' => 'success']);
            }
            //не должно быть по идее, но вдруг
            return response()->json(['error' => 'could_not_update_sphere', 'warning' => 'try_to_synchronize'], 500);
        } catch (\Exception $e) {
            // something went wrong
            return response()->json(['error' => 'could_not_update_sphere'], 500);
        }
    }
}
