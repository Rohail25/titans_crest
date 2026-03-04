<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Titans Crest</title>
    <meta name="description" content="Login to your Titans Crest account and manage your investments.">
    
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
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <a href="/" class="text-2xl font-bold text-blue-900">Titans <span class="gradient-text">Crest</span></a>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="/" class="text-gray-700 hover:text-blue-900 transition">Back Home</a>
                    <a href="/register" class="bg-blue-900 text-white px-6 py-2 rounded-lg hover:bg-blue-800 transition">Get Started</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="min-h-screen gradient-bg pt-24 pb-12 px-4 sm:px-6 lg:px-8 flex items-center">
        <div class="w-full max-w-md mx-auto">
            <!-- Card -->
            <div class="bg-white rounded-2xl shadow-2xl p-8 md:p-10">
                <!-- Header -->
                <div class="text-center mb-8">
                    <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-2">Welcome Back</h1>
                    <p class="text-gray-600">Sign in to access your investment account</p>
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
                <form method="POST" action="{{ route('login.post') }}" class="space-y-6">
                    @csrf

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-900 mb-2">Email Address</label>
                        <input 
                            type="email" 
                            name="email" 
                            id="email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg input-focus transition bg-white text-gray-900 placeholder-gray-500"
                            placeholder="you@example.com"
                        >
                        @error('email')
                            <p class="mt-2 text-red-600 text-sm font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-semibold text-gray-900 mb-2">Password</label>
                        <input 
                            type="password" 
                            name="password" 
                            id="password"
                            required
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg input-focus transition bg-white text-gray-900 placeholder-gray-500"
                            placeholder="••••••••"
                        >
                        @error('password')
                            <p class="mt-2 text-red-600 text-sm font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center">
                        <input type="checkbox" name="remember" id="remember" class="rounded border-gray-300 text-blue-900 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <label for="remember" class="ml-3 text-sm text-gray-700">Remember me</label>
                    </div>

                    <!-- Submit Button -->
                    <button 
                        type="submit" 
                        class="w-full bg-gray-400 text-black font-bold py-3 rounded-lg hover:from-blue-800 hover:to-blue-600 transition shadow-lg"
                    >
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
                <a 
                    href="/register"
                    class="block w-full text-center bg-gray-100 hover:bg-gray-200 text-gray-900 font-bold py-3 rounded-lg transition border-2 border-gray-200"
                >
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
            <p class="text-center text-white text-sm mt-8">
                Having trouble? <a href="/" class="font-semibold hover:underline">Contact Support</a>
            </p>
        </div>
    </div>

</body>
</html>
