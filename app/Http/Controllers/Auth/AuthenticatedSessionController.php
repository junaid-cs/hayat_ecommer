<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        return $this->authenticated($request);

    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }


    protected function authenticated(Request $request)
    {
        if(Auth::user()->role !== '0') //1 = Admin Login
        {
            return redirect()->route('Admin.Dashboard')->with('status', 'Welcome to your dashboard');
        }
        elseif(Auth::user()->role == '0') // Normal or Default User Login
        {
            return redirect('/')->with('status','Logged in successfully');
        }
        else{
            return redirect('/')->with('status','Logged in successfully');

        }
       
    }
}
