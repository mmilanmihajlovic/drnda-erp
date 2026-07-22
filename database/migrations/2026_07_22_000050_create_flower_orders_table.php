<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('flower_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();        // CV-2026-001
            $table->foreignId('case_id')->constrained('funeral_cases')->cascadeOnDelete();
            $table->string('customer_name');                 // kupac ili porodica
            $table->string('customer_phone')->nullable();
            $table->dateTime('delivery_at')->nullable();     // datum i vreme isporuke
            $table->string('delivery_location')->nullable(); // lokacija isporuke
            $table->text('ribbon_text')->nullable();         // tekst trake
            $table->text('note')->nullable();
            $table->enum('status', ['aktivan', 'zavrsen', 'otkazan'])->default('aktivan');
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('flower_orders'); }
};
