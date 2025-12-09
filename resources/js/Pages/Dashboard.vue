<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200"
            >
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Welcome Card -->
                <div
                    class="mb-6 overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800"
                >
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg font-semibold mb-2">Welcome!</h3>
                        <p class="text-gray-600 dark:text-gray-400">
                            You're logged in to the shipping label system.
                        </p>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                        Quick Actions
                    </h3>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        <Link
                            :href="route('shipping-labels.create')"
                            class="block rounded-lg bg-indigo-600 p-6 text-white hover:bg-indigo-700 transition"
                        >
                            <div class="flex items-center">
                                <svg class="h-8 w-8 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                <div>
                                    <div class="font-semibold">New Label</div>
                                    <div class="text-sm text-indigo-100">Create a new shipping label</div>
                                </div>
                            </div>
                        </Link>

                        <Link
                            :href="route('shipping-labels.index')"
                            class="block rounded-lg bg-green-600 p-6 text-white hover:bg-green-700 transition"
                        >
                            <div class="flex items-center">
                                <svg class="h-8 w-8 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                <div>
                                    <div class="font-semibold">My Labels</div>
                                    <div class="text-sm text-green-100">View all labels</div>
                                </div>
                            </div>
                        </Link>

                        <Link
                            :href="route('profile.edit')"
                            class="block rounded-lg bg-gray-600 p-6 text-white hover:bg-gray-700 transition"
                        >
                            <div class="flex items-center">
                                <svg class="h-8 w-8 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                <div>
                                    <div class="font-semibold">My Profile</div>
                                    <div class="text-sm text-gray-100">Edit personal information</div>
                                </div>
                            </div>
                        </Link>
                    </div>
                </div>

                <!-- Info Card -->
                <div
                    class="overflow-hidden bg-blue-50 border border-blue-200 shadow-sm sm:rounded-lg dark:bg-blue-900 dark:border-blue-700"
                >
                    <div class="p-6">
                        <div class="flex items-start">
                            <svg class="h-6 w-6 text-blue-600 dark:text-blue-300 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div class="flex-1">
                                <h4 class="text-sm font-semibold text-blue-900 dark:text-blue-100 mb-1">
                                    How to use the system
                                </h4>
                                <p class="text-sm text-blue-800 dark:text-blue-200">
                                    1. Click "New Label" to create a shipping label<br>
                                    2. Fill in from and to addresses (US only)<br>
                                    3. Enter package weight and dimensions<br>
                                    4. Get rates or generate the label directly<br>
                                    5. Print the label and attach it to your package!
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
