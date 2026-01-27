<nav id="navbar"
    class="w-full flex  flex-row items-center h-[80px] text-white shadow-md bg-white/80 backdrop-blur-md sticky top-0 z-50 px-5 lg:px-[10%] py-5 transition-all duration-500 ease-[cubic-bezier(0.4,0,0.2,1)]">
    <div>
        <input type="image" src="images/logo.png" class="w-[60px]" alt="">
    </div>
    <div class="flex flex-col transition-all duration-500 ease-[cubic-bezier(0.4,0,0.2,1)]" id="navTitle">
        <h1 class="text-2xl font-bold transition-all duration-500 text-blue ease-[cubic-bezier(0.4,0,0.2,1)]"
            id="navHeading">GReAT
        </h1>
        <span class="text-sm italic font-thin text-blue transition-all duration-500 ease-[cubic-bezier(0.4,0,0.2,1)]"
            id="navSubtitle">Government Revenue, Accounting and Taxation System</span>
    </div>

    <div class="ml-auto h-full flex items-center justify-center">
        <a href="{{ route('login') }}"
            class="hover:-translate-y-1 text-blue ease-out transition delay-75  font-semibold hover:text-lumot">User
            Login</a>
    </div>
</nav>

<script>
    let lastScroll = 0;
    const navbar = document.getElementById('navbar');
    const navHeading = document.getElementById('navHeading');
    const navSubtitle = document.getElementById('navSubtitle');

    window.addEventListener('scroll', () => {
        const currentScroll = window.pageYOffset;

        if (currentScroll > 50) {
            // Scrolled - shrink navbar
            navbar.classList.remove('h-[80px]');
            navbar.classList.add('h-[60px]');
            navbar.classList.add('bg-blue/80'); // More opaque when scrolled
            navbar.classList.add('py-5'); // More opaque when scrolled

            // Shrink title
            navHeading.classList.remove('text-2xl');
            navHeading.classList.add('text-xl');

            // Shrink subtitle
            navSubtitle.classList.remove('text-sm');
            navSubtitle.classList.add('text-xs');

        } else {
            // At top - restore original size
            navbar.classList.remove('h-[60px]');
            navbar.classList.add('h-[80px]');
            navbar.classList.remove('bg-blue/80');

            // Restore title
            navHeading.classList.remove('text-xl');
            navHeading.classList.add('text-2xl');

            // Restore subtitle
            navSubtitle.classList.remove('text-xs');
            navSubtitle.classList.add('text-sm');
        }

        lastScroll = currentScroll;
    });
</script>

<style>
    /* Enhanced cubic-bezier animation */
    #navbar,
    #navHeading,
    #navSubtitle {
        transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Ensure backdrop blur is always 100% */
    #navbar {
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
    }
</style>
