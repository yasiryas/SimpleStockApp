<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->string('no_resi')->nullable()->after('no_shipment');
            $table->string('tujuan')->nullable()->after('no_resi');
            $table->enum('status', ['draft', 'dikirim', 'selesai'])->default('draft')->after('catatan');
        });
    }

    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropColumn(['no_resi', 'tujuan', 'status']);
        });
    }
};
