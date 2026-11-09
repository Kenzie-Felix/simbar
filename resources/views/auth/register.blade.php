@extends('layouts.app')

@section('title', 'Register - SIMBAR')

@section('content')
<style>
    .register-container {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        padding: var(--spacing-lg);
    }

    .register-card {
        background: white;
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-xl);
        width: 100%;
        max-width: 500px;
        padding: var(--spacing-2xl);
    }

    .register-header {
        text-align: center;
        margin-bottom: var(--spacing-2xl);
    }

    .register-header h1 {
        font-size: 1.75rem;
        font-weight: var(--font-weight-bold);
        color: var(--gray-900);
        margin-bottom: var(--spacing-sm);
    }

    .register-subtitle {
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

    .form-helper-text {
        font-size: var(--font-size-xs);
        color: var(--gray-500);
        display: block;
        margin-top: var(--spacing-sm);
    }

    .form-helper-text ul {
        margin: var(--spacing-xs) 0 0 var(--spacing-lg);
        padding-left: var(--spacing-lg);
    }

    .form-helper-text li {
        margin-bottom: var(--spacing-xs);
    }

    .register-button {
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

    .register-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(30, 58, 138, 0.4);
    }

    .register-button:active {
        transform: translateY(0px);
    }

    .register-divider {
        border: none;
        border-top: 1px solid var(--gray-200);
        margin: var(--spacing-lg) 0;
    }

    .register-footer {
        text-align: center;
    }

    .register-footer p {
        font-size: var(--font-size-sm);
        color: var(--gray-600);
        margin: 0;
    }

    .register-footer a {
        color: var(--primary);
        text-decoration: none;
        font-weight: var(--font-weight-medium);
        transition: color 0.2s ease;
    }

    .register-footer a:hover {
        color: var(--primary-dark);
        text-decoration: underline;
    }

    @media (max-width: 480px) {
        .register-container {
            padding: var(--spacing-md);
        }

        .register-card {
            padding: var(--spacing-lg);
        }

        .register-header h1 {
            font-size: 1.5rem;
        }
    }
</style>

<div class="register-container">
    <div class="register-card">
        <!-- Header -->
        <div class="register-header">
            <h1>Create Account</h1>
            <p class="register-subtitle">Join SIMBAR - Enterprise Inventory Management</p>
        </div>

        <!-- Errors -->
        @if ($errors->any())
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-circle alert-icon"></i>
                <div>
                    <strong>Registration Failed</strong>
                    <ul style="margin: var(--spacing-sm) 0 0 var(--spacing-lg); padding-left: var(--spacing-lg);">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <!-- Registration Form -->
        <form method="POST" action="{{ route('register') }}">
            @csrf

            <!-- Full Name -->
            <div class="form-group">
                <label for="name" class="form-label">Full Name</label>
                <input 
                    type="text" 
                    class="form-control @error('name') is-invalid @enderror" 
                    id="name" 
                    name="name" 
                    value="{{ old('name') }}" 
                    required 
                    autofocus
                    placeholder="Enter your full name"
                >
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

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
                    placeholder="Enter your email address"
                >
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Password -->
            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <input 
                    type="password" 
                    class="form-control @error('password') is-invalid @enderror" 
                    id="password" 
                    name="password" 
                    required
                    placeholder="Create a strong password"
                >
                <span class="form-helper-text">
                    <strong>Password Requirements:</strong>
                    <ul>
                        <li>Minimum 8 characters</li>
                        <li>Uppercase and lowercase letters</li>
                        <li>At least one number</li>
                        <li>At least one special character (!@#$%^&*)</li>
                    </ul>
                </span>
                @error('password')
                    <div class="invalid-feedback" style="display: block;">{{ $message }}</div>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div class="form-group">
                <label for="password_confirmation" class="form-label">Confirm Password</label>
                <input 
                    type="password" 
                    class="form-control @error('password_confirmation') is-invalid @enderror" 
                    id="password_confirmation" 
                    name="password_confirmation" 
                    required
                    placeholder="Confirm your password"
                >
                @error('password_confirmation')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Register Button -->
            <button type="submit" class="register-button">
                <i class="bi bi-person-plus"></i> Create Account
            </button>

            <!-- Divider -->
            <hr class="register-divider">

            <!-- Footer -->
            <div class="register-footer">
                <p>
                    Already have an account?
                    <a href="{{ route('login') }}">Login here</a>
                </p>
            </div>
        </form>
    </div>
</div>

@endsection
