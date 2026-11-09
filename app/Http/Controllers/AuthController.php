<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    /**
     * Show the login form
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Silakan masukkan alamat email yang valid.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        // Check if user exists and is active
        $user = User::where('email', $validated['email'])->first();
        
        if (!$user || !$user->is_active) {
            return back()->withErrors([
                'email' => 'Email atau kata sandi tidak sesuai, atau akun tidak aktif.',
            ])->onlyInput('email');
        }

        // Attempt authentication
        if (Auth::attempt($validated, $request->boolean('remember'))) {
            $request->session()->regenerate();
            
            // Update last login timestamp
            $user->update(['last_login_at' => now()]);

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'Email atau kata sandi yang Anda masukkan tidak benar.',
        ])->onlyInput('email');
    }

    /**
     * Show the registration form
     */
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    /**
     * Handle registration request
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.max' => 'Nama lengkap tidak boleh lebih dari 255 karakter.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Silakan masukkan alamat email yang valid.',
            'email.unique' => 'Alamat email ini sudah terdaftar.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'password.min' => 'Kata sandi minimal harus 8 karakter.',
            'password.mixed_case' => 'Kata sandi harus mengandung huruf besar dan kecil.',
            'password.numbers' => 'Kata sandi harus mengandung minimal satu angka.',
            'password.symbols' => 'Kata sandi harus mengandung minimal satu simbol.',
        ]);

        // Create new user (Staff by default)
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'Staff', // Default role for new registrations
            'is_active' => true,
        ]);

        event(new Registered($user));

        // Redirect to login with success message
        return redirect(route('login'))->with('success', 'Registrasi berhasil. Silakan masuk menggunakan akun yang baru saja Anda buat.');
    }

    /**
     * Handle logout request
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
