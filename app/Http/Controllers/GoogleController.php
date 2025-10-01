<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $user = Socialite::driver('google')->stateless()->user();
            dd($user);
            $finduser = User::where('google_id', $user->id)->first();

            if ($finduser) {
                //  dd($finduser);
                Auth::login($finduser);

                return redirect('/');

            } else {
                $newUser = User::updateOrCreate(['email' => $user->email], [
                    'name'      => $user->name,
                    'email'     => $user->email,
                    'google_id' => $user->id,
                    'password'  => Hash::make('123456dummy'),
                ]);

                Auth::login($newUser);

                return redirect('/');
            }

        } catch (Exception $e) {
            dd($e->getMessage());
        }
    }

    public function handleGoogleApi(Request $request)
    {
        try {
            $googleToken = $request->input('token');
            $googleUser  = Socialite::driver('google')->stateless()->userFromToken($googleToken);
            $findUser    = User::where('google_id', $googleUser->id)->orWhere('email', $googleUser->email)->first();
            if ($findUser) {
                Auth::login($findUser);
                $success['token']       = $findUser->createToken('MyApp')->accessToken;
                $success['Information'] = $findUser;
                $response               = [
                    'success' => true,
                    'data'    => $success,
                    'message' => 'User Login Successfully',
                ];
                return response()->json($response, 200);
            } else {

                $newUser = User::updateOrCreate(['email' => $googleUser->email], [
                    'name'      => $googleUser->name,
                    'google_id' => $googleUser->id,
                    'password'  => Hash::make('123456dummy'),
                ]);
                Auth::login($newUser);
                $success['token']       = $newUser->createToken('MyApp')->accessToken;
                $success['Information'] = $newUser;
                $response               = [
                    'success' => true,
                    'data'    => $success,
                    'message' => 'User Login Successfully',
                ];
                return response()->json($response, 200);

            }

        } catch (Exception $e) {
            dd($e->getMessage());
        }
    }

}
