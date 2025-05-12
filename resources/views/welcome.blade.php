<!DOCTYPE html>
<html lang="en" class="">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>P2C - Connecting Producers & Carriers</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style>
        html {
            scroll-behavior: smooth !important;
        }
    </style>
</head>

<body class="bg-red-600 scroll-smooth transition duration-300 ease-in-out p-[2px]">
    <!-- Navbar (Overlay on Content) -->
    <nav id="nav" class="fixed top-0 left-0 w-full text-white py-3 z-50 ">
        <div class="container hidden md:flex justify-center mx-auto">
            <ul class="flex gap-10 text-lg font-medium">
                <li>
                    <a href="#home" class="hover:underline hover:underline-offset-8">
                        Home
                    </a>
                </li>
                <li>
                    <a href="#how-it-works" class="hover:underline hover:underline-offset-8">
                        How it works
                    </a>
                </li>
                <li>
                    <a href="#about" class="hover:underline hover:underline-offset-8">
                        About
                    </a>
                </li>
                <li>
                    <a href="#contact" class="hover:underline hover:underline-offset-8">
                        Contact
                    </a>
                </li>
            </ul>
        </div>
        <div class="container items-center m-auto px-8 md:hidden">
            <div class="container mx-auto flex justify-between px-4">
                <div class="items-center">
                    <span class="text-3xl font-bold">P2C</span>
                </div>
                <button id="mobileMenuButton">
                    <!-- <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg> -->
                    <i class="font-bold text-4xl">=</i>
                </button>
            </div>
            <div id="mobileMenu" class="hidden md:hidden text-center bg-red-600">
                <ul class="flex flex-col text-lg font-medium">
                    <li>
                        <a href="#home" class="block px-2 py-1 hover:underline hover:underline-offset-8">
                            Home
                        </a>
                    </li>
                    <li>
                        <a href="#how-it-works" class="block px-2 py-1 hover:underline hover:underline-offset-8">
                            How it works
                        </a>
                    </li>
                    <li>
                        <a href="#about" class="block px-2 py-1 hover:underline hover:underline-offset-8">
                            about
                        </a>
                    </li>
                    <li>
                        <a href="#contact" class="block px-2 py-1 hover:underline hover:underline-offset-8">
                            Contact
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section (Full-Screen with Navbar Offset) -->
    <section id="home" class="h-screen flex flex-col justify-center items-center sliderx
        text-center text-white bg-red-500 bg-[url(imgs/sketch1.webp)] bg-auto bg-blend-overlay">
        <div class="backdrop-blur-[2px] py-16 w-full h-screen">
            <h1 class="text-6xl font-semibold md:font-bold md:text-8xl my-20">Seamless Deliveries</h1>
            <p class="text-lg font-semibold md:text-xl my-20">
                Join P2C today and experience the future of Deliveries,
                <br> where reliability meets innovation!
            </p>
            <div class="mt-16">
                <a href="#how-it-works">
                    <x-primaryButton>How it works</x-primaryButton>
                </a>
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section id="how-it-works" class="py-16 bg-gradient-to-b from-zinc-200 to-white text-gray-900 text-center">
        <h2 class="text-5xl font-bold">How P2C Works</h2>
        <p class="mt-1 text-lg">
            P2C simplifies logistics by connecting producers with customer through reliable carriers.
        </p>
        <div class="mt-3 flex justify-start flex-col md:flex-row gap-5 px-3">
            <div class="w-full md:w-1/2 text-white rounded-sm shadow-md bg-red-500
            bg-[url(imgs/sketch3.webp)] bg-blend-darken bg-cover bg-center">
                <div class="p-2 backdrop-blur-[5px] w-full h-full">
                    <h3 class="text-4xl font-semibold">For Producers</h3>
                    <div class="text-start mt-2">
                        <p class="font-bold text-lg">
                            Watch your customers base grows effortlessly.
                        </p>
                        <p class="font-semibold text-md">
                            Expand Your Reach with easy steps:
                        </p>
                        <ol class="text-start list-decimal px-6">
                            <li>Seamless Registration through the website or mobile app.</li>
                            <li>start listing and Monitor Deliveries.</li>
                            <li>Optimize costs, and enhance customer satisfaction.</li>
                            <li>Get your shipments delivered efficiently.</li>
                        </ol>
                        <div class="my-5 flex flex-row justify-start gap-2">
                            <a href="#">
                                <x-primaryButton> Download App </x-primaryButton>
                            </a>
                            <a href="#">
                                <x-secondaryButton> Register </x-secondaryButton>
                            </a>
                        </div>
                    </div>
                    <div class="bg-red-500">
                        <img src="imgs/sketch6.webp" class="w-fit">
                    </div>
                </div>
            </div>
            <div class="w-full md:w-1/2 text-white rounded-sm shadow-md bg-red-500
            bg-[url(imgs/sketch4.webp)] bg-blend-darken bg-cover bg-center">
                <div class="p-2 backdrop-blur-[5px] w-full h-full">
                    <h3 class="text-4xl font-semibold">For Carriers</h3>
                    <div class="text-start mt-2">
                        <p class="font-bold text-lg">
                            With us you can start Earning Today.
                        </p>
                        <p class="font-semibold text-md">
                            Earn more Money Whether full-time or part-time.<br>
                        </p>
                        <ol class="text-start list-decimal px-6">
                            <li>Start the Registration through the mobile app.</li>
                            <li>Download the app, fill Basic information and thats it.</li>
                            <li>accept deliveries, and start making money on Your Terms Whether.</li>
                            <li>Work with verified producers and grow your delivery network.</li>
                        </ol>
                        <div class="my-5 flex flex-row justify-start gap-2">
                            <a href="#">
                                <x-primaryButton> Download App </x-primaryButton>
                            </a>
                        </div>
                    </div>
                    <div class=" bg-red-500">
                        <img src="imgs/sketch5.webp" class="w-fit">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- about -->
    <section id="about" class="md:h-screen py-16 px-10 text-white text-center bg-gradient-to-b from-red-600 to-red-500">
        <h1 class="font-bold text-6xl">About</h1>
        <p class="mt-16 text-xl">
            P2C is a revolutionary local shipping platform designed to bridge the gap between producers and customers through carriers, ensuring reliable, and effortless deliveries. Whether you're a producer looking to expand your customer reach or a carrier seeking flexible earning opportunities, P2C simplifies the entire process with smart solutions.
        </p>
        <div class="mt-16">
            <h2 class="text-5xl font-bold">What Our Users Say</h2>
            <div id="testimonialSlider" class="mt-10 text-lg font-medium ">
                <p>
                    "P2C transformed the way I deliver products! Orders arrive on time, and I no longer worry about logistics. It's a game-changer for small businesses like mine." — Sarah M., Local Bakery Owner
                </p>
                <p class="hidden">
                    "Finding reliable carriers used to be a hassle, but with P2C, I get instant access to professionals who make deliveries seamless. My customers love the fast shipping!" — Ahmed K., Handmade Goods Seller
                </p>
                <p class="hidden">
                    "Since joining P2C, I've seen a huge boost in customer satisfaction. Deliveries are smooth, communication is great, and it helps my business grow!" — Lina R., Organic Farm Supplier
                </p>
                <p class="hidden">
                    "P2C gives me the freedom to choose deliveries that fit my schedule. It's an excellent side gig that actually pays well!" — James P., Independent Carrier
                </p>
                <p class="hidden">
                    "I love how easy it is to use the app. Accepting deliveries is simple, and the extra income helps me support my family. Best decision I’ve made!" — Kareem H., Full-Time Driver
                </p>
                <p class="hidden">
                    "Driving with P2C has been great. I set my own hours, pick deliveries that work for me, and I always get paid on time!" — Sophie L., Part-Time Courier
                </p>
            </div>
        </div>
    </section>

    <!-- Contact -->
    <footer id="contact" class="md:h-screen py-16 px-10 bg-white text-black text-center flex flex-col justify-around md:flex-row">
        <div class="w-full">
            <h2 class="text-3xl font-bold">Contact Us</h2>
            <form class="mt-5 grid gap-2 text-start">
                <div class="flex flex-col col-span-2 md:col-span-1">
                    <label class="font-semibold">Name</label>
                    <input type="text" class="p-1 rounded border shadow-lg" placeholder="Your Name">
                </div>
                <div class="flex flex-col col-span-2 md:col-span-1">
                    <label class="font-semibold">Email</label>
                    <input type="text" class="p-1 rounded border shadow-lg" placeholder="Your Email">
                </div>
                <div class="flex flex-col col-span-2">
                    <label class="font-semibold">Message</label>
                    <input type="text" class="p-1 rounded border shadow-lg " placeholder="Your Message">
                </div>
                <div class="flex flex-col col-span-2">
                    <x-secondaryButton type="submit">Send</x-secondaryButton>
                </div>
            </form>
        </div>
        <div class="w-full mt-4 md:mt-0">
            <h2 class="text-3xl font-bold">Location & Links</h2>
            <div class="grid grid-cols-2 mt-10">
                <div class="col-span-1">
                    <h4 class="font-semibold mb-4">Socials</h4>
                    <ul class="w-full">
                        <li>one</li>
                        <li>two</li>
                        <li>three</li>
                        <li>four</li>
                    </ul>
                </div>
                <div class="col-span-1">
                    <h4 class="font-semibold mb-4">Navs</h4>
                    <ul class="w-full">
                        <li>one</li>
                        <li>two</li>
                        <li>three</li>
                        <li>four</li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>

    <!-- Back to Top Button -->
    <button id="backToTop" class="hidden fixed bottom-6 right-6 bg-red-600 text-white px-2 py-1 font-extrabold rounded-[10px] shadow-lg
    hover:bg-white hover:text-red-600 hover:inset-ring-3 hover:inset-ring-red-600">^</button>

    <script>
        // home Slider
        let images = ['sketch1.webp', 'sketch2.webp', 'sketch3.webp', 'sketch4.webp', 'sketch5.webp', 'sketch6.webp'];
        let slider = document.querySelector(".slider");
        let i = 0;
        setInterval(() => {
            if (i === 0) {
                slider.classList.remove("bg-[url(imgs/" + images[images.length - 1] + ")]");
            } else {
                slider.classList.remove("bg-[url(imgs/" + images[i - 1] + ")]");
            }
            slider.classList.add("bg-[url(imgs/" + images[i] + ")]");
            i = i === images.length ? 0 : (i + 1) % images.length;
        }, 3000);

        // Mobile Menu Toggle
        const mobileMenuButton = document.getElementById('mobileMenuButton');
        const mobileMenu = document.getElementById('mobileMenu');
        mobileMenuButton.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });
        // Testimonials Slider
        let slides = document.querySelectorAll("#testimonialSlider p");
        let index = 0;
        setInterval(() => {
            slides.forEach(s => s.classList.add("hidden"));
            slides[index].classList.remove("hidden");
            index = (index + 1) % slides.length;
        }, 3000);

        // Back to Top Button Visibility & Functionality
        const backToTop = document.getElementById("backToTop");
        const nav = document.getElementById("nav");
        window.addEventListener("scroll", () => {
            if (window.scrollY > 300) {
                backToTop.classList.remove("hidden");
                nav.classList.add('bg-red-600', 'shadow-lg', );
            } else {
                backToTop.classList.add("hidden");
                nav.classList.remove('bg-red-600', 'shadow-lg', );
            }
        });
        backToTop.addEventListener("click", () => {
            document.getElementById("home").scrollIntoView({
                behavior: "smooth"
            });
        });
    </script>

</body>

</html>
