<?php

namespace App\Http\Controllers;

use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
       /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        // $users = User::all();
        // return response(['data'=>$users]);
        return User::all();
    }

    //
    public function register(Request $request)
    {

        $rules = [
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|confirmed'
        ];

        // $fields = $request->validate([
        //     'name' => 'required|string',
        //     'email' => 'required|string|email|unique:users',
        //     'password' => 'required|string|confirmed'
        // ]);

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response([
                'responseCode' => 422,
                'errors'=>$validator->errors()
            ]);
        }else {
            $user = User::create([
                'name' => $request['name'],
                'email' => $request['email'],
                'password' => bcrypt($request['password'])
            ]);

            $token = $user->createToken('authToken')->plainTextToken;

            return response(['status'=>'success','user' => $user, 'token' => $token], Response::HTTP_CREATED);
        }


    }

    public function login(Request $request)
    {
        $fields = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string'
        ]);

        $user = User::where('email', $fields['email'])->first();

        if (!$user || !Hash::check($fields['password'], $user->password)) {
            return response(['status'=>'failure','message' => 'Invalid credentials'], Response::HTTP_UNAUTHORIZED);
        }


        $token = $user->createToken('authToken')->plainTextToken;

        return response(['status'=>'success','user' => $user, 'token' => $token], Response::HTTP_CREATED);
    }

    public function logout(Request $request)
    {
        auth()->user()->tokens->each(function ($token, $key) {
            $token->delete();
        });

        // auth()->user()->tokens->delete();

        return response(['status'=>'success','message' => 'Successfully logged out']);
    }



    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        return User::find($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $fields = $request->validate([
            'name' => 'string',
            'email' => 'string|email',
            'password' => 'string'
        ]);


        $user = User::find($id);
        $user->update([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => bcrypt($fields['password'])
        ]);

        // $user->update($request->all());

        return response(['status'=>'success','user' => $user]);

        // return $user;

        // return Product::find($id)->update($request->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $res = User::destroy($id);
        return response(['status'=>'success','message' => 'User deleted successfully.', 'serverResponse'=> $res]);
    }

    /**
     * Search the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function search($name)
    {
        //
        return User::where('name', 'like', '%' . $name . '%')->get();
    }
}
