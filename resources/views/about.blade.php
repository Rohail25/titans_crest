<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>About - Titans Crest</title>
    <meta name="description" content="Learn about the Titans Crest digital asset investment project and mission.">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Inter', sans-serif; }
        .gradient-bg { background: linear-gradient(135deg, #041a3d 0%, #0b2f66 55%, #174a8f 100%); }
        .gradient-text { background: linear-gradient(135deg, #e4bf56 0%, #d4af37 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .hero-overlay {
            background-image:
                radial-gradient(circle at 18% 20%, rgba(212, 175, 55, 0.16), transparent 30%),
                radial-gradient(circle at 84% 24%, rgba(118, 165, 255, 0.14), transparent 24%),
                linear-gradient(135deg, rgba(4, 26, 61, 0.9), rgba(11, 47, 102, 0.78)),
                url('/images/hero-section.jpg');
            background-size: cover;
            background-position: center;
        }
        .section-divider {
            height: 1px;
            background: linear-gradient(90deg, rgba(4, 26, 61, 0), rgba(4, 26, 61, 0.22), rgba(4, 26, 61, 0));
        }
        .content-card {
            transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
        }
        .content-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 14px 30px -16px rgba(4, 26, 61, 0.4);
            border-color: rgba(212, 175, 55, 0.45);
        }
        .modern-btn {
            border-radius: 12px;
            box-shadow: 0 8px 18px -10px rgba(4, 26, 61, 0.55);
            transition: transform 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
        }
        .modern-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 14px 28px -14px rgba(4, 26, 61, 0.62);
        }
    </style>
</head>
<body class="bg-gray-50">

    <!-- Navigation -->
    <nav class="bg-[#062a5f] shadow-sm fixed w-full top-0 z-50 border-b border-[#d4af37]/30" style="background-color: #062a5f;">
        <div class="max-w-7xl bg-[#062a5f] mx-auto py-3 px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <a href="/"><img src="/images/logo.svg" alt="Titans Crest" width="80" height="80"></a>
                </div>
                <div class="hidden md:flex items-center space-x-8">
                    <a href="/" class="text-white hover:text-[#d4af37] transition">Home</a>
                    <a href="/about" class="text-[#d4af37] transition">About</a>
                    <a href="/#packages" class="text-white hover:text-[#d4af37] transition">Packages</a>
                    <a href="/#stats" class="text-white hover:text-[#d4af37] transition">Stats</a>
                </div>
                <div class="flex items-center space-x-4">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="modern-btn bg-[#d4af37] text-[#062a5f] px-6 py-2 font-semibold hover:bg-[#e1c263] transition">Dashboard</a>
                    @else
                        <a href="/login" class="modern-btn text-white px-5 py-2 hover:bg-white/10 transition" style="background-color: #174a8f;">Login</a>
                        <a href="/register" class="modern-btn text-white px-6 py-2 font-bold hover:bg-[#e1c263] transition" style="background-color: #d4af37;">Sign Up</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero -->
    <section class="hero-overlay pt-36 pb-20 px-4 sm:px-6 lg:px-8">
        <div class="max-w-6xl mx-auto">
            <div class="grid lg:grid-cols-5 gap-8 items-center">
                <div class="lg:col-span-3 text-left">
                    <p class="inline-flex items-center text-[#d4af37] bg-[#041a3d]/45 border border-[#d4af37]/35 rounded-full px-4 py-1.5 font-semibold tracking-[0.14em] uppercase text-xs mb-5">About Titans Crest</p>
                    <h1 class="text-4xl md:text-6xl font-extrabold text-white leading-tight mb-6">Project <span class="gradient-text">Overview</span></h1>
                    <p class="text-blue-100 text-lg leading-8 max-w-2xl">Digital asset investment backed by advanced trading systems, professional market execution, and transparent long-term operations.</p>
                </div>
                <div class="lg:col-span-2">
                    <div class="bg-[#062a5f]/85 border border-[#d4af37]/35 rounded-2xl p-6 backdrop-blur-sm shadow-xl">
                        <p class="text-[#d4af37] text-sm font-semibold mb-4 uppercase tracking-wide">Program Highlights</p>
                        <div class="space-y-4 text-white">
                            <div class="flex items-center justify-between border-b border-white/10 pb-3">
                                <span class="text-blue-100 inline-flex items-center gap-2">
                                    <svg class="w-4 h-4 text-[#d4af37]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 12h16M12 4v16"></path></svg>
                                    Trading Model
                                </span>
                                <span class="font-semibold">Algorithmic + Arbitrage</span>
                            </div>
                            <div class="flex items-center justify-between border-b border-white/10 pb-3">
                                <span class="text-blue-100 inline-flex items-center gap-2">
                                    <svg class="w-4 h-4 text-[#d4af37]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-2.761 0-5 1.79-5 4s2.239 4 5 4 5-1.79 5-4-2.239-4-5-4z"></path></svg>
                                    Capital Focus
                                </span>
                                <span class="font-semibold">Risk Managed</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-blue-100 inline-flex items-center gap-2">
                                    <svg class="w-4 h-4 text-[#d4af37]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                                    Target Performance
                                </span>
                                <span class="font-semibold">Up to 40% Monthly</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Overview Content -->
    <section class="py-20 px-4 sm:px-6 lg:px-8 bg-[#f5f7fb]">
        <div class="max-w-6xl mx-auto">
            <div class="mb-10 md:mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-[#062a5f] mb-3">Project Narrative</h2>
                <p class="text-gray-600 text-lg leading-8">A structured digital asset investment program designed for consistency, transparency, and long-term investor confidence.</p>
                <div class="section-divider mt-8"></div>
            </div>

            <div class="space-y-8 md:space-y-10">
                <article class="content-card grid md:grid-cols-2 gap-8 items-stretch bg-white rounded-2xl border border-[#dce4f1] shadow-sm overflow-hidden">
                    <div class="p-8 md:p-10">
                        <span class="inline-block text-xs font-semibold tracking-wider uppercase text-[#d4af37] mb-4">Section 01</span>
                        <h3 class="text-2xl md:text-3xl font-bold text-[#062a5f] mb-5">Project Overview</h3>
                        <p class="text-gray-700 leading-8">Our platform is a Digital Asset Investment Program designed to generate consistent returns through advanced cryptocurrency trading strategies and professional market analysis.</p>
                    </div>
                    <div class="min-h-64 md:min-h-full bg-[#0b2f66]">
                        <img src="/images/hero-section.jpg" alt="Digital asset investment" class="w-full h-full object-cover">
                    </div>
                </article>

                <article class="content-card grid md:grid-cols-2 gap-8 items-stretch bg-white rounded-2xl border border-[#dce4f1] shadow-sm overflow-hidden">
                    <div class="p-8 md:p-10 md:order-2">
                        <span class="inline-block text-xs font-semibold tracking-wider uppercase text-[#d4af37] mb-4">Section 02</span>
                        <h3 class="text-2xl md:text-3xl font-bold text-[#062a5f] mb-5">Trading Strategy</h3>
                        <p class="text-gray-700 leading-8">Our team of experienced traders operates across multiple crypto exchanges, using a combination of algorithmic trading, arbitrage opportunities, and risk-managed strategies to maximize profitability while maintaining capital protection.</p>
                    </div>
                    <div class="min-h-64 md:min-h-full bg-[#0b2f66] md:order-1">
                        <img src="/images/hero-section.jpg" alt="Advanced trading strategies" class="w-full h-full object-cover">
                    </div>
                </article>

                <article class="content-card grid md:grid-cols-2 gap-8 items-stretch bg-white rounded-2xl border border-[#dce4f1] shadow-sm overflow-hidden">
                    <div class="p-8 md:p-10">
                        <span class="inline-block text-xs font-semibold tracking-wider uppercase text-[#d4af37] mb-4">Section 03</span>
                        <h3 class="text-2xl md:text-3xl font-bold text-[#062a5f] mb-5">Project Goal</h3>
                        <p class="text-gray-700 leading-8">The goal of this project is to provide investors with a reliable and transparent opportunity to participate in the rapidly growing digital asset market. Through active trading and portfolio management, the program aims to generate up to 40% monthly returns, depending on market conditions.</p>
                    </div>
                    <div class="min-h-64 md:min-h-full bg-[#0b2f66]">
                        <img src="/images/hero-section.jpg" alt="Transparent investment opportunity" class="w-full h-full object-cover">
                    </div>
                </article>

                <article class="content-card grid md:grid-cols-2 gap-8 items-stretch bg-white rounded-2xl border border-[#dce4f1] shadow-sm overflow-hidden">
                    <div class="p-8 md:p-10 md:order-2">
                        <span class="inline-block text-xs font-semibold tracking-wider uppercase text-[#d4af37] mb-4">Section 04</span>
                        <h3 class="text-2xl md:text-3xl font-bold text-[#062a5f] mb-5">Investor Participation</h3>
                        <p class="text-gray-700 leading-8">Investors can participate by selecting suitable investment packages and receive profits on a regular basis according to the platform’s distribution schedule.</p>
                    </div>
                    <div class="min-h-64 md:min-h-full bg-[#0b2f66] md:order-1">
                        <img src="/images/hero-section.jpg" alt="Investment packages and regular profits" class="w-full h-full object-cover">
                    </div>
                </article>

                <article class="content-card grid md:grid-cols-2 gap-8 items-stretch bg-white rounded-2xl border border-[#dce4f1] shadow-sm overflow-hidden">
                    <div class="p-8 md:p-10">
                        <span class="inline-block text-xs font-semibold tracking-wider uppercase text-[#d4af37] mb-4">Section 05</span>
                        <h3 class="text-2xl md:text-3xl font-bold text-[#062a5f] mb-5">Mission</h3>
                        <p class="text-gray-700 leading-8">Our mission is to build a long-term, trusted investment community by combining innovative trading technology, professional expertise, and transparent financial operations.</p>
                    </div>
                    <div class="min-h-64 md:min-h-full bg-[#0b2f66]">
                        <img src="/images/hero-section.jpg" alt="Trusted long-term investment community" class="w-full h-full object-cover">
                    </div>
                </article>
            </div>
        </div>
    </section>

    <footer class="bg-[#041a3d] text-white py-10 px-4 sm:px-6 lg:px-8 border-t border-[#d4af37]/30">
        <div class="max-w-6xl mx-auto text-center text-gray-300">
            <p>&copy; {{ date('Y') }} Titans Crest. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
