@include('partials.header')

<body class="min-h-screen font-main bg-bluebody relative">
    <!-- Full-Screen Background Video -->
    <div class="fixed top-0 left-0 w-full h-full z-0 overflow-hidden">
        <video id="bgVideo"
            class="absolute top-1/2 left-1/2 min-w-full min-h-full w-auto h-auto transform -translate-x-1/2 -translate-y-1/2 object-cover"
            autoplay muted playsinline>
            <source src="{{ asset('videos/1.mp4') }}" type="video/mp4">
        </video>
        <div class="absolute inset-0 bg-black opacity-50"></div>
        <div class="absolute inset-0 bg-gradient-to-b from-transparent via-transparent to-blue opacity-60"></div>
    </div>

    <!-- Content Layer -->
    <div class="relative z-10">
        @include('partials.navigation')

        <main class="min-h-screen">
            <!-- Hero Section -->
            <section class="pt-32 pb-20 px-4 sm:px-6 lg:px-8">
                <div class="max-w-4xl mx-auto text-center">
                    <h1 class="text-5xl md:text-7xl font-bold text-white mb-6 drop-shadow-lg">
                        Welcome to GReAT System
                    </h1>
                    <p class="text-xl md:text-2xl text-gray-100 mb-10 drop-shadow-md">
                        Where you can experience faster work, accurate results, reliable service and more!
                    </p>
                    <div class="flex gap-4 justify-center items-center flex-col">
                        <a href="{{ route('login') }}"
                            class="px-8 py-4 bg-yellow hover:bg-lumot border-2 border-white text-green font-semibold rounded-lg shadow-xl transition-all duration-300 hover:scale-105">
                            Login to your Account
                        </a>
                        <span class="italic text-xs text-white/50">Only for Government Use</span>
                    </div>
                </div>
            </section>

            <!-- Bento Grid Features Section -->
            <section class="py-20 px-4 sm:px-6 lg:px-8">
                <div class="max-w-7xl mx-auto">
                    <h2 class="text-4xl md:text-5xl font-bold text-white text-center mb-16 drop-shadow-lg">
                        Why Choose GReAT?
                    </h2>

                    <!-- Bento Grid Layout -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 auto-rows-fr">
                        <!-- Large Card - Boost Income -->
                        <div
                            class="md:col-span-2 bg-white/90 backdrop-blur-sm rounded-3xl p-8 hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 group">
                            <div class="flex flex-col md:flex-row items-center gap-6">
                                <div class="md:w-[20%] w-full flex items-start justify-start">
                                    <div>
                                        <lord-icon src="https://cdn.lordicon.com/bsdkzyjd.json" trigger="loop"
                                            colors="primary:#10454F,secondary:#BDE038" style="width:120px;height:120px">
                                        </lord-icon>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-2xl md:text-4xl font-bold text-green mb-3">Boost Your Income</h3>
                                    <p class="text-gray text-base md:text-lg leading-relaxed">
                                        Increase productivity and revenue using our advanced system. Work faster and
                                        more efficiently than traditional methods, maximizing your earning potential.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Small Card - Secured Data -->
                        <div
                            class="bg-gradient-to-br from-logo-blue to-logo-teal text-white rounded-3xl p-8 hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 group">
                            <div class="flex-shrink-0 md:w-[20%] w-full flex items-start justify-start mb-4">
                                <div>
                                    <lord-icon src="https://cdn.lordicon.com/apbwvyeg.json" trigger="loop"
                                        colors="primary:#fff,secondary:#BDE038" style="width:120px;height:120px">
                                    </lord-icon>
                                </div>
                            </div>
                            <h3 class="text-2xl md:text-3xl font-bold mb-3">Secured Data</h3>
                            <p class="text-white/90 text-base md:text-lg leading-relaxed">
                                Enterprise-grade encryption protects your sensitive information from unauthorized
                                access.
                            </p>
                        </div>

                        <!-- Small Card - Convenient Transactions -->
                        <div
                            class="bg-white/90 backdrop-blur-sm rounded-3xl p-8 hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 group">
                            <div class="mb-4">
                                <lord-icon src="https://cdn.lordicon.com/jectmwqf.json" trigger="loop"
                                    colors="primary:#10454F,secondary:#BDE038" style="width:100px;height:100px">
                                </lord-icon>
                            </div>
                            <h3 class="text-2xl md:text-3xl font-bold text-green mb-3">Convenient Transactions</h3>
                            <p class="text-gray text-base md:text-lg leading-relaxed">
                                Our system provides online payment for clients who prefer remote transactions.
                            </p>
                        </div>

                        <!-- Medium Card - Fast Processing -->
                        <div
                            class="md:col-span-2 bg-gradient-to-r from-logo-green via-lumot to-logo-teal text-white rounded-3xl p-8 hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 group">
                            <div class="flex items-center gap-6 flex-col md:flex-row">
                                <div class="md:w-[20%] w-full flex items-start justify-start">
                                    <div>
                                        <lord-icon src="https://cdn.lordicon.com/warimioc.json" trigger="loop"
                                            colors="primary:#ffffff,secondary:#10454F" style="width:100px;height:100px">
                                        </lord-icon>
                                    </div>
                                </div>
                                <div>
                                    <h3 class="text-2xl md:text-5xl font-bold mb-3">Lightning Fast Processing</h3>
                                    <p class="text-white/90 text-base md:text-xl leading-relaxed">
                                        Transform hours of work into minutes with our optimized workflows and automated
                                        systems designed for maximum efficiency.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Stats Section -->
            <section class="py-24 px-4 sm:px-6 lg:px-8">
                <div class="max-w-7xl mx-auto">
                    <div class="text-center mb-16">
                        <h2 class="text-5xl md:text-6xl font-bold text-white mb-4 drop-shadow-2xl">
                            Transforming Government Services
                        </h2>
                        <p class="text-xl md:text-2xl text-gray-100 max-w-3xl mx-auto drop-shadow-lg">
                            Join thousands experiencing the future of efficient governance
                        </p>
                    </div>

                    <!-- Bento Stats Grid -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-12">
                        <div
                            class="bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl p-6 hover:bg-white/20 transition-all duration-300">
                            <div class="text-logo-green text-4xl md:text-5xl font-bold mb-2 counter"
                                data-target="15000">
                                0</div>
                            <p class="text-white font-semibold">Active Users</p>
                            <p class="text-gray-300 text-sm">Across LGUs</p>
                        </div>

                        <div
                            class="bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl p-6 hover:bg-white/20 transition-all duration-300">
                            <div class="text-logo-green text-4xl md:text-5xl font-bold mb-2 counter" data-target="98">0
                            </div>
                            <p class="text-white font-semibold">Success Rate</p>
                            <p class="text-gray-300 text-sm">Accuracy</p>
                        </div>

                        <div
                            class="bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl p-6 hover:bg-white/20 transition-all duration-300">
                            <div class="text-logo-green text-4xl md:text-5xl font-bold mb-2 counter" data-target="75">0
                            </div>
                            <p class="text-white font-semibold">Time Saved</p>
                            <p class="text-gray-300 text-sm">Average</p>
                        </div>

                        <div
                            class="bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl p-6 hover:bg-white/20 transition-all duration-300">
                            <div class="text-logo-green text-4xl md:text-5xl font-bold mb-2">24/7</div>
                            <p class="text-white font-semibold">Availability</p>
                            <p class="text-gray-300 text-sm">Always On</p>
                        </div>
                    </div>

                    <!-- Trust Section -->
                    <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-3xl p-10">
                        <h3 class="text-3xl font-bold text-white text-center mb-8">Trusted Partners</h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                            <div
                                class="bg-white/5 rounded-xl p-6 text-center hover:bg-white/10 transition-all duration-300">
                                <span class="text-white/80 font-semibold">LGU Partner 1</span>
                            </div>
                            <div
                                class="bg-white/5 rounded-xl p-6 text-center hover:bg-white/10 transition-all duration-300">
                                <span class="text-white/80 font-semibold">LGU Partner 2</span>
                            </div>
                            <div
                                class="bg-white/5 rounded-xl p-6 text-center hover:bg-white/10 transition-all duration-300">
                                <span class="text-white/80 font-semibold">LGU Partner 3</span>
                            </div>
                            <div
                                class="bg-white/5 rounded-xl p-6 text-center hover:bg-white/10 transition-all duration-300">
                                <span class="text-white/80 font-semibold">LGU Partner 4</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- CTA Section -->
            <section class="py-20 px-4 sm:px-6 lg:px-8">
                <div class="max-w-4xl mx-auto">
                    <div
                        class="bg-gradient-to-r from-logo-blue via-logo-teal to-logo-green rounded-3xl p-12 text-center shadow-2xl">
                        <h2 class="text-4xl md:text-5xl font-bold text-white mb-6">
                            Ready to Get Started?
                        </h2>
                        <p class="text-xl text-white/90 mb-8">
                            Transform your workflow today with GReAT System
                        </p>
                        <div class="flex gap-4 justify-center flex-wrap">
                            <button
                                class="px-8 py-4 bg-white text-logo-blue font-semibold rounded-lg hover:bg-logo-green hover:text-white transition-all duration-300 hover:scale-105 shadow-lg">
                                Contact Us
                            </button>
                            <button
                                class="px-8 py-4 bg-transparent border-2 border-white text-white font-semibold rounded-lg hover:bg-white/10 transition-all duration-300">
                                Learn More
                            </button>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>

    @include('partials.footer')

    <script>
        // Video playlist
        const videos = [
            "{{ asset('videos/1.mp4') }}",
            "{{ asset('videos/2.mp4') }}",
            "{{ asset('videos/3.mp4') }}"
        ];

        let currentVideoIndex = 0;
        const video = document.getElementById('bgVideo');

        video.addEventListener('ended', function() {
            currentVideoIndex = (currentVideoIndex + 1) % videos.length;
            video.src = videos[currentVideoIndex];
            video.load();
            video.play();
        });

        video.play().catch(function(error) {
            console.log("Autoplay prevented:", error);
        });

        // Counter animation
        function animateCounter(element) {
            const target = parseInt(element.getAttribute('data-target'));
            const duration = 2000;
            const increment = target / (duration / 16);
            let current = 0;

            const updateCounter = () => {
                current += increment;
                if (current < target) {
                    element.textContent = Math.floor(current).toLocaleString();
                    requestAnimationFrame(updateCounter);
                } else {
                    const text = element.parentElement.querySelector('p').textContent;
                    element.textContent = target.toLocaleString() + (text.includes('Rate') || text.includes(
                        'Saved') ? '%' : '+');
                }
            };
            updateCounter();
        }

        const observerOptions = {
            threshold: 0.5,
            rootMargin: '0px 0px -100px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const counters = entry.target.querySelectorAll('.counter');
                    counters.forEach(counter => {
                        if (counter.textContent === '0') {
                            animateCounter(counter);
                        }
                    });
                }
            });
        }, observerOptions);

        const statsSection = document.querySelector('section:nth-of-type(3)');
        if (statsSection) {
            observer.observe(statsSection);
        }
    </script>
</body>
