<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\Account;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AuthenticationController extends Controller
{

    /**
     * Register user
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request) {

        $validated = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => [ 'required', 'string', 'email', 'max:255', Rule::unique(User::class),],
            'password' => ['required','min:8'],
        ]);

        if ($validated->fails()) {
            return response([
                'message' => 'Validation failed',
                'errors' => $validated->errors()
            ], Response::HTTP_BAD_REQUEST);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        Account::create([
            'user_id' => $user->id,
            'balance' => "0.00"
        ]);

        $token = $user->createToken('myAppToken');

        return (new UserResource($user))->additional([
            'message' => 'success',
            'token' => $token->plainTextToken,
        ]);
    }

    /**
     * Login user and return token
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required'],
        ]);

        if ($validated->fails()) {
            return response(['errors' => $validated->errors()], Response::HTTP_BAD_REQUEST);
        }

        if (!auth()->attempt($request->only('email', 'password'))) {
            return response(['message' => 'Invalid credentials'], 401);
        }

        $user = auth()->user();

        return (new UserResource($user))->additional([
            'message' => 'success',
            'token' => $user->createToken('myAppToken')->plainTextToken,
        ]);
    }

    /**
     * Logout the user and delete their personal access token.
     *
     * @param Request $request
     * @return \Illumina    te\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Successfully logged out']);
    }
}
