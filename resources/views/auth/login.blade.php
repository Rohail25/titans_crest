<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Titans Crest</title>
    <meta name="description" content="Login to your Titans Crest account and manage your investments.">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        html,
        body {
            font-family: 'Inter', sans-serif;
            height: auto;
            overflow-y: auto;
        }

        .gradient-bg {
            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 50%, #3b82f6 100%);
        }

        .gradient-text {
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .input-focus:focus {
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            border-color: #3b82f6;
        }
    </style>
</head>

<body class="bg-gray-50" style="overflow-y: auto !important;">

    <!-- Navigation -->
    <nav class="bg-white shadow-sm fixed w-full top-0 z-50">
        <div class="max-w-7xl mx-auto py-2 px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    {{-- <a href="/" class="text-2xl font-bold text-blue-900">Titans <span class="gradient-text">Crest</span></a> --}}
                    <a href="/"> <img src="images/logo.svg" alt="Titans Crest" width="80" height="80"></a>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="/" class="text-gray-700 hover:text-blue-900 transition">Back Home</a>
                    <a href="/register"
                        class="bg-blue-900 text-white px-6 py-2 rounded-lg hover:bg-blue-800 transition">Get Started</a>
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

                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Welcome Back</h1>
                    <p class="text-gray-600 text-sm">Sign in to access your investment account</p>
                </div>

                <!-- Session Messages -->
                @if (session('success'))
                    <div class="mb-6 p-4 bg-green-50 border-2 border-green-200 rounded-lg">
                        <p class="text-green-800 font-semibold text-sm">{{ session('success') }}</p>
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-6 p-4 bg-red-50 border-2 border-red-200 rounded-lg">
                        <p class="text-red-800 font-semibold text-sm">{{ session('error') }}</p>
                    </div>
                @endif

                @if (session('info'))
                    <div class="mb-6 p-4 bg-blue-50 border-2 border-blue-200 rounded-lg">
                        <p class="text-blue-800 font-semibold text-sm">{{ session('info') }}</p>
                    </div>
                @endif

                <!-- Validation Errors Alert -->
                @if ($errors->any())
                    <div class="mb-6 p-4 bg-red-50 border-2 border-red-200 rounded-lg">
                        <p class="text-red-800 font-semibold text-sm mb-2">Login Failed</p>
                        <ul class="text-red-700 text-sm space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>• {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Form -->
                <form method="POST" action="{{ route('login.post') }}" class="space-y-5">
                    @csrf

                    <!-- Email -->
                    <div class="mb-6">
                        <label for="email" class="block text-sm font-semibold text-gray-900 mb-3">Email
                            Address</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required
                            autofocus
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg input-focus transition bg-white text-gray-900 placeholder-gray-500 hover:border-gray-400"
                            placeholder="you@example.com">
                        @error('email')
                            <p class="mt-2 text-red-600 text-sm font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="mb-3">

                        <div class="relative">
                            <input type="password" name="password" id="password" required
                                class="w-full px-4 py-3 pr-14 border-2 border-gray-300 rounded-lg input-focus transition bg-white text-gray-900 placeholder-gray-500 hover:border-gray-400"
                                placeholder="••••••••">
                            <div class="flex justify-between items-center mb-3">
                                <label for="password" class="block text-sm font-semibold text-gray-900">Password</label>
                                <a href="{{ route('password.forgot') }}"
                                    class="text-sm text-blue-900 hover:text-blue-700 font-semibold transition">
                                    <i class="fas fa-question-circle"></i> Forgot Password?
                                </a>
                            </div>
                            <div class="absolute inset-y-1 right-0 flex items-center pr-4">
                                <button type="button" onclick="togglePasswordVisibility('password')"
                                    class="text-gray-600 hover:text-blue-900 transition focus:outline-none cursor-pointer text-lg leading-none"
                                    tabindex="-1" title="Toggle password visibility">
                                    <i class="fas fa-eye" id="password-icon"></i>
                                </button>
                            </div>
                        </div>
                        @error('password')
                            <p class="mt-2 text-red-600 text-sm font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center mb-6">
                        <input type="checkbox" name="remember" id="remember"
                            class="rounded border-2 border-gray-300 text-blue-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 cursor-pointer">
                        <label for="remember" class="ml-3 text-sm text-gray-700 cursor-pointer">Remember me</label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit"
                        class="text-black w-full bg-gradient-to-r from-blue-900 to-blue-700 font-bold py-3 rounded-lg hover:from-blue-800 hover:to-blue-600 transition shadow-lg mb-6 border-2 border-blue-800">
                        Sign In
                    </button>
                </form>

                <!-- Divider -->
                <div class="relative my-8">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-white text-gray-500">Don't have an account?</span>
                    </div>
                </div>

                <!-- Register Link -->
                <a href="/register"
                    class="block w-full text-center bg-gray-100 hover:bg-gray-200 text-gray-900 font-bold py-3 rounded-lg transition border-2 border-gray-200">
                    Create Account
                </a>

                <!-- Info Box -->
                {{-- <div class="mt-8 p-4 bg-blue-50 border-l-4 border-blue-900 rounded">
                    <p class="text-blue-900 text-sm">
                        <span class="font-semibold">Demo Login:</span><br>
                        Email: demo@example.com<br>
                        Password: password
                    </p>
                </div> --}}
            </div>

            <!-- Footer Text -->
            {{-- <p class="text-center text-white text-sm mt-8">
                Having trouble? <a href="/" class="font-semibold hover:underline">Contact Support</a>
            </p> --}}
        </div>
    </div>

    <script>
        function togglePasswordVisibility(fieldId) {
            const field = document.getElementById(fieldId);
            const icon = document.getElementById(fieldId + '-icon');

            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                field.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
</body>

</html>
