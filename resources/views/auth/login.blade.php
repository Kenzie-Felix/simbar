@extends('layouts.app')

@section('title', 'Login - SIMBAR')

@section('content')
<style>
    .login-container {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        padding: var(--spacing-lg);
    }

    .login-card {
        background: white;
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-xl);
        width: 100%;
        max-width: 450px;
        padding: var(--spacing-2xl);
    }

    .login-header {
        text-align: center;
        margin-bottom: var(--spacing-2xl);
    }

    .login-header h1 {
        font-size: 2rem;
        font-weight: var(--font-weight-bold);
        color: var(--primary);
        margin-bottom: var(--spacing-sm);
    }

    .login-subtitle {
        font-size: var(--font-size-sm);
        color: var(--gray-500);
    }

    .form-group {
        margin-bottom: var(--spacing-lg);
    }

    .form-label {
        display: block;
        margin-bottom: var(--spacing-sm);
        font-weight: var(--font-weight-medium);
        color: var(--gray-700);
        font-size: var(--font-size-sm);
    }

    .form-control {
        width: 100%;
        padding: var(--spacing-sm) var(--spacing-md);
        border: 1px solid var(--gray-300);
        border-radius: var(--radius-md);
        font-size: var(--font-size-sm);
        transition: all 0.2s ease;
        font-family: var(--font-family);
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px var(--primary-light);
    }

    .form-control.is-invalid {
        border-color: var(--danger);
    }

    .form-control.is-invalid:focus {
        box-shadow: 0 0 0 3px var(--danger-light);
    }

    .invalid-feedback {
        display: block;
        margin-top: var(--spacing-xs);
        font-size: var(--font-size-xs);
        color: var(--danger);
    }

    .remember-me {
        display: flex;
        align-items: center;
        gap: var(--spacing-sm);
        margin-bottom: var(--spacing-lg);
    }

    .remember-me input[type="checkbox"] {
        width: 1rem;
        height: 1rem;
        cursor: pointer;
    }

    .remember-me label {
        cursor: pointer;
        font-size: var(--font-size-sm);
        color: var(--gray-700);
        margin: 0;
    }

    .login-button {
        width: 100%;
        padding: var(--spacing-md);
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        color: white;
        border: none;
        border-radius: var(--radius-md);
        font-size: var(--font-size-base);
        font-weight: var(--font-weight-medium);
        cursor: pointer;
        transition: all 0.3s ease;
        margin-bottom: var(--spacing-lg);
        box-shadow: 0 4px 12px rgba(30, 58, 138, 0.3);
    }

    .login-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(30, 58, 138, 0.4);
    }

    .login-button:active {
        transform: translateY(0px);
    }

    .login-divider {
        border: none;
        border-top: 1px solid var(--gray-200);
        margin: var(--spacing-lg) 0;
    }

    .login-footer {
        text-align: center;
    }

    .login-footer p {
        font-size: var(--font-size-sm);
        color: var(--gray-600);
        margin: 0;
    }

    .login-footer a {
        color: var(--primary);
        text-decoration: none;
        font-weight: var(--font-weight-medium);
        transition: color 0.2s ease;
    }

    .login-footer a:hover {
        color: var(--primary-dark);
        text-decoration: underline;
    }

    .password-input-wrapper {
        position: relative;
    }

    .password-toggle {
        position: absolute;
        right: var(--spacing-md);
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        cursor: pointer;
        color: var(--gray-500);
        font-size: 1.1rem;
        padding: var(--spacing-xs);
        transition: color 0.2s ease;
        z-index: 10;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
    }

    .password-toggle:hover {
        color: var(--primary);
    }

    @media (max-width: 480px) {
        .login-container {
            padding: var(--spacing-md);
        }

        .login-card {
            padding: var(--spacing-lg);
        }

        .login-header h1 {
            font-size: 1.5rem;
        }
    }
</style>

<div class="login-container">
    <div class="login-card">
        <!-- Header -->
        <div class="login-header">
            <h1>
                <i class="bi bi-boxes"></i> SIMBAR
            </h1>
            <p class="login-subtitle">Office Inventory Management System</p>
        </div>

        <!-- Errors -->
        @if ($errors->any())
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-circle alert-icon"></i>
                <div>
                    <strong>Login Failed</strong>
                    <ul style="margin: var(--spacing-sm) 0 0 var(--spacing-lg); padding-left: var(--spacing-lg);">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <!-- Login Form -->
        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email -->
            <div class="form-group">
                <label for="email" class="form-label">Email Address</label>
                <input 
                    type="email" 
                    class="form-control @error('email') is-invalid @enderror" 
                    id="email" 
                    name="email" 
                    value="{{ old('email') }}" 
                    required 
                    autofocus
                    placeholder="Enter your email address"
                >
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Password -->
            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <div class="password-input-wrapper">
                    <input 
                        type="password" 
                        class="form-control @error('password') is-invalid @enderror" 
                        id="password" 
                        name="password" 
                        required
                        placeholder="Enter your password"
                        style="padding-right: 45px;"
                    >
                    <button 
                        type="button" 
                        class="password-toggle" 
                        id="passwordToggle" 
                        aria-label="Show/hide password"
                        tabindex="-1"
                    >
                        <i class="bi bi-eye" id="passwordIcon"></i>
                    </button>
                </div>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Remember Me -->
            <div class="remember-me">
                <input 
                    type="checkbox" 
                    id="remember" 
                    name="remember"
                >
                <label for="remember">Remember me</label>
            </div>

            <!-- Login Button -->
            <button type="submit" class="login-button">
                <i class="bi bi-box-arrow-in-right"></i> Login
            </button>

            <!-- Divider -->
            <hr class="login-divider">

            <!-- Footer -->
            <div class="login-footer">
                <p>
                    Don't have an account?
                    <a href="{{ route('register') }}">Register here</a>
                </p>
            </div>
        </form>
    </div>
</div>

<script>
// Password visibility toggle
document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('password');
    const passwordToggle = document.getElementById('passwordToggle');
    const passwordIcon = document.getElementById('passwordIcon');

    if (passwordToggle && passwordInput) {
        passwordToggle.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Toggle input type
            const isPasswordType = passwordInput.type === 'password';
            passwordInput.type = isPasswordType ? 'text' : 'password';
            
            // Toggle icon
            if (isPasswordType) {
                passwordIcon.classList.remove('bi-eye');
                passwordIcon.classList.add('bi-eye-slash');
            } else {
                passwordIcon.classList.remove('bi-eye-slash');
                passwordIcon.classList.add('bi-eye');
            }
        });
    }
});
</script>

@endsection
