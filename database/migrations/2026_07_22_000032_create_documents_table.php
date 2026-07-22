<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('case_id')->constrained('funeral_cases')->cascadeOnDelete();
            $table->foreignId('department_id')->constrained('departments')->restrictOnDelete();
            $table->string('name');
            $table->string('type'); // npr. smrtovnica, saglasnost, faktura
            $table->string('path'); // storage path
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size')->nullable(); // bytes
            $table->foreignId('uploaded_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('documents'); }
};
