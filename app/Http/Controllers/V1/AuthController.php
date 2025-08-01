<?php

declare(strict_types=1);

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\UserResource;
use App\Http\Validators\AuthValidators;
use App\Models\V1\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

final class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = AuthValidators::register($request->all());
        $user = User::create([
            'email' => $validator->safe()->input('email'),
            'phone' => $validator->safe()->input('phone'),
            'password' => Hash::make($validator->safe()->input('password')),
            'role' => $validator->safe()->input('account_type'),
            'firebase_token' => $validator->safe()->input('firebase_token') ?? null,
        ]);

        try {
            $by = $validator->safe()->input('by') ?? 'phone';
            $user->sendVerificationCode($by);

            return Success(
                msg: __('main.registerd'),
                code: 201
            );
        } catch (Exception $e) {
            return Error(msg: $e->getMessage());
        }
    }

    public function verify(Request $request)
    {
        $validator = AuthValidators::verify($request->all());
        $user = User::where('phone', $validator->safe()->input('phone'))->first();
        if ($user->code('verification')->code !== $validator->safe()->integer('code')) {
            return Error(msg: __('main.invalid code'));
        }

        try {
            $user->verify();

            return Success(msg: __('main.verified'));
        } catch (Exception $e) {
            return Error(msg: $e->getMessage());
        }
    }

    public function login(Request $request)
    {
        $validator = AuthValidators::login($request->all());
        $credentials = $validator->safe()->only('phone', 'password');
        try {
            if (! Auth::attempt($credentials)) {
                return Error(msg: __('main.invalid credentials'));
            }

            $user = Auth::user();

            if (! Hash::check($validator->safe()->input('password'), $user->password)) {
                return Error(msg: __('main.incorrect password'));
            }

            if ($user->verified_at === null || $user->verification_code !== null) {
                return Error(msg: __('main.unverified'), code: 401);
            }

            $token = JWTAuth::claims(['role' => $user->role])->fromUser($user);
            $user = UserResource::make($user);

            return Success(payload: ['user' => $user, 'token' => $token]);
        } catch (JWTException) {
            return Error(msg: __('main.token error 1'), code: 500);
        }
    }

    public function refreshToken()
    {
        try {
            return Success(payload: ['token' => Auth::refresh()]);
        } catch (Exception $exception) {
            return Error(msg: $exception->getMessage().' , Login again');
        }
    }

    public function getUser()
    {
        try {
            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return Error(msg: 'User not found', code: 404);
            }
        } catch (JWTException) {
            return Error(msg: __('main.invalid token'), code: 400);
        }

        return Success(payload: ['user' => UserResource::make($user)]);
    }

    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());

        return Success(msg: __('main.logout'));
    }

    public function forgetPassword(Request $request)
    {
        $validator = AuthValidators::forgetPassword($request->all());
        $user = User::where('phone', $validator->safe()->input('phone'))->first();

        // if there is a previous code from registeration or other what to do
        $user->sendVerificationCode();

        return Success(
            msg: __('main.code sent'),
            code: 201
        );
    }

    public function resetPassword(Request $request)
    {
        $validator = AuthValidators::resetPassword($request->all());
        $user = User::where('phone', $validator->safe()->input('phone'))->first();

        if (is_null($user->verified_at)) {
            return Error(msg: __('main.verify account'), code: 401);
        }

        $user->update(['password' => Hash::make($validator->safe()->input('password'))]);

        return Success(msg: __('main.updated'));
    }

    public function resendCode(Request $request)
    {
        $validator = AuthValidators::resendCode($request->all());
        $user = User::where('phone', $validator->safe()->input('phone'))->first();

        if (is_null($user->code('verification')->code)) {
            return Error(msg: __('main.invalid operation'), code: 403);
        }

        $user->sendVerificationCode();

        return Success(
            msg: __('main.code sent')
        );
    }

    public function changePassword(Request $request)
    {
        $validator = AuthValidators::changePassword($request->all());
        $user = Auth::user();
        if (! Hash::check($validator->safe()->input('old_password'), $user->password)) {
            return Error(msg: __('main.invalid password'));
        }

        if ($user->password === Hash::make($validator->safe()->input('new_password'))) {
            return Error(msg: __('main.passwords are equals'));
        }

        $user->update(['password' => Hash::make($validator->safe()->input('new_password'))]);

        return Success(msg: __('main.updated'));
    }

    public function deleteUser()
    {
        $user = Auth::user();
        JWTAuth::invalidate(JWTAuth::getToken());
        $user->badge()?->delete();
        $user->delete();

        return Success(msg: __('main.deleted'));
    }
}
