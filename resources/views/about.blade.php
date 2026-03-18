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
        .mobile-menu-trigger { display: inline-flex; }
        .whatsapp-fab {
            position: fixed;
            right: 18px;
            bottom: 18px;
            width: 56px;
            height: 56px;
            border-radius: 9999px;
            background: #25d366;
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
            z-index: 90;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .whatsapp-fab:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 30px rgba(0, 0, 0, 0.35);
            color: #fff;
        }
        .whatsapp-fab svg {
            width: 30px;
            height: 30px;
        }
        @media (min-width: 768px) {
            .mobile-menu-trigger { display: none !important; }
            #mobileMenuOverlay,
            #mobileMenuPanel { display: none !important; }
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
                    <a href="/about" class="text-gray-300 transition">About</a>
                    <a href="/#packages" class="text-white hover:text-[#d4af37] transition">Packages</a>
                    <a href="/#stats" class="text-white hover:text-[#d4af37] transition">Stats</a>
                </div>
                <div class="hidden md:flex items-center space-x-4">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="modern-btn bg-[#d4af37] text-[#062a5f] px-6 py-2 font-semibold hover:bg-[#e1c263] transition">Dashboard</a>
                    @else
                        <a href="/login" class="modern-btn text-white px-5 py-2 hover:bg-white/10 transition" style="background-color: #174a8f;">Login</a>
                        <a href="/register" class="modern-btn text-white px-6 py-2 font-bold hover:bg-[#e1c263] transition" style="background-color: #d4af37;">Sign Up</a>
                    @endauth
                </div>
                <button id="mobileMenuButton" type="button" class="mobile-menu-trigger items-center justify-center w-11 h-11 rounded-lg border border-[#d4af37]/40 text-white hover:bg-white/10 transition" aria-label="Open menu" aria-expanded="false" aria-controls="mobileMenuPanel">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>
    </nav>

    <div id="mobileMenuOverlay" class="fixed inset-0 bg-black/50 md:hidden" style="z-index: 60; display: none;"></div>
    <aside id="mobileMenuPanel" class="fixed top-0 left-0 h-full w-72 max-w-[85%] bg-[#062a5f] border-r border-[#d4af37]/30 transition-transform duration-300 ease-out md:hidden" style=" background-color: #062a5f; z-index: 70; transform: translateX(-100%);">
        <div class="h-16 px-4 border-b border-[#d4af37]/30 flex items-center justify-between">
            <a href="/" class="text-white font-bold text-lg">Titans Crest</a>
            <button id="mobileMenuClose" type="button" class="w-10 h-10 inline-flex items-center justify-center text-white rounded-lg hover:bg-white/10 transition" aria-label="Close menu">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <nav class="p-4 space-y-2">
            <a href="/" class="mobile-menu-link block text-white px-4 py-3 rounded-lg hover:bg-white/10 transition">Home</a>
            <a href="/about" class="mobile-menu-link block text-white px-4 py-3 rounded-lg hover:bg-white/10 transition">About</a>
            <a href="/#packages" class="mobile-menu-link block text-white px-4 py-3 rounded-lg hover:bg-white/10 transition">Packages</a>
            <a href="/#stats" class="mobile-menu-link block text-white px-4 py-3 rounded-lg hover:bg-white/10 transition">Stats</a>
            @auth
                <a href="{{ url('/dashboard') }}" class="mobile-menu-link block mt-4 bg-[#d4af37] text-[#062a5f] text-center px-4 py-3 rounded-lg font-semibold">Dashboard</a>
            @else
                <a href="/login" class="mobile-menu-link block mt-4 text-white text-center px-4 py-3 rounded-lg border border-white/20 hover:bg-white/10 transition">Login</a>
                <a href="/register" class="mobile-menu-link block mt-2 bg-[#d4af37] text-[#062a5f] text-center px-4 py-3 rounded-lg font-bold">Sign Up</a>
            @endauth
        </nav>
    </aside>

    <!-- Hero -->
    <section class="hero-overlay pt-36 pb-20 px-4 sm:px-6 lg:px-8" style="padding-top: 150px;">
        <div class="max-w-6xl mx-auto">
            <div class="grid grid-cols-2 md:grid-cols-5 gap-8 items-center">
                <div class="lg:col-span-3 md:col-span-3 text-left">
                    <h1 class="text-4xl md:text-6xl font-extrabold text-white leading-tight mb-6">Project <span class="gradient-text">Overview</span></h1>
                    <p class="text-blue-100 text-lg leading-8 max-w-2xl">Our platform combines advanced trading strategies, algorithmic systems, and risk-managed investments to generate consistent returns. Investors can participate safely and transparently, receiving profits regularly based on their selected investment plan.</p>
                </div>
                <div class="lg:col-span-2 md:col-span-2">
                    {{-- <div class="bg-[#062a5f]/85 border border-[#d4af37]/35 rounded-2xl p-6 backdrop-blur-sm shadow-xl">
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
                    </div> --}}
                    <img src="images/about.jpg" alt="">
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
                <article class="content-card grid md:grid-cols-2 gap-8 items-stretch bg-white shadow-sm overflow-hidden">
                    <div class="p-8 md:p-10">
                        <h3 class="text-2xl md:text-3xl font-bold text-[#062a5f] mb-5">Project Overview</h3>
                        <p class="text-gray-700 leading-8">Our platform is a Digital Asset Investment Program designed to generate consistent returns through advanced cryptocurrency trading strategies and professional market analysis.</p>
                    </div>
                    <div class="min-h-64 md:min-h-full bg-[#0b2f66]">
                        <img src="/images/about-1.jpg" alt="Digital asset investment" class="w-full h-full object-cover">
                    </div>
                </article>

                <article class="content-card grid md:grid-cols-2 gap-8 items-stretch bg-white shadow-sm overflow-hidden mt-5" style="margin-top: 50px;">
                    <div class="min-h-64 md:min-h-full bg-[#0b2f66] md:order-1">
                    <img src="/images/about-2.jpg" alt="Advanced trading strategies" class="w-full h-full object-cover">
                    </div>
                    <div class="p-8 md:p-10 md:order-2">
                        <h3 class="text-2xl md:text-3xl font-bold text-[#062a5f] mb-5">Trading Strategy</h3>
                        <p class="text-gray-700 leading-8">Our team of experienced traders operates across multiple crypto exchanges, using a combination of algorithmic trading, arbitrage opportunities, and risk-managed strategies to maximize profitability while maintaining capital protection.</p>

                    </div>
                </article>

                <article class="content-card grid md:grid-cols-2 gap-8 items-stretch bg-white  shadow-sm overflow-hidden" style="margin-top: 50px;">
                    <div class="p-8 md:p-10">
                        <h3 class="text-2xl md:text-3xl font-bold text-[#062a5f] mb-5">Project Goal</h3>
                        <p class="text-gray-700 leading-8">The goal of this project is to provide investors with a reliable and transparent opportunity to participate in the rapidly growing digital asset market. Through active trading and portfolio management, the program aims to generate up to 40% monthly returns, depending on market conditions.</p>
                    </div>
                    <div class="min-h-64 md:min-h-full bg-[#0b2f66]">
                        <img src="/images/about-3.jpg" alt="Transparent investment opportunity" class="w-full h-full object-cover">
                    </div>
                </article>

                <article class="content-card grid md:grid-cols-2 gap-8 items-stretch  shadow-sm overflow-hidden">
                  
                    <div class="min-h-64 md:min-h-full bg-[#0b2f66] md:order-1">
                        <img src="/images/about-4.jpg" alt="Investment packages and regular profits" class="w-full h-full object-cover" style="margin-top: 50px;">
                    </div>
                    <div class="p-8 md:p-10 md:order-2">
                        <h3 class="text-2xl md:text-3xl font-bold text-[#062a5f] mb-5">Investor Participation</h3>
                        <p class="text-gray-700 leading-8">Investors can participate by selecting suitable investment packages and receive profits on a regular basis according to the platform’s distribution schedule.</p>
                    </div>
                </article>

                <article class="content-card grid md:grid-cols-2 gap-8 items-stretch bg-white shadow-sm overflow-hidden" style="margin-top: 50px;">
                    <div class="p-8 md:p-10">
                        <h3 class="text-2xl md:text-3xl font-bold text-[#062a5f] mb-5">Mission</h3>
                        <p class="text-gray-700 leading-8">Our mission is to build a long-term, trusted investment community by combining innovative trading technology, professional expertise, and transparent financial operations.</p>
                    </div>
                    <div class="min-h-64 md:min-h-full bg-[#0b2f66]">
                        <img src="/images/about-5.jpg" alt="Trusted long-term investment community" class="w-full h-full object-cover">
                    </div>
                </article>
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
                        <li>info@titanscrest.tech</li>
                        <li>24/7 Live Support</li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 pt-8 text-center text-gray-400">
                <p>&copy; {{ date('Y') }} Titans Crest. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        (function () {
            const menuButton = document.getElementById('mobileMenuButton');
            const menuClose = document.getElementById('mobileMenuClose');
            const menuPanel = document.getElementById('mobileMenuPanel');
            const menuOverlay = document.getElementById('mobileMenuOverlay');
            const menuLinks = document.querySelectorAll('.mobile-menu-link');

            if (!menuButton || !menuClose || !menuPanel || !menuOverlay) {
                return;
            }

            const openMenu = function () {
                menuPanel.style.transform = 'translateX(0)';
                menuOverlay.style.display = 'block';
                menuButton.setAttribute('aria-expanded', 'true');
                document.body.style.overflow = 'hidden';
            };

            const closeMenu = function () {
                menuPanel.style.transform = 'translateX(-100%)';
                menuOverlay.style.display = 'none';
                menuButton.setAttribute('aria-expanded', 'false');
                document.body.style.overflow = '';
            };

            menuButton.addEventListener('click', openMenu);
            menuClose.addEventListener('click', closeMenu);
            menuOverlay.addEventListener('click', closeMenu);
            menuLinks.forEach(function (link) {
                link.addEventListener('click', closeMenu);
            });

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    closeMenu();
                }
            });

            window.addEventListener('resize', function () {
                if (window.innerWidth >= 768) {
                    closeMenu();
                }
            });
        })();
    </script>

    @php
        $waPhoneRaw = \App\Models\Setting::get('whatsapp_number', '15551234567');
        $waPhone = preg_replace('/[^0-9]/', '', (string) $waPhoneRaw);
        if ($waPhone === '') {
            $waPhone = '15551234567';
        }
        $waText = urlencode('Hello Titans Crest Support, I need assistance.');
    @endphp
    <a href="https://wa.me/{{ $waPhone }}?text={{ $waText }}" class="whatsapp-fab" target="_blank" rel="noopener" aria-label="Chat on WhatsApp" title="Chat on WhatsApp">
        <svg viewBox="0 0 32 32" fill="currentColor" aria-hidden="true">
            <path d="M19.11 17.2c-.3-.15-1.77-.88-2.04-.98-.27-.1-.47-.15-.67.15s-.77.98-.95 1.18c-.18.2-.35.22-.65.08-.3-.15-1.28-.47-2.44-1.5-.9-.8-1.5-1.78-1.68-2.08-.18-.3-.02-.46.13-.61.13-.13.3-.35.45-.53.15-.18.2-.3.3-.5.1-.2.05-.38-.02-.53-.08-.15-.67-1.61-.92-2.2-.24-.58-.48-.5-.67-.5h-.57c-.2 0-.53.08-.8.38-.27.3-1.03 1.01-1.03 2.46s1.05 2.85 1.2 3.05c.15.2 2.06 3.15 5 4.42.7.3 1.25.47 1.67.6.7.22 1.33.19 1.83.12.56-.08 1.77-.72 2.02-1.42.25-.7.25-1.3.18-1.42-.07-.12-.27-.2-.57-.35z"></path>
            <path d="M16.02 3.2c-7.04 0-12.77 5.73-12.77 12.77 0 2.25.59 4.45 1.7 6.38L3.2 28.8l6.62-1.72a12.7 12.7 0 0 0 6.2 1.6h.01c7.04 0 12.77-5.73 12.77-12.77 0-3.41-1.33-6.61-3.75-9.02A12.7 12.7 0 0 0 16.02 3.2zm0 23.26h-.01c-1.92 0-3.8-.52-5.45-1.5l-.39-.23-3.93 1.02 1.05-3.83-.25-.4a10.5 10.5 0 0 1-1.62-5.62c0-5.8 4.72-10.52 10.53-10.52 2.8 0 5.43 1.09 7.41 3.07a10.43 10.43 0 0 1 3.08 7.44c0 5.8-4.73 10.52-10.53 10.52z"></path>
        </svg>
    </a>
</body>
</html>
