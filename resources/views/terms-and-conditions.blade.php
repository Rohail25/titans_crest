<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Terms and Conditions - Titans Crest</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Source+Serif+4:opsz,wght@8..60,400;8..60,600&display=swap');

        :root {
            --ink: #1f2937;
            --ink-soft: #4b5563;
            --accent: #d4af37;
        }

        body {
            font-family: 'Manrope', sans-serif;
            color: var(--ink);
            text-rendering: optimizeLegibility;
            -webkit-font-smoothing: antialiased;
        }

        .gradient-bg { background: linear-gradient(135deg, #041a3d 0%, #0b2f66 55%, #174a8f 100%); }

        .terms-hero-title {
            font-weight: 800;
            letter-spacing: -0.02em;
            line-height: 1.1;
        }

        .terms-hero-subtitle {
            font-weight: 500;
            letter-spacing: 0.01em;
            opacity: 0.95;
        }

        .terms-panel {
            max-width: 72ch;
        }

        .terms-list {
            font-family: 'Source Serif 4', Georgia, serif;
            font-size: 1.06rem;
            line-height: 1.95;
            color: var(--ink-soft);
            list-style: decimal;
            padding-left: 1.5rem;
        }

        .terms-list li::marker {
            color: var(--accent);
            font-weight: 700;
            font-family: 'Manrope', sans-serif;
        }

        .terms-list li + li {
            margin-top: 0.55rem;
        }
    </style>
</head>
<body class="bg-gray-100">
    <nav class="bg-[#062a5f] shadow-sm fixed w-full top-0 z-50 border-b border-[#d4af37]/30" style="background-color: #062a5f;">
        <div class="max-w-7xl bg-[#062a5f] mx-auto py-3 px-4 sm:px-6 lg:px-8" >
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    {{-- <span class="text-2xl font-bold text-blue-900">Titans <span class="gradient-text">Crest</span></span> --}}
                    <a href="/"><img src="images/logo.svg" alt="Titans Crest" width="80" height="80"></a>
                </div>
                <div class="hidden md:flex items-center space-x-8">
                    <a href="/"  class="text-white hover:text-[#d4af37] transition">Home</a>
                    <a href="/about"  class="text-white hover:text-[#d4af37] transition">About</a>
                    <a href="/#packages" class="text-white hover:text-[#d4af37] transition">Packages</a>
                    <a href="/#stats" class="text-white hover:text-[#d4af37] transition">Stats</a>
                </div>
                <div class="flex items-center space-x-4">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="bg-[#d4af37] text-[#062a5f] px-6 py-2 rounded-lg font-semibold hover:bg-[#e1c263] transition">Dashboard</a>
                    @else
                        <a href="/login" class="text-white px-5 py-2 rounded-lg hover:bg-white/10 transition" style="background-color: #174a8f;">Login</a>
                        <a href="/register" class="text-white  px-6 py-2 rounded-lg font-bold hover:bg-[#e1c263] transition shadow-md" style="background-color: #d4af37;">Sign Up</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>


    <section class="gradient-bg py-14 px-4 sm:px-6 lg:px-8" style="margin-top: 100px;">
        <div class="max-w-5xl mx-auto">
            <h1 class="terms-hero-title text-4xl md:text-5xl text-white mb-3">Terms and Conditions</h1>
            <p class="terms-hero-subtitle text-blue-100 text-base md:text-lg">Please read these terms carefully before using the platform.</p>
        </div>
    </section>

    <section class="py-12 px-4 sm:px-6 lg:px-8" style="background-color: #f3f4f6;">
        <div class="terms-panel max-w-5xl mx-auto bg-white rounded-2xl shadow-md border border-gray-200 p-8 sm:p-10">
            <ol class="terms-list">
                <li>All investments made on the platform are voluntary and users must understand the risks involved in digital asset trading.</li>
                <li>The company aims to generate profits through professional cryptocurrency trading and digital asset management.</li>
                <li>Returns are estimated and may vary depending on market conditions.</li>
                <li>Investors must register an account and select an investment package to participate in the program.</li>
                <li>The minimum withdrawal amount is 10 USD.</li>
                <li>A 5% withdrawal fee will be applied to every withdrawal request.</li>
                <li>Profits will be distributed according to the investment plan selected by the user.</li>
                <li>The company reserves the right to update or modify the investment plans and policies when necessary.</li>
                <li>Any misuse of the platform, multiple accounts, or fraudulent activity may result in account suspension.</li>
            </ol>

            <div class="mt-10">
                <a href="/register" class="inline-block bg-[#062a5f] text-white px-6 py-3 rounded-lg font-semibold hover:bg-[#174a8f] transition">Back to Registration</a>
            </div>
        </div>
    </section>
     <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <div class="grid md:grid-cols-4 gap-8 mb-8">
                <div>
                    <h3 class="text-2xl font-bold mb-4">Titans <span class="gradient-text">Crest</span></h3>
                    <p class="text-gray-400">Professional BNB investment platform for sustainable wealth growth.</p>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Platform</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="/#features" class="hover:text-amber-400 transition">Features</a></li>
                        <li><a href="/#packages" class="hover:text-amber-400 transition">Packages</a></li>
                        <li><a href="/#how-it-works" class="hover:text-amber-400 transition">How It Works</a></li>
                        <li><a href="/#stats" class="hover:text-amber-400 transition">Statistics</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Account</h4>
                    <ul class="space-y-2 text-gray-400">
                        @auth
                            <li><a href="{{ url('/dashboard') }}" class="hover:text-amber-400 transition">Dashboard</a></li>
                        @else
                            <li><a href="/login" class="hover:text-amber-400 transition">Login</a></li>
                            <li><a href="/register" class="hover:text-amber-400 transition">Register</a></li>
                            <li><a href="/terms-and-conditions" class="hover:text-amber-400 transition">Terms and Conditions</a></li>
                        @endauth
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Contact</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li>Titansingapur7@gmail.com</li>
                        <li>24/7 Live Support</li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 pt-8 text-center text-gray-400">
                <p>&copy; {{ date('Y') }} Titans Crest. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>
