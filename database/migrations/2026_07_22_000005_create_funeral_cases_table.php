<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('funeral_cases', function (Blueprint $table) {
            $table->id();
            $table->string('case_number')->unique();
            $table->string('case_type')->default('standard');
            $table->string('deceased_name');
            $table->string('family_contact_name')->nullable();
            $table->string('family_contact_phone')->nullable();
            $table->string('location')->nullable();
            $table->string('route_from')->nullable();
            $table->string('route_to')->nullable();
            $table->dateTime('funeral_at')->nullable();
            $table->string('status')->default('aktivan');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('funeral_cases'); }
};
