<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
});
</script>

<template>
    <Head title="Welcome - Shipping Label Generator" />

    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800">
        <!-- Navigation -->
        <nav class="fixed w-full bg-white/80 dark:bg-gray-900/80 backdrop-blur-sm border-b border-gray-200 dark:border-gray-700 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16 items-center">
                    <div class="flex items-center space-x-3">
                        <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                        <span class="text-2xl font-bold text-gray-900 dark:text-white">ShipLabel</span>
                    </div>
                    
                    <div v-if="canLogin" class="flex items-center space-x-4">
                        <Link
                            :href="route('login')"
                            class="text-gray-700 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white font-medium"
                        >
                            Log in
                        </Link>

                        <Link
                            v-if="canRegister"
                            :href="route('register')"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                        >
                            Get Started
                        </Link>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <div class="relative pt-32 pb-20 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto">
                <div class="text-center">
                    <h1 class="text-5xl md:text-6xl font-extrabold text-gray-900 dark:text-white mb-6">
                        Generate USPS Shipping Labels
                        <span class="block text-indigo-600 mt-2">Fast, Simple, Reliable</span>
                    </h1>
                    
                    <p class="mt-6 text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto">
                        Create, print, and manage your USPS shipping labels with ease. 
                        Powered by EasyPost API for accurate rates and reliable delivery.
                    </p>

                    <div class="mt-10 flex flex-col sm:flex-row gap-4 justify-center">
                        <Link
                            v-if="canRegister"
                            :href="route('register')"
                            class="inline-flex items-center justify-center px-8 py-4 bg-indigo-600 border border-transparent rounded-lg font-semibold text-base text-white hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-lg"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            Create Your First Label
                        </Link>

                        <Link
                            v-if="canLogin"
                            :href="route('login')"
                            class="inline-flex items-center justify-center px-8 py-4 bg-white dark:bg-gray-800 border-2 border-gray-300 dark:border-gray-600 rounded-lg font-semibold text-base text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                        >
                            Already have an account?
                        </Link>
                    </div>
                </div>

                <!-- Feature Cards -->
                <div class="mt-24 grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Feature 1 -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8 hover:shadow-xl transition-shadow duration-300">
                        <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Instant Labels</h3>
                        <p class="text-gray-600 dark:text-gray-300">
                            Generate USPS shipping labels in seconds. Enter addresses, get the cheapest rate automatically, and print immediately.
                        </p>
                    </div>

                    <!-- Feature 2 -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8 hover:shadow-xl transition-shadow duration-300">
                        <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Track Everything</h3>
                        <p class="text-gray-600 dark:text-gray-300">
                            Keep track of all your shipments in one place. Search, filter, and monitor delivery status with ease.
                        </p>
                    </div>

                    <!-- Feature 3 -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8 hover:shadow-xl transition-shadow duration-300">
                        <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Secure & Private</h3>
                        <p class="text-gray-600 dark:text-gray-300">
                            Your data is protected. Each user has access only to their own labels with enterprise-grade security.
                        </p>
                    </div>
                </div>

                <!-- Stats Section -->
                <div class="mt-24 bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-12">
                    <div class="text-center mb-12">
                        <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">How It Works</h2>
                        <p class="text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">
                            Get your shipping labels in just a few simple steps
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                        <div class="text-center">
                            <div class="w-16 h-16 bg-indigo-100 dark:bg-indigo-900 rounded-full flex items-center justify-center mx-auto mb-4">
                                <span class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">1</span>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Sign Up</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Create your free account in seconds</p>
                        </div>

                        <div class="text-center">
                            <div class="w-16 h-16 bg-indigo-100 dark:bg-indigo-900 rounded-full flex items-center justify-center mx-auto mb-4">
                                <span class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">2</span>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Enter Details</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Add from/to addresses and package info</p>
                        </div>

                        <div class="text-center">
                            <div class="w-16 h-16 bg-indigo-100 dark:bg-indigo-900 rounded-full flex items-center justify-center mx-auto mb-4">
                                <span class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">3</span>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Generate Label</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Get your USPS label instantly</p>
                        </div>

                        <div class="text-center">
                            <div class="w-16 h-16 bg-indigo-100 dark:bg-indigo-900 rounded-full flex items-center justify-center mx-auto mb-4">
                                <span class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">4</span>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Print & Ship</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Print your label and drop it off</p>
                        </div>
                    </div>
                </div>

                <!-- CTA Section -->
                <div class="mt-24 text-center">
                    <h2 class="text-4xl font-bold text-gray-900 dark:text-white mb-6">
                        Ready to Ship Smarter?
                    </h2>
                    <p class="text-xl text-gray-600 dark:text-gray-300 mb-8 max-w-2xl mx-auto">
                        Join thousands of businesses and individuals who trust our platform for their shipping needs.
                    </p>
                    <Link
                        v-if="canRegister"
                        :href="route('register')"
                        class="inline-flex items-center px-8 py-4 bg-indigo-600 border border-transparent rounded-lg font-semibold text-base text-white hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-lg"
                    >
                        Get Started for Free
                        <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </Link>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700 mt-24">
            <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
                <div class="text-center text-gray-500 dark:text-gray-400">
                    <p class="text-sm">
                        Powered by EasyPost API • Built with Laravel {{ $page.props.laravelVersion }} & Vue.js
                    </p>
                    <p class="text-xs mt-2">
                        © 2024 ShipLabel. All rights reserved.
                    </p>
                </div>
            </div>
        </footer>
    </div>
</template>

<style scoped>
/* Add any component-specific styles if needed */
</style>
