<?php

namespace App\Services;

use EasyPost\EasyPostClient;
use EasyPost\Exception\Api\ApiException;
use Illuminate\Support\Facades\Log;

class EasyPostService
{
    private EasyPostClient $client;

    public function __construct()
    {
        $apiKey = config('services.easypost.api_key');

        if (empty($apiKey)) {
            throw new \Exception('EasyPost API key not configured');
        }

        $this->client = new EasyPostClient($apiKey);
    }

    /**
     * Create a shipment and purchase the cheapest USPS rate
     */
    public function createShipment(array $fromAddress, array $toAddress, array $parcel): array
    {
        try {
            // Create addresses
            $from = $this->client->address->create($fromAddress);
            $to = $this->client->address->create($toAddress);

            // Create parcel
            $parcelObj = $this->client->parcel->create($parcel);

            // Create shipment
            $shipment = $this->client->shipment->create([
                'from_address' => $from,
                'to_address' => $to,
                'parcel' => $parcelObj,
            ]);

            // Get cheapest USPS rate
            $rate = $this->getCheapestUSPSRate($shipment->rates);

            if (!$rate) {
                throw new \Exception('No USPS rates available for this shipment');
            }

            // Buy the shipment with the selected rate
            $shipment = $this->client->shipment->buy($shipment->id, ['rate' => $rate]);

            return [
                'success' => true,
                'shipment_id' => $shipment->id,
                'tracking_code' => $shipment->tracking_code,
                'label_url' => $shipment->postage_label->label_url,
                'label_pdf_url' => $shipment->postage_label->label_pdf_url ?? null,
                'label_png_url' => $shipment->postage_label->label_png_url ?? null,
                'carrier' => $rate->carrier,
                'service' => $rate->service,
                'rate' => $rate->rate,
                'shipment' => $shipment,
            ];

        } catch (ApiException $e) {
            Log::error('EasyPost API Error: ' . $e->getMessage());
            throw new \Exception('Failed to create shipment: ' . $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Shipment Creation Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get rates for a shipment without purchasing
     */
    public function getRates(array $fromAddress, array $toAddress, array $parcel): array
    {
        try {
            $from = $this->client->address->create($fromAddress);
            $to = $this->client->address->create($toAddress);
            $parcelObj = $this->client->parcel->create($parcel);

            $shipment = $this->client->shipment->create([
                'from_address' => $from,
                'to_address' => $to,
                'parcel' => $parcelObj,
            ]);

            $uspsRates = array_filter($shipment->rates, function($rate) {
                return strtoupper($rate->carrier) === 'USPS';
            });

            return array_map(function($rate) {
                return [
                    'id' => $rate->id,
                    'carrier' => $rate->carrier,
                    'service' => $rate->service,
                    'rate' => $rate->rate,
                    'delivery_days' => $rate->delivery_days ?? null,
                ];
            }, array_values($uspsRates));

        } catch (ApiException $e) {
            Log::error('EasyPost Get Rates Error: ' . $e->getMessage());
            throw new \Exception('Failed to get rates: ' . $e->getMessage());
        }
    }

    /**
     * Refund a shipment (if possible)
     */
    public function refundShipment(string $shipmentId): bool
    {
        try {
            $this->client->shipment->refund($shipmentId);
            return true;
        } catch (ApiException $e) {
            Log::error('EasyPost Refund Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get the cheapest USPS rate from available rates
     */
    private function getCheapestUSPSRate(array $rates)
    {
        $uspsRates = array_filter($rates, function($rate) {
            return strtoupper($rate->carrier) === 'USPS';
        });

        if (empty($uspsRates)) {
            return null;
        }

        usort($uspsRates, function($a, $b) {
            return floatval($a->rate) <=> floatval($b->rate);
        });

        return $uspsRates[0];
    }

}

