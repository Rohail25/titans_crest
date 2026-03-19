<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Forgot Password - Titans Crest</title>
    <meta name="description" content="Reset your Titans Crest account password.">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        .gradient-bg { background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 50%, #3b82f6 100%); }
        .gradient-text { background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .input-focus:focus { 
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            border-color: #3b82f6;
        }
    </style>
</head>
<body class="bg-gray-50">

    <!-- Navigation -->
    <nav class="bg-white shadow-sm fixed w-full top-0 z-50">
        <div class="max-w-7xl mx-auto py-2 px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <a href="/"> <img src="images/logo.svg" alt="Titans Crest" width="80" height="80"></a>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="/" class="text-gray-700 hover:text-blue-900 transition">Back Home</a>
                    <a href="/register" class="bg-blue-900 text-white px-6 py-2 rounded-lg hover:bg-blue-800 transition">Get Started</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="min-h-screen gradient-bg mt-5 pt-24 pb-12 px-4 sm:px-6 lg:px-8 flex items-center justify-center">
        <div style="width: 100%; max-width: 450px;">
            <!-- Card -->
            <div class="bg-white rounded-2xl shadow-2xl p-8">
                <!-- Header -->
                <div class="text-center mb-8">
                    <div class="mb-4">
                        <a href="/" class="inline-block text-2xl font-bold text-blue-900">Titans <span class="gradient-text">Crest</span></a>
                    </div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Forgot Password?</h1>
                    <p class="text-gray-600 text-sm">Don't worry! We'll help you reset it.</p>
                </div>

                <!-- Session Messages -->
                @if (session('message'))
                    <div class="mb-6 p-4 bg-green-50 border-2 border-green-200 rounded-lg">
                        <p class="text-green-800 font-semibold text-sm"><i class="fas fa-check-circle"></i> {{ session('message') }}</p>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-6 p-4 bg-red-50 border-2 border-red-200 rounded-lg">
                        <p class="text-red-800 font-semibold text-sm mb-2"><i class="fas fa-exclamation-circle"></i> Error</p>
                        <ul class="text-red-700 text-sm space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>• {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Form -->
                <form method="POST" action="{{ route('password.send-reset-link') }}" class="space-y-5">
                    @csrf

                    <!-- Email -->
                    <div class="mb-6">
                        <label for="email" class="block text-sm font-semibold text-gray-900 mb-3">Email Address</label>
                        <input 
                            type="email" 
                            name="email" 
                            id="email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg input-focus transition bg-white text-gray-900 placeholder-gray-500 hover:border-gray-400"
                            placeholder="you@example.com"
                        >
                        @error('email')
                            <p class="mt-2 text-red-600 text-sm font-medium">{{ $message }}</p>
                        @enderror
                        <p class="mt-2 text-gray-600 text-xs">We'll send a password reset link to this email address.</p>
                    </div>

                    <!-- Submit Button -->
                    <button 
                        type="submit" 
                        class="w-full bg-gradient-to-r from-blue-900 to-blue-700 font-bold py-3 rounded-lg hover:from-blue-800 hover:to-blue-600 transition shadow-lg border-2 border-blue-800 "
                    >
                        <i class="fas fa-paper-plane"></i> Send Reset Link
                    </button>

                    <!-- Back to Login -->
                    <div class="text-center mt-6">
                        <p class="text-gray-700 text-sm">
                            Remember your password? 
                            <a href="{{ route('login') }}" class="text-blue-900 font-semibold hover:text-blue-700 transition">
                                <i class="fas fa-sign-in-alt"></i> Sign In
                            </a>
                        </p>
                    </div>
                </form>

                <!-- Info Box -->
                <div class="mt-6 p-4 bg-blue-50 border-l-4 border-blue-900 rounded">
                    <p class="text-blue-900 text-xs">
                        <i class="fas fa-info-circle"></i> 
                        <strong>Security Notice:</strong> Reset links expire after 1 hour. If you didn't request this, you can safely ignore this email.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePasswordVisibility(inputId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(inputId + '-icon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>
