<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import axios from 'axios';

const form = useForm({
    from_name: '',
    from_street1: '',
    from_street2: '',
    from_city: '',
    from_state: '',
    from_zip: '',
    from_phone: '',
    from_email: '',
    to_name: '',
    to_street1: '',
    to_street2: '',
    to_city: '',
    to_state: '',
    to_zip: '',
    to_phone: '',
    to_email: '',
    weight: '',
    length: '',
    width: '',
    height: '',
});

const rates = ref([]);
const showingRates = ref(false);
const loadingRates = ref(false);
const selectedRate = ref(null);

const usStates = [
    'AL', 'AK', 'AZ', 'AR', 'CA', 'CO', 'CT', 'DE', 'FL', 'GA',
    'HI', 'ID', 'IL', 'IN', 'IA', 'KS', 'KY', 'LA', 'ME', 'MD',
    'MA', 'MI', 'MN', 'MS', 'MO', 'MT', 'NE', 'NV', 'NH', 'NJ',
    'NM', 'NY', 'NC', 'ND', 'OH', 'OK', 'OR', 'PA', 'RI', 'SC',
    'SD', 'TN', 'TX', 'UT', 'VT', 'VA', 'WA', 'WV', 'WI', 'WY',
];

const getRates = async () => {
    loadingRates.value = true;
    try {
        const response = await axios.post(route('shipping-labels.rates'), form.data());
        rates.value = response.data.data;
        showingRates.value = true;
    } catch (error) {
        alert('Error getting rates: ' + (error.response?.data?.message || error.message));
    } finally {
        loadingRates.value = false;
    }
};

const submit = async () => {
    if (!form.weight || !form.from_zip || !form.to_zip) {
        alert('Please fill in all required fields');
        return;
    }

    form.processing = true;

    try {
        const response = await axios.post(route('shipping-labels.store'), form.data());
        alert('Label created successfully!');
        window.location.href = route('shipping-labels.show', response.data.data.id);
    } catch (error) {
        alert('Error creating label: ' + (error.response?.data?.message || error.message));
    } finally {
        form.processing = false;
    }
};
</script>

<template>
    <Head title="New Label" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    New Shipping Label
                </h2>
                <Link
                    :href="route('shipping-labels.index')"
                    class="text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400"
                >
                    ← Back
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <form @submit.prevent="submit" class="space-y-6">
                    <!-- From Address -->
                    <div class="bg-white shadow-sm sm:rounded-lg dark:bg-gray-800 p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                            📦 From Address
                        </h3>
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name *</label>
                                <input v-model="form.from_name" type="text" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" />
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Street Address *</label>
                                <input v-model="form.from_street1" type="text" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" />
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Apt, Suite (optional)</label>
                                <input v-model="form.from_street2" type="text" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">City *</label>
                                <input v-model="form.from_city" type="text" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">State *</label>
                                <select v-model="form.from_state" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                                    <option value="">Select...</option>
                                    <option v-for="state in usStates" :key="state" :value="state">{{ state }}</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">ZIP Code *</label>
                                <input v-model="form.from_zip" type="text" required maxlength="5" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Phone</label>
                                <input v-model="form.from_phone" type="text" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" />
                            </div>
                        </div>
                    </div>

                    <!-- To Address -->
                    <div class="bg-white shadow-sm sm:rounded-lg dark:bg-gray-800 p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                            📍 To Address
                        </h3>
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name *</label>
                                <input v-model="form.to_name" type="text" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" />
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Street Address *</label>
                                <input v-model="form.to_street1" type="text" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" />
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Apt, Suite (optional)</label>
                                <input v-model="form.to_street2" type="text" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">City *</label>
                                <input v-model="form.to_city" type="text" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">State *</label>
                                <select v-model="form.to_state" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                                    <option value="">Select...</option>
                                    <option v-for="state in usStates" :key="state" :value="state">{{ state }}</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">ZIP Code *</label>
                                <input v-model="form.to_zip" type="text" required maxlength="5" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Phone</label>
                                <input v-model="form.to_phone" type="text" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" />
                            </div>
                        </div>
                    </div>

                    <!-- Parcel Details -->
                    <div class="bg-white shadow-sm sm:rounded-lg dark:bg-gray-800 p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                            📏 Package Details
                        </h3>
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Weight (oz) *</label>
                                <input v-model="form.weight" type="number" step="0.1" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Length (in)</label>
                                <input v-model="form.length" type="number" step="0.1" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Width (in)</label>
                                <input v-model="form.width" type="number" step="0.1" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Height (in)</label>
                                <input v-model="form.height" type="number" step="0.1" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" />
                            </div>
                        </div>
                    </div>

                    <!-- Rates (if showing) -->
                    <div v-if="showingRates && rates.length > 0" class="bg-white shadow-sm sm:rounded-lg dark:bg-gray-800 p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                            💰 Available Rates
                        </h3>
                        <div class="space-y-2">
                            <div v-for="(rate, index) in rates" :key="index" class="flex items-center justify-between p-3 border rounded dark:border-gray-700">
                                <div>
                                    <span class="font-medium">{{ rate.carrier }} - {{ rate.service }}</span>
                                    <span class="text-sm text-gray-500 ml-2">({{ rate.delivery_days }} days)</span>
                                </div>
                                <span class="font-semibold">${{ rate.rate }} {{ rate.currency }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end space-x-4">
                        <button
                            type="button"
                            @click="getRates"
                            :disabled="loadingRates"
                            class="rounded-md bg-gray-600 px-4 py-2 text-sm text-white hover:bg-gray-700 disabled:opacity-50"
                        >
                            {{ loadingRates ? 'Loading...' : 'Get Rates' }}
                        </button>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="rounded-md bg-indigo-600 px-4 py-2 text-sm text-white hover:bg-indigo-700 disabled:opacity-50"
                        >
                            {{ form.processing ? 'Creating...' : 'Create Label' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
