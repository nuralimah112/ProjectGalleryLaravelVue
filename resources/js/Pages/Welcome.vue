<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps<{
    canLogin: boolean;
    canRegister: boolean;
    laravelVersion: string;
    phpVersion: string;
}>();

function handleImageError() {
    document.getElementById('screenshot-container')?.classList.add('!hidden');
    document.getElementById('docs-card')?.classList.add('!row-span-1');
    document.getElementById('docs-card-content')?.classList.add('!flex-row');
    document.getElementById('background')?.classList.add('!hidden');
}
</script>

<template>
    <Head title="Welcome" />
    <div class="bg-white text-gray-700">
        <div class="relative flex min-h-screen flex-col items-center justify-center selection:bg-[#FF2D20] selection:text-white">
            <header class="w-full bg-white border-b border-gray-200">
                <nav class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4">
                    <a href="/" class="flex items-center space-x-3 rtl:space-x-reverse">
                        <!-- Logo Image -->
                        <img src="/images/profiles/logo.jpg" alt="Logo" class="w-8 h-8 rounded-full">
                        <span class="self-center text-2xl font-semibold whitespace-nowrap text-gray-900">Gallery</span>
                    </a>

                    <div class="flex md:order-2 space-x-3 md:space-x-0 rtl:space-x-reverse">
                        <nav v-if="canLogin" class="flex space-x-2">
                            <Link
    v-if="$page.props.auth.user"
    :href="route('dashboard')"
    class="rounded-md px-3 py-2 text-gray-800 bg-gray-200 ring-1 ring-transparent transition hover:text-gray-600 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:bg-gray-800 dark:hover:text-gray-300 dark:focus-visible:ring-white"
>
    Dashboard
</Link>

                            <template v-else>
                                <Link :href="route('login')" class="text-white bg-red-800 hover:bg-red-900 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-4 py-2 text-center mr-2">
                                    Login
                                </Link>
                                <Link v-if="canRegister" :href="route('register')" class="text-white bg-red-800 hover:bg-red-900 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-4 py-2 text-center">
                                    Register
                                </Link>
                            </template>
                        </nav>
                        <button data-collapse-toggle="navbar-cta" type="button" class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-gray-500 rounded-lg md:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200" aria-controls="navbar-cta" aria-expanded="false">
                            <span class="sr-only">Open main menu</span>
                            <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 17 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h15M1 7h15M1 13h15"/>
                            </svg>
                        </button>
                    </div>
                </nav>
            </header>

            <main class="flex-grow mt-4 px-4 sm:px-6 lg:px-8">
                <h1 class="text-4xl font-bold text-center text-gray-900 mb-2">Get your next</h1>
                <p class="text-lg text-center text-gray-600 mb-8">You can try this</p>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    <!-- First Row of Cards -->
                    <div class="card">
                        <img src="/images/profiles/1.jpg" class="w-full h-56 object-cover rounded-t-lg" alt="" />
                    </div>
                    <div class="card">
                        <img src="/images/profiles/2.jpg" class="w-full h-56 object-cover rounded-t-lg" alt="" />
                    </div>
                    <div class="card">
                        <img src="/images/profiles/3.jpg" class="w-full h-56 object-cover rounded-t-lg" alt="" />
                    </div>
                    <div class="card">
                        <img src="/images/profiles/4.jpg" class="w-full h-56 object-cover rounded-t-lg" alt="" />
                    </div>

                    <!-- Second Row of Cards -->
                    <div class="card">
                        <img src="/images/profiles/4.jpg" class="w-full h-56 object-cover rounded-t-lg" alt="" />
                    </div>
                    <div class="card">
                        <img src="/images/profiles/3.jpg" class="w-full h-56 object-cover rounded-t-lg" alt="" />
                    </div>
                    <div class="card">
                        <img src="/images/profiles/2.jpg" class="w-full h-56 object-cover rounded-t-lg" alt="" />
                    </div>
                    <div class="card">
                        <img src="/images/profiles/1.jpg" class="w-full h-56 object-cover rounded-t-lg" alt="" />
                    </div>
                </div>
            </main>

            <footer class="py-10 text-center text-sm text-gray-700">
                Laravel v{{ laravelVersion }} (PHP v{{ phpVersion }})
            </footer>
        </div>
    </div>
</template>

<style scoped>
.card {
    background-color: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    height: 300px; /* Set fixed height for uniformity */
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}
.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
}
</style>
