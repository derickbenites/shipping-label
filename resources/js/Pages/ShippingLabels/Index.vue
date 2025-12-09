<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    labels: Object,
    stats: Object,
    filters: Object,
});

const search = ref(props.filters.search || '');
const status = ref(props.filters.status || '');

watch([search, status], () => {
    router.get('/shipping-labels', {
        search: search.value,
        status: status.value,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
});

const getStatusColor = (status) => {
    const colors = {
        'created': 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
        'pending': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
        'cancelled': 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
    };
    return colors[status] || colors['pending'];
};

const formatAddress = (label, type) => {
    if (!label) return 'N/A';
    const city = type === 'from' ? label.from_city : label.to_city;
    const state = type === 'from' ? label.from_state : label.to_state;
    return `${city || ''}, ${state || ''}`;
};

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(amount || 0);
};
</script>

<template>
    <Head title="My Labels" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    My Shipping Labels
                </h2>
                <Link
                    :href="route('shipping-labels.create')"
                    class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm text-white hover:bg-indigo-700"
                >
                    New Label
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Statistics Cards -->
                <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-gray-800 p-5">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Total</dt>
                        <dd class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ stats.total }}</dd>
                    </div>
                    <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-gray-800 p-5">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Active</dt>
                        <dd class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ stats.active }}</dd>
                    </div>
                    <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-gray-800 p-5">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Cancelled</dt>
                        <dd class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ stats.cancelled }}</dd>
                    </div>
                    <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-gray-800 p-5">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Spent</dt>
                        <dd class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ formatCurrency(stats.total_spent) }}</dd>
                    </div>
                </div>

                <!-- Filters -->
                <div class="mb-6 bg-white shadow-sm sm:rounded-lg dark:bg-gray-800 p-6">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Search</label>
                            <input
                                v-model="search"
                                type="text"
                                placeholder="Tracking, name..."
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                            <select
                                v-model="status"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                            >
                                <option value="">All</option>
                                <option value="created">Created</option>
                                <option value="pending">Pending</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Labels Table -->
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-900">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">From → To</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Tracking</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Cost</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Date</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                <tr v-for="label in labels.data" :key="label.id" class="hover:bg-gray-50 dark:hover:bg-gray-900">
                                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">#{{ label.id }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                                        {{ formatAddress(label, 'from') }} → {{ formatAddress(label, 'to') }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                        <code class="text-xs">{{ label.tracking_code || 'N/A' }}</code>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                                        {{ formatCurrency(label.rate) }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span :class="getStatusColor(label.status)" class="inline-flex rounded-full px-2 text-xs font-semibold">
                                            {{ label.status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                        {{ new Date(label.created_at).toLocaleDateString() }}
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm">
                                        <Link
                                            :href="route('shipping-labels.show', label.id)"
                                            class="text-indigo-600 hover:text-indigo-900"
                                        >
                                            View
                                        </Link>
                                    </td>
                                </tr>
                                <tr v-if="labels.data.length === 0">
                                    <td colspan="7" class="px-6 py-12 text-center text-sm text-gray-500">
                                        <p class="font-medium">No labels found</p>
                                        <div class="mt-6">
                                            <Link
                                                :href="route('shipping-labels.create')"
                                                class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm text-white hover:bg-indigo-700"
                                            >
                                                New Label
                                            </Link>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div v-if="labels.data.length > 0" class="border-t px-4 py-3 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-700 dark:text-gray-300">
                                Showing {{ labels.from }} to {{ labels.to }} of {{ labels.total }}
                            </div>
                            <div class="flex space-x-2">
                                <Link
                                    v-for="link in labels.links"
                                    :key="link.label"
                                    :href="link.url"
                                    :class="[link.active ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700 dark:bg-gray-800', 'rounded px-3 py-2 text-sm']"
                                    v-html="link.label"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
