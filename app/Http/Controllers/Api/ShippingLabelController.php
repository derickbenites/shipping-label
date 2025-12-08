<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateShippingLabelRequest;
use App\Models\ShippingLabel;
use App\Services\EasyPostService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ShippingLabelController extends Controller
{
    private EasyPostService $easyPostService;

    public function __construct(EasyPostService $easyPostService)
    {
        $this->easyPostService = $easyPostService;
    }

    /**
     * Display a listing of user's shipping labels
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();

        $labels = ShippingLabel::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $labels,
        ]);
    }

    /**
     * Create a new shipping label
     */
    public function store(CreateShippingLabelRequest $request): JsonResponse
    {
        $user = Auth::user();

        try {
            DB::beginTransaction();

            // Prepare address data for EasyPost
            $fromAddress = [
                'name' => $request->from_name,
                'company' => $request->from_company,
                'street1' => $request->from_street1,
                'street2' => $request->from_street2,
                'city' => $request->from_city,
                'state' => $request->from_state,
                'zip' => $request->from_zip,
                'country' => 'US',
                'phone' => $request->from_phone,
            ];

            $toAddress = [
                'name' => $request->to_name,
                'company' => $request->to_company,
                'street1' => $request->to_street1,
                'street2' => $request->to_street2,
                'city' => $request->to_city,
                'state' => $request->to_state,
                'zip' => $request->to_zip,
                'country' => 'US',
                'phone' => $request->to_phone,
            ];

            $parcel = [
                'weight' => $request->weight,
                'length' => $request->length,
                'width' => $request->width,
                'height' => $request->height,
            ];

            // Create shipment via EasyPost
            $shipmentData = $this->easyPostService->createShipment(
                $fromAddress,
                $toAddress,
                $parcel
            );

            // Save to database
            $label = ShippingLabel::create([
                'user_id' => $user->id,
                'easypost_shipment_id' => $shipmentData['shipment_id'],
                'from_name' => $request->from_name,
                'from_company' => $request->from_company,
                'from_street1' => $request->from_street1,
                'from_street2' => $request->from_street2,
                'from_city' => $request->from_city,
                'from_state' => $request->from_state,
                'from_zip' => $request->from_zip,
                'from_country' => 'US',
                'from_phone' => $request->from_phone,
                'to_name' => $request->to_name,
                'to_company' => $request->to_company,
                'to_street1' => $request->to_street1,
                'to_street2' => $request->to_street2,
                'to_city' => $request->to_city,
                'to_state' => $request->to_state,
                'to_zip' => $request->to_zip,
                'to_country' => 'US',
                'to_phone' => $request->to_phone,
                'weight' => $request->weight,
                'length' => $request->length,
                'width' => $request->width,
                'height' => $request->height,
                'carrier' => $shipmentData['carrier'],
                'service' => $shipmentData['service'],
                'rate' => $shipmentData['rate'],
                'tracking_code' => $shipmentData['tracking_code'],
                'label_url' => $shipmentData['label_url'],
                'label_pdf_url' => $shipmentData['label_pdf_url'],
                'label_png_url' => $shipmentData['label_png_url'],
                'status' => 'purchased',
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Shipping label created successfully',
                'data' => $label,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create shipping label: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to create shipping label',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display a specific shipping label
     */
    public function show(string $id): JsonResponse
    {
        $user = Auth::user();

        $label = ShippingLabel::where('user_id', $user->id)
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $label,
        ]);
    }

    /**
     * Get available rates without creating a label
     */
    public function getRates(CreateShippingLabelRequest $request): JsonResponse
    {
        try {
            $fromAddress = [
                'name' => $request->from_name,
                'street1' => $request->from_street1,
                'street2' => $request->from_street2,
                'city' => $request->from_city,
                'state' => $request->from_state,
                'zip' => $request->from_zip,
                'country' => 'US',
            ];

            $toAddress = [
                'name' => $request->to_name,
                'street1' => $request->to_street1,
                'street2' => $request->to_street2,
                'city' => $request->to_city,
                'state' => $request->to_state,
                'zip' => $request->to_zip,
                'country' => 'US',
            ];

            $parcel = [
                'weight' => $request->weight,
                'length' => $request->length,
                'width' => $request->width,
                'height' => $request->height,
            ];

            $rates = $this->easyPostService->getRates($fromAddress, $toAddress, $parcel);

            return response()->json([
                'success' => true,
                'data' => $rates,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get rates: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to get shipping rates',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Cancel/refund a shipping label
     */
    public function destroy(string $id): JsonResponse
    {
        $user = Auth::user();

        try {
            $label = ShippingLabel::where('user_id', $user->id)
                ->findOrFail($id);

            // Attempt to refund via EasyPost
            $refunded = $this->easyPostService->refundShipment($label->easypost_shipment_id);

            if ($refunded) {
                $label->status = 'cancelled';
                $label->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Shipping label cancelled and refunded successfully',
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to refund shipping label. It may not be eligible for refund.',
            ], 400);

        } catch (\Exception $e) {
            Log::error('Failed to cancel shipping label: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel shipping label',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
