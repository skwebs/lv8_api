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
            'mobile' => 'nullable|string|size:10',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|min:8|max:20|string|confirmed'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response([
                'error' => true,
                'errors' => $validator->errors()
            ]);
        } else {
            $user = User::create([
                'name' => $request['name'],
                'mobile' => $request['mobile'],
                'email' => $request['email'],
                'password' => bcrypt($request['password'])
            ]);

            $token = $user->createToken('authToken')->plainTextToken;

            return response(['error' => false, 'user' => $user, 'token' => $token], Response::HTTP_CREATED);
        }
    }

    public function login(Request $request)
    {

        $rules = [
            'email' => 'required|string|email|exists:users',
            'password' => 'required|min:8|max:20|string'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response([
                'error' => true,
                'errors' => $validator->errors()
            ], 401);
        } else {
            $user = User::where('email', $request['email'])->first();

            if (!$user || !Hash::check($request['password'], $user->password)) {
                return response()->json(['error' => true, 'message' => 'Invalid credentials']);
            }


            $token = $user->createToken('authToken')->plainTextToken;

            return response(['error' => false, 'user' => $user, 'token' => $token], Response::HTTP_CREATED);
        }

    }

    public function logout(Request $request)
    {
        auth()->user()->tokens->each(function ($token, $key) {
            $token->delete();
        });

        // auth()->user()->tokens->delete();

        return response(['error' => false, 'message' => 'Successfully logged out']);
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
            'mobile' => 'string|size:10',
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

        return response(['error' => false, 'user' => $user]);

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
        return response(['error' => false, 'message' => 'User deleted successfully.', 'serverResponse' => $res]);
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
