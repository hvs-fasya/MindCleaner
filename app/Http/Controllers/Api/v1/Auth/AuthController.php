<?php

namespace App\Http\Controllers\Api\v1\Auth;

use App\User;
use Validator;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function __construct()
    {
//        $this->middleware('guest', ['except' => 'logout']);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
            'sex' => 'required|in:"f","m"',
            'phone' => 'numeric|max:32',
        ]);
    }

    public function get_access_token(Request $request)
    {
        // grab credentials from the request
        $credentials = $request->only('email', 'password');

        $validator = Validator::make($credentials, [
            'email' => 'required|email|max:255',
            'password' => 'required',
        ]);

        if ($validator->fails()) {

            return response()->json(['error' => $validator->errors()->messages()], 422);
        }

        try {
            // attempt to verify the credentials and create a token for the user
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 401);
            }
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        // all good so return the token
        return response()->json(compact('token'));
    }

    //    public function login()
//    {
//        try {
//
//            if (! $user = JWTAuth::parseToken()->authenticate()) {
//                return response()->json(['user_not_found'], 404);
//            }
//
//        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
//
//            return response()->json(['token_expired'], $e->getStatusCode());
//
//        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
//
//            return response()->json(['token_invalid'], $e->getStatusCode());
//
//        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
//
//            return response()->json(['token_absent'], $e->getStatusCode());
//
//        }
//        return response()->json($user);
//    }

    public function refresh_token()
    {
        $token = JWTAuth::getToken();

        if(!$token){
            return response()->json(['error' => 'token_absent'], 400);
        }

        try{
            $token = JWTAuth::refresh($token);

        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e){

            return response()->json(['error' => 'token_invalid'], $e->getStatusCode());
        }

        return response()->json(compact('token'));

    }

    public function logout_remote()
    {
        $token = JWTAuth::getToken();

        if(!$token){
            return response()->json(['error' => 'token_absent'], 400);
        }

        try{

            JWTAuth::invalidate($token);

        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e){

            return response()->json(['error' => 'token_invalid'], $e->getStatusCode());
        }

        return response()->json(['result' => 'success']);
    }

    public function register_remote(Request $request)
    {
        $validator = $this->validator($request->all());

        if ($validator->fails()) {

            return response()->json(['error' => $validator->errors()->messages()], 422);
        }

        try{
            $user = new User;
            $user->fill($request->all());
            $user->password = bcrypt($request->password);
            $user->save();

            $credentials = $request->only('email', 'password');

            $token = JWTAuth::attempt($credentials);

        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json(['error' => 'could_not_create_token'], 500);

        } catch (\Exception $e) {
            // something went wrong whilst user register
            return response()->json(['error' => 'could_not_register_user'], 500);
        }

        return response()->json(compact('token'));
    }

    public function update_user_remote(Request $request)
    {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }

        $validator = Validator::make($request->all(), [
            'name' => 'max:255',
            'email' => ['email','max:255',Rule::unique('users')->ignore($user->id)],
            'password' => 'min:6|confirmed',
            'sex' => 'in:"f","m"',
            'phone' => 'numeric|max:32',
        ]);

        if ($validator->fails()) {

            return response()->json(['error' => $validator->errors()->messages()], 422);
        }

        try{
            $user->fill($request->all());
            $user->password = bcrypt($request->password);
            $user->save();
        } catch (\Exception $e) {
            // something went wrong whilst user register
            return response()->json(['error' => 'could_not_update_user'], 500);
        }

        return response()->json(['result' => 'success']);
    }
}
