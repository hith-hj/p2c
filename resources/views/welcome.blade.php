<!DOCTYPE html>
<html lang="en" class="">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>P2C - Connecting Producers & Carriers</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        html {
            scroll-behavior: smooth !important;
        }

        body {
            padding-top: 1rem;
        }
    </style>
</head>

<body class="bg-red-600 scroll-smooth transition duration-700 ease-in-out">

    <!-- Navbar (Overlay on Content) -->
    <nav id="nav" class="fixed top-0 left-0 w-full text-white py-3 z-50">
        <div class="container mx-auto justify-center px-8 hidden md:flex">
            <ul class="flex space-x-8 text-lg font-medium">
                <li>
                    <a href="#home" class="hover:underline hover:underline-offset-8">
                        Home
                    </a>
                </li>
                <li>
                    <a href="#producer" class="hover:underline hover:underline-offset-8">
                        Producers
                    </a>
                </li>
                <li>
                    <a href="#carrier" class="hover:underline hover:underline-offset-8">
                        Carriers
                    </a>
                </li>
                <li>
                    <a href="#testimonials" class="hover:underline hover:underline-offset-8">
                        Testimonials
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
                    <span class="text-2xl font-bold">P2C</span>
                </div>
                <button id="mobileMenuButton" class="text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
            <div id="mobileMenu" class="hidden md:hidden bg-primary dark:bg-backgroundDark text-center bg-red-600 text-white">
                <ul class="px-2 pt-2 pb-4 space-y-1">
                    <li><a href="#home" class="block px-2 py-1 hover:underline">Home</a></li>
                    <li><a href="#how-it-works" class="block px-2 py-1 hover:underline">How It Works</a></li>
                    <li><a href="#producer" class="block px-2 py-1 hover:underline">Producers</a></li>
                    <li><a href="#carrier" class="block px-2 py-1 hover:underline">Carriers</a></li>
                    <li><a href="#testimonials" class="block px-2 py-1 hover:underline">Testimonials</a></li>
                    <li><a href="#contact" class="block px-2 py-1 hover:underline">Contact</a></li>
                </ul>
            </div>
        </div>
    </nav>



    <!-- Hero Section (Full-Screen with Navbar Offset) -->
    <section id="home" class="h-screen flex flex-col justify-center items-center
        text-center bg-gradient-to-b from-red-600 to-zinc-200 text-white px-6">
        <h1 class="text-6xl font-bold">Seamless Shipping Connections</h1>
        <p class="mt-6 text-xl">Join P2C today and experience the future of local shipping where reliability meets innovation!</p>
        <div class="mt-8 space-x-6">
            <a href="#how-it-works" class="bg-red-600 text-gray-100 px-8 py-4 rounded shadow-lg
            hover:bg-white hover:text-red-600">How it works</a>
        </div>
    </section>

    <!-- How It Works -->
    <section id="how-it-works" class="py-28 bg-gradient-to-b from-zinc-200 to-white text-gray-900 text-center ">
        <h2 class="text-5xl font-bold">How P2C Works</h2>
        <p class="mt-6 text-lg">
            P2C simplifies logistics by connecting producers with customer through reliable carriers.
        </p>
        <div class="mt-10 flex justify-center flex-col md:flex-row gap-2">
            <div class="w-full md:w-1/3 p-8 bg-red-700 text-white rounded-lg shadow-md hover:ring-4 hover:ring-red-400">
                <h3 class="text-2xl font-semibold">For Producers</h3>
                <p class="mt-4">Register, list deliveries, and effortlessly watch your goods goes to the customers.</p>
            </div>
            <div class="w-full md:w-1/3 p-8 bg-red-700 text-white rounded-lg shadow-md hover:ring-4 hover:ring-red-400">
                <h3 class="text-2xl font-semibold">For Carriers</h3>
                <p class="mt-4">Download the app, accept deliveries, and start earning today.</p>
            </div>
        </div>
    </section>

    <!-- Producer Section -->
    <section id="producer" class="py-28 bg-gradient-to-b from-white to-zinc-200 text-center px-10">
        <h2 class="text-5xl font-bold text-gray-900">Producers</h2>
        <p class="mt-6 text-lg text-gray-700">
            Expand Your Reach with trusted carriers and get your shipments delivered efficiently. Seamless Registration, Sign up through the website or mobile app and start listing shipments. Effortless Shipping Management – Monitor deliveries, optimize shipping costs, and enhance customer satisfaction.
        </p>
        <div class="mt-10 flex justify-center flex-col md:flex-row gap-2">
            <a href="#" class="bg-red-600 text-white px-8 py-4 rounded shadow-lg hover:bg-white hover:text-red-600">
                Register
            </a>
            <a href="#" class="bg-zinc-800 text-white px-8 py-4 rounded shadow-lg hover:bg-white hover:text-red-600">
                Download App
            </a>
        </div>
    </section>

    <!-- Carrier Section -->
    <section id="carrier" class="py-28 bg-gradient-to-b from-zinc-200 to-white text-dark text-center px-10">
        <h2 class="text-5xl font-bold">Carriers</h2>
        <p class="mt-6 text-lg text-gray-600">
            Earn Money on Your Terms Whether full-time or part-time.
            P2C lets you maximize your earnings.
            Download the app, accept deliveries, and start making money.
            Work with verified producers and grow your delivery network.
        </p>
        <a href="#" class="mt-10 inline-block bg-red-600 text-white px-8 py-4 rounded shadow-lg
        hover:bg-white hover:text-red-600">
            Download App
        </a>
    </section>

    <section class="py-28 bg-gradient-to-b from-white to-red-600  text-center px-10">
        <h2 class="text-5xl font-bold text-zinc-700"></h2>
        <div id="testimonialSlider" class="mt-10 text-lg font-medium  text-zinc-700">
        </div>
    </section>

    <!-- Testimonials -->
    <section id="testimonials" class="py-28 bg-red-600  text-center px-10">
        <h2 class="text-5xl font-bold text-zinc-100">What Our Users Say</h2>
        <div id="testimonialSlider" class="mt-10 text-lg font-medium  text-zinc-100">
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
    </section>

    <section id="about" class="h-screen flex flex-col justify-center items-center text-center bg-gradient-to-b
        from-red-600 to-zinc-200 text-white px-6">
        <h1 class="text-6xl font-bold">About</h1>
        <p class="mt-6 text-xl">
            P2C is a revolutionary local shipping platform designed to bridge the gap between producers and customers through carriers, ensuring reliable, and effortless deliveries. Whether you're a producer looking to expand your customer reach or a carrier seeking flexible earning opportunities, P2C simplifies the entire process with smart solutions.
        </p>
        <div class="mt-8 space-x-6"></div>
    </section>

    <!-- Footer & Contact -->
    <footer id="contact" class="py-16 bg-gradient-to-b from-zinc-200 to-white text-dark text-center px-10 items-center">
        <h2 class="text-3xl font-bold">Get in Touch</h2>
        <!-- <form class="mt-10 flex flex-col md:flex-row mf:flex-wrap justify-center gap-2"> -->
        <form class="mt-10 grid gap-2">
            <input type="text" class="p-4 rounded border shadow-lg col-span-2 md:col-span-1 " placeholder="Your Name">
            <input type="text" class="p-4 rounded border shadow-lg col-span-2 md:col-span-1" placeholder="Your Email">
            <input type="text" class="p-4 rounded border shadow-lg col-span-2" placeholder="Your Message">
            <button type="submit" class="px-8 py-4 rounded shadow-lg text-white col-span-2 bg-red-600 hover:bg-white hover:text-red-600">
                Submit
            </button>
        </form>
        <!-- <p class="mt-8">Follow us on social media for updates!</p> -->
    </footer>

    <!-- Back to Top Button -->
    <button id="backToTop" class="hidden fixed bottom-6 right-6 bg-red-600 text-white py-1 px-3 rounded shadow-lg hover:bg-red-600">^</button>

    <script>
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
            if (window.scrollY > 200) {
                backToTop.classList.remove("hidden");
                nav.classList.add('bg-red-600', 'shadow-lg');
            } else {
                backToTop.classList.add("hidden");
                nav.classList.remove('bg-red-600', 'shadow-lg');
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
