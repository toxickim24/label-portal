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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_approved')->default(false)->after('email');
            $table->boolean('is_suspended')->default(false)->after('is_approved');
            $table->timestamp('approved_at')->nullable()->after('is_suspended');
            $table->unsignedBigInteger('approved_by')->nullable()->after('approved_at');
            $table->timestamp('suspended_at')->nullable()->after('approved_by');
            $table->unsignedBigInteger('suspended_by')->nullable()->after('suspended_at');
            $table->softDeletes();

            $table->foreign('approved_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('suspended_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropForeign(['suspended_by']);
            $table->dropColumn([
                'is_approved',
                'is_suspended',
                'approved_at',
                'approved_by',
                'suspended_at',
                'suspended_by',
            ]);
            $table->dropSoftDeletes();
        });
    }
};
