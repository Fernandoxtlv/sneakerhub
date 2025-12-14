<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $remember = $request->boolean('remember');

        // Store guest cart session before login
        $guestSessionId = session()->getId();

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            // Merge guest cart with user cart
            $this->mergeGuestCart($guestSessionId);

            // Redirect based on role
            $user = Auth::user();
            if ($user->hasStaffAccess()) {
                return redirect()->intended(route('admin.dashboard'));
            }

            return redirect()->intended(route('home'));
        }

        return back()->withErrors([
            'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    protected function mergeGuestCart(string $guestSessionId): void
    {
        $guestCart = Cart::where('session_id', $guestSessionId)->first();

        if ($guestCart && $guestCart->items->isNotEmpty()) {
            $userCart = Cart::getOrCreate(Auth::id());
            $userCart->mergeWith($guestCart);
        }
    }
}
