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
        Schema::create('businesses', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('corporation_type')->nullable();
            $table->string('reg_num')->nullable();
            $table->string('tin')->nullable();
            $table->string('business_name')->nullable();
            $table->string('franchise')->nullable();
            $table->string('building_no', 50)->nullable();
            $table->string('building_name', 50)->nullable();
            $table->string('block_no', 50)->nullable();
            $table->string('lot_no', 50)->nullable();
            $table->string('street', 50)->nullable();
            $table->string('subdivision', 50)->nullable();
            $table->integer('region_id')->nullable();
            $table->integer('province_id')->nullable();
            $table->integer('municipality_id')->nullable();
            $table->integer('barangay_id')->nullable();
            $table->integer('zip_code')->nullable();
            $table->string('district', 30)->nullable();
            $table->string('complete_address')->nullable();
            $table->string('docs_business_reg')->nullable();
            $table->string('docs_business_permit')->nullable();
            $table->string('docs_bir_2303')->nullable();
            $table->string('pic_name', 50)->nullable();
            $table->string('pic_ctc_no', 15)->nullable();
            $table->string('pic_email', 50)->nullable();
            $table->string('docs_autorization_form')->nullable();
            $table->integer('payment_id')->nullable();
            $table->decimal('amount', 8, 2)->nullable();
            $table->string('status', 50)->nullable();
            $table->string('qr_code')->nullable();
            $table->boolean('is_active')->default(1);
            $table->integer('created_by');
            $table->timestamp('created_at');
            $table->integer('updated_by')->nullable();
            $table->timestamp('updated_at');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('businesses');
    }
};
