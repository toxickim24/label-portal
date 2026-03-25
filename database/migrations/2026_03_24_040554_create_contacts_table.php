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
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state', 2)->nullable();
            $table->string('zip', 20)->nullable();
            $table->string('contact_type')->nullable(); // buyer, seller, investor, etc.
            $table->string('status')->default('lead'); // lead, prospect, active, closed, archived
            $table->string('source')->nullable(); // referral, website, open house, etc.
            $table->string('priority')->default('medium'); // low, medium, high
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('last_contact_date')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes for better performance
            $table->index('email');
            $table->index('phone');
            $table->index('status');
            $table->index('contact_type');
            $table->index('assigned_to');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
