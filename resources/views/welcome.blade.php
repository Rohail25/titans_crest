<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Titans Crest - Professional BNB Investment Platform</title>
    <meta name="description" content="Join Titans Crest and grow your wealth with secure BNB staking. Earn up to 3x returns with daily profits, transparent operations, and instant withdrawals.">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        .gradient-bg { background: linear-gradient(135deg, #041a3d 0%, #0b2f66 55%, #174a8f 100%); }
        .gradient-text { background: linear-gradient(135deg, #e4bf56 0%, #d4af37 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .card-hover { transition: all 0.3s ease; }
        .card-hover:hover { transform: translateY(-8px); box-shadow: 0 20px 25px -5px rgba(2, 15, 42, 0.18), 0 10px 10px -5px rgba(2, 15, 42, 0.1); }
    </style>
</head>
<body class="bg-gray-50">
    
    <!-- Navigation -->
    <nav class="bg-[#062a5f] shadow-sm fixed w-full top-0 z-50 border-b border-[#d4af37]/30" style="background-color: #062a5f;">
        <div class="max-w-7xl bg-[#062a5f] mx-auto py-3 px-4 sm:px-6 lg:px-8" >
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    {{-- <span class="text-2xl font-bold text-blue-900">Titans <span class="gradient-text">Crest</span></span> --}}
                    <a href="/"><img src="{{ asset('images/logo.svg') }}" alt="Titans Crest" width="80" height="80"></a>
                </div>
                <div class="hidden md:flex items-center space-x-8">
                    <a href="/" class="text-gray-300 hover:text-[#d4af37] transition">Home</a>
                    <a href="/about" class="text-white hover:text-[#d4af37] transition">About</a>
                    <a href="#packages" class="text-white hover:text-[#d4af37] transition">Packages</a>
                    <a href="#stats" class="text-white hover:text-[#d4af37] transition">Stats</a>
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

    <!-- Hero Section -->
    <section
        class="gradient-bg pt-32 pb-20 px-4 sm:px-6 lg:px-8"
        style="background-image: linear-gradient(135deg, rgba(4, 26, 61, 0.86), rgba(11, 47, 102, 0.74)), url('/images/hero-section.jpg'); background-size: cover; background-position: center;"
    >
        <div class="max-w-7xl mx-auto">
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div class="text-white">
                    <h1 class="text-4xl md:text-6xl font-extrabold mb-6 leading-tight text-white">
                       THE PEAK OF <span class="gradient-text">FINANCIAL </span> POWER
                    </h1>
                    <p class="text-xl mb-8 text-blue-100">Join thousands of investors earning daily profits through our secure, transparent, and professional investment platform.</p>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="/register" class="bg-[#d4af37] text-[#062a5f] px-8 py-4 rounded-lg font-bold text-lg hover:bg-[#e1c263] transition text-center">Invest Now</a>
                        <a href="#how-it-works" class="border border-white/60 text-white px-8 py-4 rounded-lg font-bold text-lg hover:bg-white/10 transition text-center backdrop-blur-sm">Learn More</a>
                    </div>
                    
                    <!-- Trust Indicators -->
                    <div class="grid grid-cols-3 gap-6 mt-12 pt-8 border-t border-blue-700">
                        <div>
                            <div class="text-3xl font-bold gradient-text">4x</div>
                            <div class="text-sm text-blue-200">Max Returns</div>
                        </div>
                        <div>
                            <div class="text-3xl font-bold gradient-text">24/7</div>
                            <div class="text-sm text-blue-200">Support</div>
                        </div>
                        <div>
                            <div class="text-3xl font-bold gradient-text">100%</div>
                            <div class="text-sm text-blue-200">Transparent</div>
                        </div>
                    </div>
                </div>
                
                <!-- Hero Image/Illustration -->
                <div class="hidden md:block">
                    <div class="bg-[#0b2f66]/70 backdrop-blur-md rounded-2xl p-8 ">
                        {{-- <div class="space-y-4">
                            <div class="bg-gradient-to-r from-[#d4af37] to-[#b8901e] rounded-lg p-4 flex items-center justify-between">
                                <div>
                                    <div class="text-white text-sm">Total Balance</div>
                                    <div class="text-white text-2xl font-bold">$24,567.89</div>
                                </div>
                                <svg class="w-12 h-12 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                </svg>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-white bg-opacity-20 rounded-lg p-4">
                                    <div class=" text-sm">Daily Profit</div>
                                    <div class=" text-xl font-bold">+$127.45</div>
                                </div>
                                <div class="bg-white bg-opacity-20 rounded-lg p-4">
                                    <div class=" text-sm">Total Earnings</div>
                                    <div class=" text-xl font-bold">$3,456.78</div>
                                </div>
                            </div>
                        </div> --}}
                        <img src="images/hero-section.jpg" alt="">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-20 px-4 sm:px-6 lg:px-8 bg-white">
        <div class="max-w-7xl mx-auto">
            <div class="bg-white rounded-2xl border-2 border-gray-200 shadow-lg overflow-hidden">
                <div class="grid md:grid-cols-2 gap-8 items-center p-8 md:p-10">
                    <div>
                        <h2 class="text-3xl font-bold text-[#062a5f] mb-2">Welcome to Titan Crest </h2>
                        <p class="text-sm font-semibold text-[#d4af37] mb-4">Your Digital Asset Investment Platform</p>
                        <p class="text-gray-700 leading-relaxed">Titan Crest is a Digital Asset Investment Program designed to help investors grow their wealth through professional cryptocurrency trading and portfolio management.</p>
                    </div>
                    <div>
                        <img src="https://images.unsplash.com/photo-1516245834210-c4c142787335?auto=format&fit=crop&w=1200&q=80" alt="About Titans Crest" class="w-full h-64 md:h-72 object-cover rounded-xl border border-gray-200 shadow-sm">
                    </div>
                </div>
            </div>

            {{-- <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8 mt-16">
                <!-- Feature 1 -->
                <div class="card-hover bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-6 border border-blue-200">
                    <div class="bg-blue-900 w-14 h-14 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Secure Platform</h3>
                    <p class="text-gray-600">Bank-level encryption and multi-layer security protocols protect your investments 24/7.</p>
                </div>

                <!-- Feature 2 -->
                <div class="card-hover bg-gradient-to-br from-amber-50 to-amber-100 rounded-xl p-6 border border-amber-200">
                    <div class="bg-amber-500 w-14 h-14 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Daily Profits</h3>
                    <p class="text-gray-600">Earn consistent daily returns automatically credited to your account every 24 hours.</p>
                </div>

                <!-- Feature 3 -->
                <div class="card-hover bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-6 border border-green-200">
                    <div class="bg-green-600 w-14 h-14 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">3x Returns Cap</h3>
                    <p class="text-gray-600">Guaranteed earnings up to 3x your investment amount with transparent profit distribution.</p>
                </div>

                <!-- Feature 4 -->
                <div class="card-hover bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-6 border border-purple-200">
                    <div class="bg-purple-600 w-14 h-14 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Instant Withdrawals</h3>
                    <p class="text-gray-600">Quick and hassle-free withdrawal process with OTP security verification.</p>
                </div>
            </div> --}}
        </div>
    </section>

    <!-- Investment Packages -->
    <section id="packages" class="py-20 px-4 sm:px-6 lg:px-8 bg-gray-50"
    style="background-image: linear-gradient(135deg, rgba(4, 26, 61, 0.86), rgba(11, 47, 102, 0.74)), url('/images/packages.jpg'); background-size: cover; background-position: center;"
    >
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-4xl  font-bold text-white mb-4">Investment <span class="gradient-text">Packages</span></h2>
                <p class="text-xl text-white max-w-2xl mx-auto">Choose the perfect plan that matches your investment goals</p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-3 gap-6 sm:gap-8 items-start !grid">

                <!-- Starter Package -->
                <div class="bg-white rounded-2xl p-8 shadow-md border-2 border-[#d9e1ee] card-hover">
                    <div class="text-center">
                        <h3 class="text-2xl font-bold text-[#062a5f] mb-2">Starter</h3>
                        <div class="text-4xl font-extrabold text-[#0b2f66] mb-4">$50 - $200</div>
                        <p class="text-gray-600 mb-6">Entry package for new investors</p>
                    </div>
                    <ul class="space-y-3 mb-8">
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-[#d4af37] mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span class="text-gray-700">Capital: $50 to $200</span>
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-[#d4af37] mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span class="text-gray-700">Up to 2x Capital</span>
                        </li>
                    </ul>
                    <a href="/register" class="block w-full bg-[#0b2f66] text-white text-center py-3 rounded-lg font-semibold hover:bg-[#174a8f] transition" style="background-color: #0b2f66;">Choose Plan</a>
                </div>

                <!-- Standard Package -->
                <div class="bg-white rounded-2xl p-8 shadow-md border-2 border-[#d9e1ee] card-hover">
                    <div class="text-center">
                        <h3 class="text-2xl font-bold text-[#062a5f] mb-2">Standard</h3>
                        <div class="text-4xl font-extrabold text-[#0b2f66] mb-4">$500 - $5,000</div>
                        <p class="text-gray-600 mb-6">Balanced growth and flexibility</p>
                    </div>
                    <ul class="space-y-3 mb-8">
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-[#d4af37] mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span class="text-gray-700">Capital: $500 to $5,000</span>
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-[#d4af37] mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span class="text-gray-700">Up to 2x Capital</span>
                        </li>
                    </ul>
                    <a href="/register" class="block w-full bg-[#0b2f66] text-white text-center py-3 rounded-lg font-semibold hover:bg-[#174a8f] transition" style="background-color: #0b2f66;">Choose Plan</a>
                </div>

                <!-- Premium Package -->
                <div class="bg-[#0b2f66] rounded-2xl p-8 shadow-lg border-2 border-[#d4af37] card-hover relative text-white">
                    <div class="absolute top-0 right-0 bg-[#d4af37] text-[#062a5f] px-4 py-1 rounded-bl-lg rounded-tr-xl text-sm font-bold">FEATURED</div>
                    <div class="text-center">
                        <h3 class="text-2xl font-bold  mb-2">Premium</h3>
                        <div class="text-4xl font-extrabold text-[#d4af37] mb-4">$10,000+</div>
                        <p class=" mb-6">High-tier professional package</p>
                    </div>
                    <ul class="space-y-3 mb-8">
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-[#d4af37] mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span class="">Capital: $10,000 and above</span>
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-[#d4af37] mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span class="">Up to 2x Capital</span>
                        </li>
                    </ul>
                    <a href="/register" class="block w-full bg-[#d4af37]  text-center py-3 rounded-lg font-bold hover:bg-[#e1c263] transition text-white" style="background-color: #0b2f66;">Choose Plan</a>
                </div>
            </div>
        </div>
    </section>

    <!-- 10-Level Referral Plan -->
    <section class="py-20 px-4 sm:px-6 lg:px-8 gradient-bg" style="margin-top: 60px;">
        <div class="max-w-4xl mx-auto">
            <div class="text-center mb-10">
                <h2 class="text-4xl font-bold text-white mb-2">10-Level Referral Plan</h2>
                <p class="text-blue-100">Earn commissions up to 10 levels deep.</p>
            </div>

            <div class="bg-white/95 rounded-2xl shadow-lg overflow-hidden border border-[#d4af37]/40">
                <table class="w-full text-left">
                    <thead class="bg-[#062a5f] text-white">
                        <tr>
                            <th class="px-6 py-4 font-semibold text-[#d4af37]">Level</th>
                            <th class="px-6 py-4 font-semibold text-[#d4af37]">Commission</th>
                        </tr>
                    </thead>
                    <tbody class="text-white bg-[#062a5f]">
                        <tr class="border-b border-gray-200">
                            <td class="pl-4 pr-6 py-4">1</td>
                            <td class="pl-4 pr-6 py-4">7%</td>
                        </tr>
                        <tr class="border-b border-gray-200">
                            <td class="pl-4 pr-6 py-4">2</td>
                            <td class="pl-4 pr-6 py-4">4%</td>
                        </tr>
                        <tr class="border-b border-gray-200">
                            <td class="pl-4 pr-6 py-4">3</td>
                            <td class="pl-4 pr-6 py-4">2%</td>
                        </tr>
                        <tr>
                            <td class="pl-4 pr-6 py-4">4</td>
                            <td class="pl-4 pr-6 py-4">1%</td>
                        </tr>
                        <tr>
                            <td class="pl-4 pr-6 py-4">5</td>
                            <td class="pl-4 pr-6 py-4">1%</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="text-center mt-6">
                <a
                    href="{{ asset('files/Titans%20Crest.pdf') }}"
                    download="Titans Crest.pdf"
                    class="inline-flex items-center bg-[#d4af37] text-[#062a5f] px-6 py-3 rounded-lg font-bold hover:bg-[#e1c263] transition shadow-md"
                 style="background-color: #d4af37; color: #062a5f; margin-top: 20px;">
                    Download Referral Plan PDF
                </a>
            </div>
        </div>
    </section>

    <!-- How It Works -->
    {{-- <section id="how-it-works" class="py-20 px-4 sm:px-6 lg:px-8 bg-white">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">How It <span class="gradient-text">Works</span></h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">Start earning in 4 simple steps</p>
            </div>
            
            <div class="grid md:grid-cols-4 gap-8">
                <div class="text-center">
                    <div class="bg-blue-900 w-16 h-16 rounded-full flex items-center justify-center text-white text-2xl font-bold mx-auto mb-4">1</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Sign Up</h3>
                    <p class="text-gray-600">Create your account in less than 2 minutes with email verification</p>
                </div>
                <div class="text-center">
                    <div class="bg-amber-500 w-16 h-16 rounded-full flex items-center justify-center text-white text-2xl font-bold mx-auto mb-4">2</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Choose Package</h3>
                    <p class="text-gray-600">Select an investment package that fits your financial goals</p>
                </div>
                <div class="text-center">
                    <div class="bg-green-600 w-16 h-16 rounded-full flex items-center justify-center text-white text-2xl font-bold mx-auto mb-4">3</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Deposit BNB</h3>
                    <p class="text-gray-600">Fund your investment with secure BNB blockchain transactions</p>
                </div>
                <div class="text-center">
                    <div class="bg-purple-600 w-16 h-16 rounded-full flex items-center justify-center text-white text-2xl font-bold mx-auto mb-4">4</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Earn Daily</h3>
                    <p class="text-gray-600">Watch your profits grow automatically every 24 hours</p>
                </div>
            </div>
        </div>
    </section> --}}

    <!-- Stats Section -->
    <section id="stats" class="py-20 px-4 sm:px-6 lg:px-8 gradient-bg" style="margin-top:50px">
        <div class="max-w-7xl mx-auto">
            <div class="grid md:grid-cols-4 gap-8 text-center text-white">
                <div>
                    <div class="text-5xl font-extrabold gradient-text mb-2">5,000+</div>
                    <div class="text-xl text-blue-100">Active Investors</div>
                </div>
                <div>
                    <div class="text-5xl font-extrabold gradient-text mb-2">$12M+</div>
                    <div class="text-xl text-blue-100">Total Invested</div>
                </div>
                <div>
                    <div class="text-5xl font-extrabold gradient-text mb-2">$3.6M+</div>
                    <div class="text-xl text-blue-100">Paid in Profits</div>
                </div>
                <div>
                    <div class="text-5xl font-extrabold gradient-text mb-2">99.9%</div>
                    <div class="text-xl text-blue-100">Uptime</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="py-20 px-4 sm:px-6 lg:px-8 bg-white">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">What Our <span class="gradient-text">Investors Say</span></h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">Real success stories from real investors on our platform</p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-8">
                <!-- Testimonial 1 -->
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl p-8 border border-blue-200 card-hover">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-blue-900 rounded-full flex items-center justify-center text-white font-bold">JM</div>
                        <div class="ml-4">
                            <h4 class="font-bold text-gray-900">John Mitchell</h4>
                            <p class="text-sm text-gray-600">Verified Investor</p>
                        </div>
                    </div>
                    <p class="text-gray-700 mb-4">"I've been investing with Titans Crest for 6 months now and the returns have exceeded my expectations. The platform is transparent, secure, and the daily profits are consistently credited."</p>
                    <div class="text-amber-400 text-lg">★★★★★</div>
                </div>

                <!-- Testimonial 2 -->
                <div class="bg-gradient-to-br from-amber-50 to-amber-100 rounded-2xl p-8 border border-amber-200 card-hover">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-amber-500 rounded-full flex items-center justify-center text-white font-bold">SR</div>
                        <div class="ml-4">
                            <h4 class="font-bold text-gray-900">Sarah Rodriguez</h4>
                            <p class="text-sm text-gray-600">Professional Investor</p>
                        </div>
                    </div>
                    <p class="text-gray-700 mb-4">"As someone who has invested in various platforms, Titans Crest stands out with its clarity and customer support. The withdrawal process is quick and I've never had any issues."</p>
                    <div class="text-amber-400 text-lg">★★★★★</div>
                </div>

                <!-- Testimonial 3 -->
                <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-2xl p-8 border border-green-200 card-hover">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-green-600 rounded-full flex items-center justify-center text-white font-bold">AK</div>
                        <div class="ml-4">
                            <h4 class="font-bold text-gray-900">Ahmed Khan</h4>
                            <p class="text-sm text-gray-600">Elite Member</p>
                        </div>
                    </div>
                    <p class="text-gray-700 mb-4">"I made the Elite investment and the dedicated manager assigned to my account is fantastic. They provide regular updates and have helped optimize my earnings significantly."</p>
                    <div class="text-amber-400 text-lg">★★★★★</div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="py-20 px-4 sm:px-6 lg:px-8 bg-gray-50">
        <div class="max-w-3xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Frequently Asked <span class="gradient-text">Questions</span></h2>
                <p class="text-xl text-gray-600">Find answers to common questions about our platform</p>
            </div>
            
            <div class="space-y-4">
                <!-- FAQ 1 -->
                <div class="bg-white rounded-xl border-2 border-gray-200 overflow-hidden faq-item">
                    <button class="w-full px-6 py-4 text-left font-semibold text-gray-900 flex justify-between items-center hover:bg-gray-50 transition faq-button">
                        <span>Is my investment safe on Titans Crest?</span>
                        <span class="text-blue-900 text-xl">+</span>
                    </button>
                    <div class="px-6 py-4 bg-gray-50 hidden faq-content">
                        <p class="text-gray-700">Yes, absolutely. We use bank-level encryption, multi-layer security protocols, and our blockchain transactions are transparent and auditable. Your BNB investment is secured with smart contracts.</p>
                    </div>
                </div>

                <!-- FAQ 2 -->
                <div class="bg-white rounded-xl border-2 border-gray-200 overflow-hidden faq-item">
                    <button class="w-full px-6 py-4 text-left font-semibold text-gray-900 flex justify-between items-center hover:bg-gray-50 transition faq-button">
                        <span>When do I receive my daily profits?</span>
                        <span class="text-blue-900 text-xl">+</span>
                    </button>
                    <div class="px-6 py-4 bg-gray-50 hidden faq-content">
                        <p class="text-gray-700">Daily profits are calculated and automatically credited to your account wallet every 24 hours. You can choose to withdraw or reinvest these profits to take advantage of compound growth.</p>
                    </div>
                </div>

                <!-- FAQ 3 -->
                <div class="bg-white rounded-xl border-2 border-gray-200 overflow-hidden faq-item">
                    <button class="w-full px-6 py-4 text-left font-semibold text-gray-900 flex justify-between items-center hover:bg-gray-50 transition faq-button">
                        <span>What is the 3x returns cap?</span>
                        <span class="text-blue-900 text-xl">+</span>
                    </button>
                    <div class="px-6 py-4 bg-gray-50 hidden faq-content">
                        <p class="text-gray-700">The 3x cap means your investment will earn up to three times its initial amount in total profits. For example, a $1,000 investment can earn up to $3,000 in profits. After reaching this cap, your investment is still safe and can be withdrawn.</p>
                    </div>
                </div>

                <!-- FAQ 4 -->
                <div class="bg-white rounded-xl border-2 border-gray-200 overflow-hidden faq-item">
                    <button class="w-full px-6 py-4 text-left font-semibold text-gray-900 flex justify-between items-center hover:bg-gray-50 transition faq-button">
                        <span>How do I withdraw my funds?</span>
                        <span class="text-blue-900 text-xl">+</span>
                    </button>
                    <div class="px-6 py-4 bg-gray-50 hidden faq-content">
                        <p class="text-gray-700">Withdrawals are quick and secure. Simply navigate to your wallet, request withdrawal, and verify with OTP. Most withdrawals are processed within minutes to your BNB wallet address. No hidden fees or delays.</p>
                    </div>
                </div>

                <!-- FAQ 5 -->
                <div class="bg-white rounded-xl border-2 border-gray-200 overflow-hidden faq-item">
                    <button class="w-full px-6 py-4 text-left font-semibold text-gray-900 flex justify-between items-center hover:bg-gray-50 transition faq-button">
                        <span>Can I refer friends and earn commissions?</span>
                        <span class="text-blue-900 text-xl">+</span>
                    </button>
                    <div class="px-6 py-4 bg-gray-50 hidden faq-content">
                        <p class="text-gray-700">Yes! Our referral program allows you to earn commissions when friends sign up using your referral code. You'll receive a percentage of their first investment as a bonus, with no limit on how many people you can refer.</p>
                    </div>
                </div>

                <!-- FAQ 6 -->
                <div class="bg-white rounded-xl border-2 border-gray-200 overflow-hidden faq-item">
                    <button class="w-full px-6 py-4 text-left font-semibold text-gray-900 flex justify-between items-center hover:bg-gray-50 transition faq-button">
                        <span>What's the minimum investment amount?</span>
                        <span class="text-blue-900 text-xl">+</span>
                    </button>
                    <div class="px-6 py-4 bg-gray-50 hidden faq-content">
                        <p class="text-gray-700">The minimum investment is $50 for our Basic package. We offer plans for every level, from $50 starter-friendly options up to our Elite package starting at $5,000.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    

    <!-- Security & Trust Section -->
    {{-- <section class="py-20 px-4 sm:px-6 lg:px-8 bg-white">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Security &amp; <span class="gradient-text">Trust</span></h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">Your security is our top priority. Learn how we protect your investments</p>
            </div>
            
            <div class="grid md:grid-cols-2 gap-12 items-center mb-16">
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl p-8 border-2 border-blue-200">
                    <h3 class="text-2xl font-bold text-gray-900 mb-6">Security Features</h3>
                    <ul class="space-y-4">
                        <li class="flex items-start">
                            <div class="bg-blue-900 rounded-full p-2 mr-4 mt-1 flex-shrink-0">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <span class="text-gray-700"><strong>SSL/TLS Encryption:</strong> All data is encrypted during transmission</span>
                        </li>
                        <li class="flex items-start">
                            <div class="bg-blue-900 rounded-full p-2 mr-4 mt-1 flex-shrink-0">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <span class="text-gray-700"><strong>OTP Verification:</strong> Two-factor authentication for all transactions</span>
                        </li>
                        <li class="flex items-start">
                            <div class="bg-blue-900 rounded-full p-2 mr-4 mt-1 flex-shrink-0">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <span class="text-gray-700"><strong>Smart Contracts:</strong> Blockchain-based transactions are auditable and transparent</span>
                        </li>
                        <li class="flex items-start">
                            <div class="bg-blue-900 rounded-full p-2 mr-4 mt-1 flex-shrink-0">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <span class="text-gray-700"><strong>Regular Audits:</strong> Third-party security audits conducted quarterly</span>
                        </li>
                        <li class="flex items-start">
                            <div class="bg-blue-900 rounded-full p-2 mr-4 mt-1 flex-shrink-0">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <span class="text-gray-700"><strong>Cold Storage:</strong> Majority of funds held in secure cold storage wallets</span>
                        </li>
                        <li class="flex items-start">
                            <div class="bg-blue-900 rounded-full p-2 mr-4 mt-1 flex-shrink-0">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <span class="text-gray-700"><strong>24/7 Monitoring:</strong> Real-time surveillance for suspicious activities</span>
                        </li>
                    </ul>
                </div>
                
                <div class="space-y-8">
                    <!-- Trust Indicator 1 -->
                    <div class="bg-white rounded-2xl p-6 border-2 border-amber-200 text-center transform hover:scale-105 transition">
                        <div class="text-5xl mb-3">🛡️</div>
                        <h4 class="text-xl font-bold text-gray-900 mb-2">DeFi Compliant</h4>
                        <p class="text-gray-600 text-sm">Full compliance with DeFi standards and blockchain protocols</p>
                    </div>
                    
                    <!-- Trust Indicator 2 -->
                    <div class="bg-white rounded-2xl p-6 border-2 border-blue-200 text-center transform hover:scale-105 transition">
                        <div class="text-5xl mb-3">✅</div>
                        <h4 class="text-xl font-bold text-gray-900 mb-2">Verified Platform</h4>
                        <p class="text-gray-600 text-sm">Verified on multiple blockchain explorers and rating platforms</p>
                    </div>
                    
                    <!-- Trust Indicator 3 -->
                    <div class="bg-white rounded-2xl p-6 border-2 border-green-200 text-center transform hover:scale-105 transition">
                        <div class="text-5xl mb-3">🔐</div>
                        <h4 class="text-xl font-bold text-gray-900 mb-2">Insured Deposits</h4>
                        <p class="text-gray-600 text-sm">Selected deposits covered by insurance for additional protection</p>
                    </div>
                </div>
            </div>
        </div>
    </section> --}}

    <!-- CTA Section -->
    <section class="py-20 px-4 sm:px-6 lg:px-8 bg-gray-50">
        <div class="max-w-4xl mx-auto text-center">
            <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6">Ready to Start <span class="gradient-text">Growing Your Wealth?</span></h2>
            <p class="text-xl text-gray-600 mb-8">Join thousands of successful investors today and secure your financial future</p>
            <a href="/register" class="inline-block bg-gradient-to-r from-amber-500 to-amber-600 text-white px-12 py-4 rounded-lg font-bold text-lg hover:from-amber-600 hover:to-amber-700 transition shadow-lg">Create Free Account</a>
        </div>
    </section>

    <script>
        // FAQ Accordion functionality
        const faqButtons = document.querySelectorAll('.faq-button');
        faqButtons.forEach(button => {
            button.addEventListener('click', function() {
                const faqItem = this.closest('.faq-item');
                const faqContent = faqItem.querySelector('.faq-content');
                const isOpen = !faqContent.classList.contains('hidden');
                
                // Close all other FAQs
                document.querySelectorAll('.faq-content').forEach(content => {
                    content.classList.add('hidden');
                });
                document.querySelectorAll('.faq-button span:last-child').forEach(span => {
                    span.textContent = '+';
                });
                
                // Toggle current FAQ
                if (!isOpen) {
                    faqContent.classList.remove('hidden');
                    this.querySelector('span:last-child').textContent = '−';
                }
            });
        });
    </script>

    <!-- Footer -->
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
                        <li><a href="#features" class="hover:text-amber-400 transition">Features</a></li>
                        <li><a href="#packages" class="hover:text-amber-400 transition">Packages</a></li>
                        <li><a href="#how-it-works" class="hover:text-amber-400 transition">How It Works</a></li>
                        <li><a href="#stats" class="hover:text-amber-400 transition">Statistics</a></li>
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