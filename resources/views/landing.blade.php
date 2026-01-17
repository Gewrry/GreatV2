@include('partials.header')

<body class="min-h-screen font-main bg-bluebody relative">
    <!-- Full-Screen Background Video -->
    <div class="fixed top-0 left-0 w-full h-full z-0 overflow-hidden">
        <video id="bgVideo"
            class="absolute top-1/2 left-1/2 min-w-full min-h-full w-auto h-auto transform -translate-x-1/2 -translate-y-1/2 object-cover"
            autoplay muted playsinline>
            <source src="{{ asset('videos/1.mp4') }}" type="video/mp4">
        </video>

        <!-- Dark Overlay for better content readability -->
        <div class="absolute inset-0 bg-black opacity-50"></div>

        <!-- Gradient Overlay (optional - for more style) -->
        <div class="absolute inset-0 bg-gradient-to-b from-transparent via-transparent to-blue opacity-60"></div>
    </div>

    <!-- Content Layer -->
    <div class="relative z-10">
        @include('partials.navigation')

        <!-- Your page content goes here -->
        <main class="min-h-screen">
            <!-- Hero Section -->
            <section class="pt-32 pb-20 px-4 sm:px-6 lg:px-8">
                <div class="max-w-4xl mx-auto text-center">
                    <h1 class="text-5xl md:text-7xl font-bold text-white mb-6 drop-shadow-lg">
                        Welcome to GReAT System
                    </h1>
                    <p class="text-xl md:text-2xl text-gray-100 mb-10 drop-shadow-md">
                        Where you can experience faster work, accurate Result, Reliable service and more!
                    </p>
                    <div class="flex gap-4 justify-center flex-wrap flex-col">
                        <button
                            class="fill-button px-8 py-4 bg-transparent border-2 border-white text-white font-semibold rounded-lg shadow-xl transition-all duration-300">
                            Login to your Account
                        </button>
                        <span class="italic text-xs text-white/50">Only for Goverment Use</span>
                        <span class="w-full border-1 border-white"></span>
                        <div class="flex flex-col">
                            <span class="italic text-xs text-white/50">Inquire or Contact us</span>
                            <span></span>
                        </div>


                    </div>
                </div>
            </section>



            <!-- Unique Features Section with Diagonal Layout -->
            <!-- Unique Features Section with Diagonal Layout -->
            <section class="py-20 px-4 sm:px-6 lg:px-8">
                <div class="max-w-7xl mx-auto">
                    <h2 class="text-4xl md:text-5xl font-bold text-white text-center mb-16 drop-shadow-lg">
                        Why Choose GReAT?
                    </h2>

                    <div class="flex-row flex">
                        <!-- Card 1: Boost Income -->
                        <div class="card mx-2 hover:w-full group">
                            <div class="tools">
                                <div class="circle">
                                    <span class="red box"></span>
                                </div>
                                <div class="circle">
                                    <span class="yellow box"></span>
                                </div>
                                <div class="circle">
                                    <span class="green box"></span>
                                </div>
                            </div>
                            <div class="card__content flex items-center justify-center flex-col px-7">
                                <lord-icon src="https://cdn.lordicon.com/bsdkzyjd.json" trigger="loop"
                                    colors="primary:#1b1091,secondary:#08a88a" style="width:100px;height:100px">
                                </lord-icon>
                                <h1 class="text-2xl font-bold    transition-opacity duration-300">
                                    More Income!</h1>
                                <p
                                    class="text-xs md:text-sm text-gray-700 opacity-0 group-hover:opacity-100 transition-opacity duration-300 px-2 leading-relaxed">
                                    Boost your productivity and income using our system. You can work faster than
                                    traditional methods.
                                </p>
                            </div>
                        </div>

                        <div class="card mx-2 hover:w-full group">
                            <div class="tools">
                                <div class="circle">
                                    <span class="red box"></span>
                                </div>
                                <div class="circle">
                                    <span class="yellow box"></span>
                                </div>
                                <div class="circle">
                                    <span class="green box"></span>
                                </div>
                            </div>
                            <div class="card__content flex items-center justify-center flex-col px-7">
                                <lord-icon src="https://cdn.lordicon.com/fgxwhgfp.json" trigger="hover"
                                    colors="primary:#3080e8,secondary:#08a88a" style="width:100px;height:100px">
                                </lord-icon>
                                <h1 class="text-2xl font-bold    transition-opacity duration-300">
                                    Secured Data!</h1>
                                <p
                                    class="text-xs md:text-sm text-gray-700 opacity-0 group-hover:opacity-100 transition-opacity duration-300 px-2 leading-relaxed">
                                    Secured Data!
                                    Our system offers a secure way of storing data, packed with encryption methods that
                                    hide your data from the eyes of troublemakers.
                                </p>
                            </div>
                        </div>

                        <div class="card mx-2 hover:w-full group">
                            <div class="tools">
                                <div class="circle">
                                    <span class="red box"></span>
                                </div>
                                <div class="circle">
                                    <span class="yellow box"></span>
                                </div>
                                <div class="circle">
                                    <span class="green box"></span>
                                </div>
                            </div>
                            <div class="card__content flex items-center justify-center flex-col px-7">
                                <lord-icon src="https://cdn.lordicon.com/bsdkzyjd.json" trigger="loop"
                                    colors="primary:#1b1091,secondary:#08a88a" style="width:100px;height:100px">
                                </lord-icon>
                                <h1 class="text-2xl font-bold    transition-opacity duration-300">
                                    Convient Transactions</h1>
                                <p
                                    class="text-xs md:text-sm text-gray-700 opacity-0 group-hover:opacity-100 transition-opacity duration-300 px-2 leading-relaxed">
                                    Our System provides Online payment for clients that doesnt want to go in LGU of
                                    their own.
                                </p>
                            </div>
                        </div>

                        <div class="card mx-2 hover:w-full group">
                            <div class="tools">
                                <div class="circle">
                                    <span class="red box"></span>
                                </div>
                                <div class="circle">
                                    <span class="yellow box"></span>
                                </div>
                                <div class="circle">
                                    <span class="green box"></span>
                                </div>
                            </div>
                            <div class="card__content flex items-center justify-center flex-col px-7">
                                <lord-icon src="https://cdn.lordicon.com/bsdkzyjd.json" trigger="loop"
                                    colors="primary:#1b1091,secondary:#08a88a" style="width:100px;height:100px">
                                </lord-icon>
                                <h1 class="text-2xl font-bold    transition-opacity duration-300">
                                    More Income!</h1>
                                <p
                                    class="text-xs md:text-sm text-gray-700 opacity-0 group-hover:opacity-100 transition-opacity duration-300 px-2 leading-relaxed">
                                    Boost your productivity and income using our system. You can work faster than
                                    traditional methods.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Testimonial/CTA Section with Morphing Shape -->
            <section class="py-24 px-4 sm:px-6 lg:px-8 relative">
                <div class="max-w-5xl mx-auto relative">
                    <!-- Animated background blob -->
                    <div
                        class="absolute inset-0 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 rounded-full blur-3xl opacity-20 animate-pulse">
                    </div>

                    <div
                        class="relative bg-white bg-opacity-10 backdrop-blur-xl border-2 border-white border-opacity-30 rounded-3xl p-12 md:p-16 text-center shadow-2xl">
                        <div class="mb-8">
                            <svg class="w-16 h-16 mx-auto text-white opacity-50" fill="currentColor"
                                viewBox="0 0 24 24">
                                <path
                                    d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z" />
                            </svg>
                        </div>
                        <blockquote class="text-2xl md:text-3xl font-medium text-white mb-6 leading-relaxed">
                            Transform your workflow with a system designed for excellence
                        </blockquote>
                        <p class="text-lg text-gray-200 mb-10">
                            Join thousands of professionals who trust GReAT System for their daily operations
                        </p>
                        <button
                            class="fill-button px-10 py-5 bg-transparent border-2 border-white text-white font-bold text-lg rounded-xl shadow-2xl transition-all duration-300 hover:shadow-3xl">
                            Get Started Today
                        </button>
                    </div>
                </div>
            </section>
        </main>

    </div>

    @include('partials.footer')

    <script>
        let hover1




        // Video playlist
        const videos = [
            "{{ asset('videos/1.mp4') }}",
            "{{ asset('videos/2.mp4') }}",
            "{{ asset('videos/3.mp4') }}"
        ];

        let currentVideoIndex = 0;
        const video = document.getElementById('bgVideo');

        // Play next video when current one ends
        video.addEventListener('ended', function() {
            currentVideoIndex = (currentVideoIndex + 1) % videos.length;
            video.src = videos[currentVideoIndex];
            video.load();
            video.play();
        });

        // Ensure autoplay starts
        video.play().catch(function(error) {
            console.log("Autoplay prevented:", error);
        });



        // Select all elements with the 'card' class
        const cards = document.querySelectorAll('.card');

        // Add event listeners to each card
        cards.forEach(card => {
            // Mouse enter - add 'shrink' class
            card.addEventListener('mouseenter', function() {
                this.classList.add('shrink');
            });

            // Mouse leave - remove 'shrink' class
            card.addEventListener('mouseleave', function() {
                this.classList.remove('shrink');
            });
        });
    </script>
</body>
