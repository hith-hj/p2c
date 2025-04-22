<?php

declare(strict_types=1);

namespace App\Http\Controllers\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\UserResource;
use App\Models\V1\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class JWTAuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['required', 'regex:/^09[1-9]{1}\d{7}$/', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'account_type' => ['required', Rule::in(['producer', 'carrier'])],
            'firebase_token' => ['required'],
        ]);

        if ($validator->fails()) {
            return $this->error(payload: ['errors' => $validator->errors()]);
        }

        $user = User::create([
            'email' => $validator->safe()->input('email'),
            'phone' => $validator->safe()->input('phone'),
            'password' => Hash::make($validator->safe()->input('password')),
            'role' => $validator->safe()->input('account_type'),
            'firebase_token' => $validator->safe()->input('firebase') ?? null,
        ]);

        try {
            $by = $validator->safe()->input('by') ?? 'phone';
            $user->sendVerificationCode($by);

            return $this->success(
                payload: ['code' => $user->code('verification')?->code],
                msg: __('main.registerd'),
                code: 201
            );
        } catch (\Exception $e) {
            return $this->error(msg: $e->getMessage());
        }
    }

    public function verify(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => ['required', 'regex:/^09[1-9]{1}\d{7}$/', 'exists:users'],
            'code' => ['required', 'numeric', 'exists:codes,code'],
        ]);

        if ($validator->fails()) {
            return $this->error(payload: ['errors' => $validator->errors()]);
        }

        $user = User::where('phone', $validator->safe()->input('phone'))->first();
        if ($user->code('verification')->code !== $validator->safe()->integer('code')) {
            return $this->error(msg: __('main.invalid code'));
        }

        try {
            $user->verify();

            return $this->success(msg: __('main.verified'));
        } catch (\Exception $e) {
            return $this->error(msg: $e->getMessage());
        }
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => ['required', 'regex:/^09[1-9]{1}\d{7}$/', 'exists:users'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        if ($validator->fails()) {
            return $this->error(payload: ['errors' => $validator->errors()]);
        }

        $credentials = $request->only('phone', 'password');
        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return $this->error(msg: __('main.invalid credentials'));
            }

            $user = auth()->user();
            if ($user->verified_at === null || $user->verification_code !== null) {
                return $this->error(msg: __('main.unverified'), code: 401);
            }

            $token = JWTAuth::claims(['role' => $user->role])->fromUser($user);
            $user = UserResource::make($user);

            return $this->success(payload: ['user' => $user, 'token' => $token]);
        } catch (JWTException) {
            return $this->error(msg: __('main.token error 1'), code: 500);
        }
    }

    public function refreshToken()
    {
        try {
            return $this->success(payload: ['token' => Auth::refresh()]);
        } catch (\Exception $exception) {
            return $this->error(payload: ['errors' => $exception->getMessage().' Login again']);
        }
    }

    public function getUser()
    {
        try {
            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return $this->error(msg: 'User not found', code: 404);
            }
        } catch (JWTException) {
            return $this->error(msg: __('main.invalid token'), code: 400);
        }

        return $this->success(payload: ['user' => UserResource::make($user)]);
    }

    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());

        return $this->success(msg: __('main.logout'));
    }

    public function forgetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => ['required', 'regex:/^09[1-9]{1}\d{7}$/', 'exists:users'],
        ]);

        if ($validator->fails()) {
            return $this->error(payload: ['errors' => $validator->errors()]);
        }

        $user = User::where('phone', $validator->safe()->input('phone'))->first();

        // if there is a previous code from registeration or other what to do

        $user->sendVerificationCode();

        return $this->success(
            payload: ['code' => $user->verification_code],
            msg: __('main.code sent'),
            code: 201
        );
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => ['required', 'regex:/^09[1-9]{1}\d{7}$/', 'exists:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if ($validator->fails()) {
            return $this->error(payload: ['errors' => $validator->errors()]);
        }

        $user = User::where('phone', $validator->safe()->input('phone'))->first();

        if (is_null($user->verified_at)) {
            return $this->error(msg: __('main.verify account'), code: 401);
        }

        $user->update(['password' => Hash::make($validator->safe()->input('password'))]);

        return $this->success(msg: __('main.updated'));
    }

    public function resendCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => ['required', 'regex:/^09[1-9]{1}\d{7}$/', 'exists:users'],
        ]);

        if ($validator->fails()) {
            return $this->error(payload: ['errors' => $validator->errors()]);
        }

        $user = User::where('phone', $validator->safe()->input('phone'))->first();

        if (is_null($user->verification_code)) {
            return $this->error(msg: __('main.invalid operation'), code: 403);
        }

        $user->sendVerificationCode();

        return $this->success(
            payload: ['code' => $user->verification_code],
            msg: __('main.code sent')
        );
    }

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password' => ['required', 'string', 'min:8'],
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if ($validator->fails()) {
            return $this->error(payload: ['errors' => $validator->errors()]);
        }

        $user = auth()->user();
        if (! Hash::check($validator->safe()->input('old_password'), $user->password)) {
            return $this->error(msg: __('main.invalid password'));
        }

        if ($user->password === Hash::make($validator->safe()->input('new_password'))) {
            return $this->error(msg: __('main.passwords are equals'));
        }

        $user->update(['password' => Hash::make($validator->safe()->input('new_password'))]);

        return $this->success(msg: __('main.updated'));
    }

    public function deleteUser()
    {
        $user = auth()->user();
        JWTAuth::invalidate(JWTAuth::getToken());
        $user->badge()?->delete();
        $user->delete();

        return $this->success(msg: __('main.deleted'));
    }
}
