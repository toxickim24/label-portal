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
        Schema::create('cma_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Subject Property (JSON)
            $table->json('subject_property')->nullable();

            // Comparables (JSON array)
            $table->json('comparables')->nullable();

            // Adjustments (JSON)
            $table->json('adjustments')->nullable();

            // Valuation Results
            $table->decimal('valuation_low', 12, 2)->nullable();
            $table->decimal('valuation_avg', 12, 2)->nullable();
            $table->decimal('valuation_high', 12, 2)->nullable();

            // Report Status
            $table->enum('status', ['draft', 'finalized'])->default('draft');

            // Generated PDF
            $table->string('generated_pdf_path')->nullable();

            // Report Title/Name
            $table->string('title')->nullable();

            // Notes
            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cma_reports');
    }
};
