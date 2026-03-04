<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register - Titans Crest</title>
    <meta name="description" content="Create your Titans Crest account and start investing in BNB.">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        .gradient-bg { background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 50%, #3b82f6 100%); }
        .gradient-text { background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .input-focus:focus { 
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            border-color: #3b82f6;
        }
        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2rem;
        }
        .step {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .step-number {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: #6b7280;
            margin-bottom: 0.5rem;
        }
        .step.active .step-number {
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
            color: white;
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
                    <a href="/login" class="bg-blue-900 text-white px-6 py-2 rounded-lg hover:bg-blue-800 transition">Sign In</a>
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
                    <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-2">Create Account</h1>
                    <p class="text-gray-600">Join thousands of successful investors</p>
                </div>

                <!-- Global Error Alert -->
                @if ($errors->any())
                    <div class="mb-6 p-4 bg-red-50 border-2 border-red-200 rounded-lg">
                        <p class="text-red-800 font-semibold text-sm mb-2">Registration Failed</p>
                        <ul class="text-red-700 text-sm space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>• {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Benefits -->
                <div class="grid grid-cols-3 gap-3 mb-8">
                    <div class="text-center">
                        <div class="text-2xl font-bold gradient-text">3x</div>
                        <div class="text-xs text-gray-600">Max Returns</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold gradient-text">24/7</div>
                        <div class="text-xs text-gray-600">Support</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold gradient-text">100%</div>
                        <div class="text-xs text-gray-600">Transparent</div>
                    </div>
                </div>

                <!-- Form -->
                <form method="POST" action="/register" class="space-y-6">
                    @csrf

                    <!-- Full Name -->
                    <div>
                        <label for="name" class="block text-sm font-semibold text-gray-900 mb-2">Full Name</label>
                        <input 
                            type="text" 
                            name="name" 
                            id="name"
                            value="{{ old('name') }}"
                            required
                            autofocus
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg input-focus transition bg-white text-gray-900 placeholder-gray-500"
                            placeholder="John Doe"
                        >
                        @error('name')
                            <p class="mt-2 text-red-600 text-sm font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-900 mb-2">Email Address</label>
                        <input 
                            type="email" 
                            name="email" 
                            id="email"
                            value="{{ old('email') }}"
                            required
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
                        <p class="mt-2 text-xs text-gray-500">Minimum 6 characters</p>
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-semibold text-gray-900 mb-2">Confirm Password</label>
                        <input 
                            type="password" 
                            name="password_confirmation" 
                            id="password_confirmation"
                            required
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg input-focus transition bg-white text-gray-900 placeholder-gray-500"
                            placeholder="••••••••"
                        >
                    </div>

                    <!-- Referral Code (Optional) -->
                    <div>
                        <label for="referral_code" class="block text-sm font-semibold text-gray-900 mb-2">Referral Code (Optional)</label>
                        <input 
                            type="text" 
                            name="referral_code" 
                            id="referral_code"
                            value="{{ old('referral_code', $referralCode ?? request('ref')) }}"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg input-focus transition bg-white text-gray-900 placeholder-gray-500"
                            placeholder="Enter referral code"
                        >
                        @error('referral_code')
                            <p class="mt-2 text-red-600 text-sm font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Terms -->
                    <div>
                        <div class="flex items-start">
                            <input type="checkbox" name="terms" id="terms" required class="mt-1 rounded border-gray-300 text-blue-900 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <label for="terms" class="ml-3 text-sm text-gray-700">
                                I agree to the <a href="#" class="text-blue-900 font-semibold hover:underline">Terms of Service</a> and <a href="#" class="text-blue-900 font-semibold hover:underline">Privacy Policy</a>
                            </label>
                        </div>
                        @error('terms')
                            <p class="mt-2 text-red-600 text-sm font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <button 
                        type="submit" 
                        class="w-full bg-gradient-to-r from-amber-500 to-amber-600 text-white font-bold py-3 rounded-lg hover:from-amber-600 hover:to-amber-700 transition shadow-lg"
                    >
                        Create Account
                    </button>
                </form>

                <!-- Divider -->
                <div class="relative my-8">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-white text-gray-500">Already have an account?</span>
                    </div>
                </div>

                <!-- Login Link -->
                <a 
                    href="/login"
                    class="block w-full text-center bg-gray-100 hover:bg-gray-200 text-gray-900 font-bold py-3 rounded-lg transition border-2 border-gray-200"
                >
                    Sign In
                </a>

                <!-- Features -->
                {{-- <div class="mt-8 pt-6 border-t border-gray-200">
                    <p class="text-xs text-gray-600 font-semibold mb-3 text-center">WHAT YOU GET</p>
                    <ul class="space-y-2 text-sm text-gray-700">
                        <li class="flex items-center">
                            <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Instant account activation
                        </li>
                        <li class="flex items-center">
                            <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            24/7 customer support
                        </li>
                        <li class="flex items-center">
                            <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Bank-level security
                        </li>
                    </ul>
                </div> --}}
            </div>

            <!-- Footer Text -->
            <p class="text-center text-white text-sm mt-8">
                Protected by 256-bit SSL encryption
            </p>
        </div>
    </div>

</body>
</html>
