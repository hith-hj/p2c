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
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.6.0/dist/leaflet.css"
        integrity="sha512-xwE/Az9zrjBIphAcBb3F6JVqxf46+CDLwfLMHloNu6KEQCAWi6HcDUbeOfBIptF7tcCzusKFjFw2yuvEpDL9wQ=="
        crossorigin="" />
</head>

<body class="bg-red-600 scroll-smooth transition duration-300 ease-in-out p-[2px]">
    <!-- Navbar (Overlay on Content) -->
    <nav id="nav" class="fixed top-0 left-0 w-full text-white py-3 z-50 ">
        <div class="container hidden md:flex justify-center mx-auto">
            <ul class="flex gap-10 text-lg font-medium">
                <li>
                    <a href="/" class="hover:underline hover:underline-offset-8">
                        Home
                    </a>
                </li>
                <li>
                    <a href="#order_details" class="hover:underline hover:underline-offset-8">
                        Order Details
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

    <div class="bg-red-600 bg-[url(/imgs/sketch1.webp)] bg-auto bg-blend-overlay">
        <section id="home" class="h-screen flex flex-col justify-center items-center sliderx
            text-center text-white backdrop-blur-[1px] ">
            <div class="py-16 w-full h-screen">
                <h1 class="text-6xl font-semibold md:font-bold md:text-8xl my-20">Super !!</h1>
                <p class="text-lg font-semibold md:text-xl mt-20 mb-10">
                    We hope that your package was delivered on time.
                </p>
                <div>
                    <p>Your Order Details is shown bellow</p>
                    <a href="#order_details" class="hover:underline hover:underline-offset-8">
                        <x-primaryButton class="mt-4">Details</x-primaryButton>
                    </a>
                </div>
            </div>
        </section>

        <section id="order_details" class="py-16 text-gray-50 text-center backdrop-blur-[1px] ">
            <h2 class="text-3xl font-bold">Your order details are:</h2>
            <div class="p-2 w-full h-full">
                <div class="text-center mt-2">
                    <table class="w-full md:w-1/2 mx-auto divide-y divide-gray-200">
                        <thead class="bg-zinc-200">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-sm font-medium text-zinc-900 uppercase tracking-wider">
                                Name
                                </th>
                                <th scope="col" class="px-6 py-3 text-sm font-medium text-zinc-900 uppercase tracking-wider">
                                Value
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-zinc-50 divide-y divide-gray-200">
                            @foreach($order as $key=>$value)
                            <tr>
                                <td class="px-6 py-3 whitespace-nowrap">
                                    <div class="text-md font-medium text-zinc-800">
                                        {{str()->headline($key)}}
                                    </div>
                                </td>
                                <td class="px-6 py-3 whitespace-nowrap">
                                    <div class="text-md font-medium text-zinc-800">
                                        {{$value}}
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>

    <!-- Contact -->
    <section id="contact" class="md:h-screen py-16 px-10 bg-white text-black text-center flex flex-col justify-around md:flex-row">
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
            <h2 class="text-3xl font-bold">Links</h2>
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
                    <h4 class="font-semibold mb-4">Links</h4>
                    <ul class="w-full">
                        <li>one</li>
                        <li>two</li>
                        <li>three</li>
                        <li>four</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Back to Top Button -->
    <button id="backToTop"
    class="hidden fixed bottom-6 right-6 px-2 py-1 bg-red-600
    text-white font-extrabold rounded-[10px] shadow-lg
    hover:bg-white hover:text-red-600 hover:inset-ring-3 hover:inset-ring-red-600">^</button>

<script>
        // home Slider
        let images = ['sketch1.webp', 'sketch2.webp', 'sketch3.webp', 'sketch4.webp', 'sketch5.webp', 'sketch6.webp'];
        let slider = document.querySelector(".slider");
        let i = 0;
        setInterval(() => {
            if (slider !== null) {
                if (i === 0) {
                    slider.classList.remove("bg-[url(/imgs/" + images[images.length - 1] + ")]");
                } else {
                    slider.classList.remove("bg-[url(/imgs/" + images[i - 1] + ")]");
                }
                slider.classList.add("bg-[url(/imgs/" + images[i] + ")]");
                i = i === images.length ? 0 : (i + 1) % images.length;
            }
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
            if (window.scrollY > 200) {
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
