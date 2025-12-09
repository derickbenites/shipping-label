<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import axios from 'axios';

const props = defineProps({
    label: Object,
});

const formatAddress = (label, type) => {
    if (!label) return 'N/A';
    const name = type === 'from' ? label.from_name : label.to_name;
    const street1 = type === 'from' ? label.from_street1 : label.to_street1;
    const street2 = type === 'from' ? label.from_street2 : label.to_street2;
    const city = type === 'from' ? label.from_city : label.to_city;
    const state = type === 'from' ? label.from_state : label.to_state;
    const zip = type === 'from' ? label.from_zip : label.to_zip;
    const phone = type === 'from' ? label.from_phone : label.to_phone;

    return [
        name,
        street1,
        street2,
        `${city}, ${state} ${zip}`,
        phone,
    ].filter(Boolean).join('\n');
};

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(amount || 0);
};

const getStatusColor = (status) => {
    const colors = {
        'created': 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
        'pending': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
        'cancelled': 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
    };
    return colors[status] || colors['pending'];
};

const printLabel = () => {
    if (props.label.label_url) {
        window.open(props.label.label_url, '_blank');
    }
};

const cancelLabel = async () => {
    if (!confirm('Are you sure you want to cancel this label?')) return;

    try {
        await axios.delete(route('shipping-labels.destroy', props.label.id));
        alert('Label cancelled successfully!');
        router.visit(route('shipping-labels.index'));
    } catch (error) {
        alert('Error cancelling label: ' + (error.response?.data?.message || error.message));
    }
};

const trackLabel = () => {
    if (props.label.tracking_code) {
        window.open(`https://tools.usps.com/go/TrackConfirmAction?tLabels=${props.label.tracking_code}`, '_blank');
    }
};
</script>

<template>
    <Head :title="`Label #${label.id}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Label #{{ label.id }}
                </h2>
                <Link
                    :href="route('shipping-labels.index')"
                    class="text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400"
                >
                    ← Back to List
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-5xl sm:px-6 lg:px-8">
                <!-- Status and Actions -->
                <div class="mb-6 flex items-center justify-between rounded-lg bg-white p-6 shadow dark:bg-gray-800">
                    <div class="flex items-center space-x-4">
                        <span
                            :class="getStatusColor(label.status)"
                            class="inline-flex rounded-full px-4 py-2 text-sm font-semibold"
                        >
                            Status: {{ label.status }}
                        </span>
                        <span class="text-sm text-gray-500 dark:text-gray-400">
                            Created at: {{ new Date(label.created_at).toLocaleString('en-US') }}
                        </span>
                    </div>
                    <div class="flex space-x-2">
                        <button
                            v-if="label.label_url"
                            @click="printLabel"
                            class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm text-white hover:bg-indigo-700"
                        >
                            🖨️ Print
                        </button>
                        <button
                            v-if="label.tracking_code"
                            @click="trackLabel"
                            class="inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-sm text-white hover:bg-blue-700"
                        >
                            📦 Track
                        </button>
                        <button
                            v-if="label.status === 'created' || label.status === 'purchased'"
                            @click="cancelLabel"
                            class="inline-flex items-center rounded-md bg-red-600 px-4 py-2 text-sm text-white hover:bg-red-700"
                        >
                            ❌ Cancel
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <!-- Label Preview -->
                    <div class="lg:col-span-2">
                        <div class="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
                            <h3 class="mb-4 text-lg font-medium text-gray-900 dark:text-gray-100">
                                📄 Label Preview
                            </h3>
                            <div v-if="label.label_url" class="rounded-lg border border-gray-300 dark:border-gray-700">
                                <iframe
                                    :src="label.label_url"
                                    class="h-96 w-full rounded-lg"
                                    frameborder="0"
                                ></iframe>
                            </div>
                            <div v-else class="rounded-lg border border-gray-300 p-12 text-center dark:border-gray-700">
                                <p class="text-gray-500 dark:text-gray-400">
                                    Label not available
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Shipping Info -->
                    <div class="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
                        <h3 class="mb-4 text-lg font-medium text-gray-900 dark:text-gray-100">
                            📦 Shipping Information
                        </h3>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                    Tracking Code
                                </dt>
                                <dd class="mt-1">
                                    <code class="rounded bg-gray-100 px-2 py-1 text-sm dark:bg-gray-900">
                                        {{ label.tracking_code || 'N/A' }}
                                    </code>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                    Shipping Cost
                                </dt>
                                <dd class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">
                                    {{ formatCurrency(label.rate) }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                    EasyPost ID
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                    {{ label.easypost_shipment_id }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                    Package Details
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                    Weight: {{ label.weight }} oz
                                    <span v-if="label.length">
                                        | Dimensions: {{ label.length }} x {{ label.width }} x {{ label.height }} in
                                    </span>
                                </dd>
                            </div>
                        </dl>
                    </div>

                    <!-- Addresses -->
                    <div class="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
                        <h3 class="mb-4 text-lg font-medium text-gray-900 dark:text-gray-100">
                            📍 Addresses
                        </h3>
                        <div class="space-y-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                    From (Origin)
                                </dt>
                                <dd class="mt-1 whitespace-pre-line text-sm text-gray-900 dark:text-gray-100">
                                    {{ formatAddress(label, 'from') }}
                                </dd>
                            </div>
                            <div class="border-t pt-4 dark:border-gray-700">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                    To (Destination)
                                </dt>
                                <dd class="mt-1 whitespace-pre-line text-sm text-gray-900 dark:text-gray-100">
                                    {{ formatAddress(label, 'to') }}
                                </dd>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
