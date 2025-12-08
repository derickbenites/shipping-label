<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('shipping_labels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // EasyPost IDs
            $table->string('easypost_shipment_id')->unique();
            $table->string('easypost_label_id')->nullable();

            // Origin Address
            $table->string('from_name');
            $table->string('from_company')->nullable();
            $table->string('from_street1');
            $table->string('from_street2')->nullable();
            $table->string('from_city');
            $table->string('from_state', 2);
            $table->string('from_zip', 10);
            $table->string('from_country', 2)->default('US');
            $table->string('from_phone')->nullable();

            // Destination Address
            $table->string('to_name');
            $table->string('to_company')->nullable();
            $table->string('to_street1');
            $table->string('to_street2')->nullable();
            $table->string('to_city');
            $table->string('to_state', 2);
            $table->string('to_zip', 10);
            $table->string('to_country', 2)->default('US');
            $table->string('to_phone')->nullable();

            // Package Details
            $table->decimal('weight', 10, 2); // in ounces
            $table->decimal('length', 10, 2)->nullable();
            $table->decimal('width', 10, 2)->nullable();
            $table->decimal('height', 10, 2)->nullable();

            // Shipping Details
            $table->string('carrier')->default('USPS');
            $table->string('service');
            $table->decimal('rate', 10, 2);
            $table->string('tracking_code')->nullable();

            // Label URLs
            $table->text('label_url')->nullable();
            $table->text('label_pdf_url')->nullable();
            $table->text('label_png_url')->nullable();

            // Status
            $table->enum('status', ['created', 'purchased', 'cancelled', 'failed'])->default('created');

            $table->timestamps();

            // Indexes
            $table->index('user_id');
            $table->index('tracking_code');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_labels');
    }
};
