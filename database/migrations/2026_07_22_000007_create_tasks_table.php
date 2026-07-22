<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('case_department_id')->constrained('case_departments')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('status')->default('novi');
            $table->string('priority')->default('normal');
            $table->foreignId('assigned_worker_id')->nullable()->constrained('workers')->nullOnDelete();
            $table->foreignId('assigned_vehicle_id')->nullable()->constrained('vehicles')->nullOnDelete();
            $table->text('note')->nullable();
            $table->dateTime('due_at')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('tasks'); }
};
