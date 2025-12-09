<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateShippingLabelRequest;
use App\Models\ShippingLabel;
use App\Services\EasyPostService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ShippingLabelController extends Controller
{
    protected EasyPostService $easyPostService;

    public function __construct(EasyPostService $easyPostService)
    {
        $this->easyPostService = $easyPostService;
    }
    /**
     * Display a listing of the user's shipping labels.
     */
    public function index(Request $request): Response
    {
        $query = auth()->user()->shippingLabels()->latest();

        // Search filter
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('tracking_code', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%")
                  ->orWhere('from_name', 'like', "%{$search}%")
                  ->orWhere('to_name', 'like', "%{$search}%")
                  ->orWhere('from_city', 'like', "%{$search}%")
                  ->orWhere('to_city', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $labels = $query->paginate(10)->withQueryString();

        // Calculate statistics
        $stats = [
            'total' => auth()->user()->shippingLabels()->count(),
            'active' => auth()->user()->shippingLabels()->whereIn('status', ['created', 'purchased'])->count(),
            'cancelled' => auth()->user()->shippingLabels()->where('status', 'cancelled')->count(),
            'total_spent' => auth()->user()->shippingLabels()->whereIn('status', ['created', 'purchased'])->sum('rate'),
        ];

        return Inertia::render('ShippingLabels/Index', [
            'labels' => $labels,
            'stats' => $stats,
            'filters' => [
                'search' => $request->search,
                'status' => $request->status,
            ],
        ]);
    }

    /**
     * Show the form for creating a new shipping label.
     */
    public function create(): Response
    {
        return Inertia::render('ShippingLabels/Create');
    }

    /**
     * Display the specified shipping label.
     */
    public function show(string $id): Response
    {
        $label = auth()->user()->shippingLabels()->findOrFail($id);

        return Inertia::render('ShippingLabels/Show', [
            'label' => $label,
        ]);
    }

    /**
     * Store a newly created shipping label.
     */
    public function store(CreateShippingLabelRequest $request): JsonResponse
    {
        try {
            $fromAddress = $request->only([
                'from_name', 'from_street1', 'from_street2', 'from_city',
                'from_state', 'from_zip', 'from_phone', 'from_email',
            ]);
            $toAddress = $request->only([
                'to_name', 'to_street1', 'to_street2', 'to_city',
                'to_state', 'to_zip', 'to_phone', 'to_email',
            ]);
            $parcelDetails = $request->only(['weight', 'length', 'width', 'height']);

            $easypostData = $this->easyPostService->createShippingLabel(
                $this->formatAddress($fromAddress, 'from'),
                $this->formatAddress($toAddress, 'to'),
                $this->formatParcel($parcelDetails)
            );

            // Get carrier and service from easypostData shipment
            $shipment = $easypostData['shipment'] ?? null;
            $carrier = $shipment ? ($shipment->selected_rate->carrier ?? 'USPS') : 'USPS';
            $service = $shipment ? ($shipment->selected_rate->service ?? 'First') : 'First';

            // Prepare data for database insertion
            $labelData = [
                // EasyPost data
                'easypost_shipment_id' => $easypostData['easypost_shipment_id'],
                'label_url' => $easypostData['easypost_label_url'],
                'tracking_code' => $easypostData['easypost_tracking_code'],
                'status' => $easypostData['status'],
                'rate' => $easypostData['cost'],
                'carrier' => $carrier,
                'service' => $service,

                // From address
                'from_name' => $fromAddress['from_name'],
                'from_street1' => $fromAddress['from_street1'],
                'from_street2' => $fromAddress['from_street2'] ?? null,
                'from_city' => $fromAddress['from_city'],
                'from_state' => $fromAddress['from_state'],
                'from_zip' => $fromAddress['from_zip'],
                'from_country' => 'US',
                'from_phone' => $fromAddress['from_phone'] ?? null,

                // To address
                'to_name' => $toAddress['to_name'],
                'to_street1' => $toAddress['to_street1'],
                'to_street2' => $toAddress['to_street2'] ?? null,
                'to_city' => $toAddress['to_city'],
                'to_state' => $toAddress['to_state'],
                'to_zip' => $toAddress['to_zip'],
                'to_country' => 'US',
                'to_phone' => $toAddress['to_phone'] ?? null,

                // Parcel details
                'weight' => $parcelDetails['weight'],
                'length' => $parcelDetails['length'] ?? null,
                'width' => $parcelDetails['width'] ?? null,
                'height' => $parcelDetails['height'] ?? null,
            ];

            $label = auth()->user()->shippingLabels()->create($labelData);

            return response()->json([
                'success' => true,
                'message' => 'Shipping label created successfully',
                'data' => $label
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get shipping rates without creating a label.
     */
    public function getRates(CreateShippingLabelRequest $request): JsonResponse
    {
        try {
            $fromAddress = $request->only([
                'from_name', 'from_street1', 'from_street2', 'from_city',
                'from_state', 'from_zip', 'from_phone', 'from_email',
            ]);
            $toAddress = $request->only([
                'to_name', 'to_street1', 'to_street2', 'to_city',
                'to_state', 'to_zip', 'to_phone', 'to_email',
            ]);
            $parcelDetails = $request->only(['weight', 'length', 'width', 'height']);

            $rates = $this->easyPostService->getRates(
                $this->formatAddress($fromAddress, 'from'),
                $this->formatAddress($toAddress, 'to'),
                $this->formatParcel($parcelDetails)
            );

            return response()->json([
                'success' => true,
                'data' => $rates
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel a shipping label.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $label = auth()->user()->shippingLabels()->findOrFail($id);

            $this->easyPostService->cancelShippingLabel($label->easypost_shipment_id);
            $label->update(['status' => 'cancelled']);

            return response()->json([
                'success' => true,
                'message' => 'Shipping label cancelled successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Format address data for EasyPost API.
     */
    private function formatAddress(array $addressData, string $prefix): array
    {
        return [
            'name' => $addressData[$prefix . '_name'] ?? null,
            'street1' => $addressData[$prefix . '_street1'] ?? null,
            'street2' => $addressData[$prefix . '_street2'] ?? null,
            'city' => $addressData[$prefix . '_city'] ?? null,
            'state' => $addressData[$prefix . '_state'] ?? null,
            'zip' => $addressData[$prefix . '_zip'] ?? null,
            'country' => 'US',
            'phone' => $addressData[$prefix . '_phone'] ?? null,
            'email' => $addressData[$prefix . '_email'] ?? null,
        ];
    }

    /**
     * Format parcel data for EasyPost API.
     */
    private function formatParcel(array $parcelData): array
    {
        $parcel = [
            'weight' => floatval($parcelData['weight'] ?? 0),
        ];

        // Add dimensions if provided
        if (!empty($parcelData['length'])) {
            $parcel['length'] = floatval($parcelData['length']);
        }
        if (!empty($parcelData['width'])) {
            $parcel['width'] = floatval($parcelData['width']);
        }
        if (!empty($parcelData['height'])) {
            $parcel['height'] = floatval($parcelData['height']);
        }

        return $parcel;
    }
}
