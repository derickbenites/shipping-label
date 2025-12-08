<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShippingLabel extends Model
{
    protected $fillable = [
        'user_id',
        'easypost_shipment_id',
        'easypost_label_id',
        'from_name',
        'from_company',
        'from_street1',
        'from_street2',
        'from_city',
        'from_state',
        'from_zip',
        'from_country',
        'from_phone',
        'to_name',
        'to_company',
        'to_street1',
        'to_street2',
        'to_city',
        'to_state',
        'to_zip',
        'to_country',
        'to_phone',
        'weight',
        'length',
        'width',
        'height',
        'carrier',
        'service',
        'rate',
        'tracking_code',
        'label_url',
        'label_pdf_url',
        'label_png_url',
        'status',
    ];

    protected $casts = [
        'weight' => 'decimal:2',
        'length' => 'decimal:2',
        'width' => 'decimal:2',
        'height' => 'decimal:2',
        'rate' => 'decimal:2',
    ];

    /**
     * Get the user that owns the shipping label
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope query to only include labels for a specific user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
