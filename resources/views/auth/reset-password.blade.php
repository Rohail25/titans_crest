<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset Password - Titans Crest</title>
    <meta name="description" content="Create a new password for your Titans Crest account.">
    
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
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Create New Password</h1>
                    <p class="text-gray-600 text-sm">Enter your new password to reset your account access</p>
                </div>

                <!-- Validation Errors Alert -->
                @if ($errors->any())
                    <div class="mb-6 p-4 bg-red-50 border-2 border-red-200 rounded-lg">
                        <p class="text-red-800 font-semibold text-sm mb-2">Password Reset Failed</p>
                        <ul class="text-red-700 text-sm space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>• {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Form -->
                <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
                    @csrf
                    <input type="hidden" name="email" value="{{ $email }}">
                    <input type="hidden" name="token" value="{{ $token }}">

                    <!-- New Password -->
                    <div class="mb-6">
                        <label for="password" class="block text-sm font-semibold text-gray-900 mb-3">New Password</label>
                        <div class="relative">
                            <input 
                                type="password" 
                                name="password" 
                                id="password"
                                required
                                minlength="8"
                                class="w-full px-4 py-3 pr-14 border-2 border-gray-300 rounded-lg input-focus transition bg-white text-gray-900 placeholder-gray-500 hover:border-gray-400"
                                placeholder="••••••••"
                            >
                            <div class="absolute inset-y-1 right-0 flex items-center pr-4">
                                <button
                                    type="button"
                                    onclick="togglePasswordVisibility('password')"
                                    class="text-gray-600 hover:text-blue-900 transition focus:outline-none cursor-pointer text-lg leading-none"
                                    tabindex="-1"
                                    title="Toggle password visibility"
                                >
                                    <i class="fas fa-eye" id="password-icon"></i>
                                </button>
                            </div>
                        </div>
                        @error('password')
                            <p class="mt-2 text-red-600 text-sm font-medium">{{ $message }}</p>
                        @enderror
                        <p class="mt-2 text-gray-600 text-xs">Password must be at least 8 characters long.</p>
                    </div>

                    <!-- Confirm Password -->
                    <div class="mb-6">
                        <label for="password_confirmation" class="block text-sm font-semibold text-gray-900 mb-3">Confirm Password</label>
                        <div class="relative">
                            <input 
                                type="password" 
                                name="password_confirmation" 
                                id="password_confirmation"
                                required
                                minlength="8"
                                class="w-full px-4 py-3 pr-14 border-2 border-gray-300 rounded-lg input-focus transition bg-white text-gray-900 placeholder-gray-500 hover:border-gray-400"
                                placeholder="••••••••"
                            >
                            <div class="absolute inset-y-1 right-0 flex items-center pr-4">
                                <button
                                    type="button"
                                    onclick="togglePasswordVisibility('password_confirmation')"
                                    class="text-gray-600 hover:text-blue-900 transition focus:outline-none cursor-pointer text-lg leading-none text-black"
                                    
                                    tabindex="-1"
                                    title="Toggle password visibility"
                                >
                                    <i class="fas fa-eye" id="password_confirmation-icon"></i>
                                </button>
                            </div>
                        </div>
                        @error('password_confirmation')
                            <p class="mt-2 text-red-600 text-sm font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password Tips -->
                    <div class="p-4 bg-blue-50 border-l-4 border-blue-900 rounded-lg mb-6">
                        <p class="text-blue-900 text-xs font-semibold mb-2"><i class="fas fa-lightbulb"></i> Password Tips</p>
                        <ul class="text-blue-800 text-xs space-y-1">
                            <li>✓ Use at least 8 characters</li>
                            <li>✓ Mix uppercase, lowercase, numbers and symbols</li>
                            <li>✓ Avoid using personal information</li>
                        </ul>
                    </div>

                    <!-- Submit Button -->
                    <button 
                        type="submit" 
                        class="w-full bg-gradient-to-r from-blue-900 to-blue-700 font-bold py-3 rounded-lg hover:from-blue-800 hover:to-blue-600 transition shadow-lg border-2 border-blue-800 text-white"
                    >
                        <i class="fas fa-save"></i> Update Password
                    </button>
                </form>

                <!-- Info Box -->
                <div class="mt-6 p-4 bg-green-50 border-l-4 border-green-900 rounded">
                    <p class="text-green-900 text-xs">
                        <i class="fas fa-check-circle"></i> 
                        <strong>You will be logged in automatically</strong> after your password is updated successfully.
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
