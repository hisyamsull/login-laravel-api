<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use PhpParser\Node\Stmt\TryCatch;
use App\Helpers\ResponseFormatter;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use GrahamCampbell\ResultType\Success;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function index()
    {
        try {
            $message = 'succes get data user';
            $data = UserResource::collection(User::all());
            $meta['status'] = 'succses';
            $meta['massage'] = $message;
            $response = [
                'meta' => $meta,
                'data' => $data
            ];
            return response()->json($response, 200);
        } catch (\Throwable $th) {
            Log::error('terjadi eror pada get data user', $th->getMessage());
            $meta['status'] = 'failed';
            $meta['massage'] = $th->getMessage();
            $response = [
                'meta' => $meta,
                'data' => []
            ];
            return response()->json($response, 500);
        }
    }

    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            $user = User::where('email', $request->email)->first();


            if (!$user || !Hash::check($request->password, $user->password)) {
                $meta['status'] = 'failed';
                $meta['massage'] = 'The provided credentials are incorrect.';
                $response = [
                    'meta' => $meta,
                    'data' => []
                ];
                return response()->json($response, 400);
            }
            $meta['status'] = 'succses';
            $meta['massage'] = 'login succsesfuly';
            $data['token'] = $user->createToken($request->email)->plainTextToken;
            $response = [
                'meta' => $meta,
                'data' => $data,
            ];
            return response()->json($response, 201);
        } catch (\Throwable $th) {
            Log::error('terjadi eror pada login', $th->getMessage());
            $meta['status'] = 'failed';
            $meta['massage'] = $th->getMessage();
            $response = [
                'meta' => $meta,
                'data' => []
            ];
            return response()->json($response, 500);
        }
    }
    public function create(Request $request)
    {
        try {
            $rules = [
                'name' => 'required',
                'email' => 'required|email',
                'password' => 'required',
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json([
                    'meta' => ['status' => 'failed', 'message' => 'Bad request',],
                    'data' => $validator->errors()
                ], 400);
            }
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);
            $data['token'] = $user->createToken($request->email)->plainTextToken;
            $data['user'] = $user;
            $meta['status'] = 'succses';
            $meta['massage'] = 'User is created successfully.';
            $response = [
                'meta' => $meta,
                'data' => $data,
            ];

            return response()->json($response, 201);
        } catch (\Throwable $th) {
            Log::error('terjadi eror pada create user', $th->getMessage());
            $meta['status'] = 'failed';
            $meta['massage'] = $th->getMessage();
            $response = [
                'meta' => $meta,
                'data' => []
            ];
            return response()->json($response, 500);
        }
    }
    public function logout(Request $request)
    {
        try {
            $meta['status'] = 'succses';
            $meta['massage'] = 'log out.';
            $response = [
                'meta' => $meta,
                'data' => []
            ];
            $request->user()->currentAccessToken()->delete();
            return response()->json($response, 201);
        } catch (\Throwable $th) {
            Log::error('terjadi eror pada log out', $th->getMessage());
            $meta['status'] = 'failed';
            $meta['massage'] = $th->getMessage();
            $response = [
                'meta' => $meta,
                'data' => []
            ];
            return response()->json($response, 500);
        }
    }
    public function destroy(Request $request)
    {
        User::where('name', $request->name)->delete();
        $meta['status code'] = 200;
        $meta['massage'] = "succes delete data";
        $data = null;
        $response = [
            'meta' => $meta,
            'data' => $data
        ];
        return response()->json($response, 200);
    }
    public function trash()
    {
        $data = UserResource::collection(User::onlyTrashed()->get());
        $meta['status code'] = 200;
        $meta['massage'] = "trashed data";
        $response = [
            'meta' => $meta,
            'data' => $data
        ];
        return response()->json($response, 200);
    }
    public function restore(Request $request)
    {
        User::where('name', $request->name)->withTrashed()->restore();
        $meta['status code'] = 200;
        $meta['massage'] = "succes resotre data";
        $response = [
            'meta' => $meta,
            'data' => User::where('name', $request->name)->get()
        ];
        $email = Config::get('global.cek');
        dd($email);
        return response()->json($response, 200);
    }
}
