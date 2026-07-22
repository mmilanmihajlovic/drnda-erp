<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('invoices', function (Blueprint $table) {
            $table->text('storno_reason')->nullable()->after('generated_by');
            $table->foreignId('storno_by')->nullable()->constrained('users')->nullOnDelete()->after('storno_reason');
            $table->timestamp('storno_at')->nullable()->after('storno_by');
        });
    }
    public function down(): void {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['storno_reason', 'storno_by', 'storno_at']);
        });
    }
};
