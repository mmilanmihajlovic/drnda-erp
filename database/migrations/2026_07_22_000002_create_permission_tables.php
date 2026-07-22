<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('permissions', function (Blueprint $t) {
            $t->bigIncrements('id'); $t->string('name'); $t->string('guard_name'); $t->timestamps();
            $t->unique(['name','guard_name']);
        });
        Schema::create('roles', function (Blueprint $t) {
            $t->bigIncrements('id'); $t->string('name'); $t->string('guard_name'); $t->timestamps();
            $t->unique(['name','guard_name']);
        });
        Schema::create('model_has_permissions', function (Blueprint $t) {
            $t->unsignedBigInteger('permission_id'); $t->string('model_type'); $t->unsignedBigInteger('model_id');
            $t->index(['model_id','model_type'],'model_has_permissions_model_id_model_type_index');
            $t->foreign('permission_id')->references('id')->on('permissions')->onDelete('cascade');
            $t->primary(['permission_id','model_id','model_type']);
        });
        Schema::create('model_has_roles', function (Blueprint $t) {
            $t->unsignedBigInteger('role_id'); $t->string('model_type'); $t->unsignedBigInteger('model_id');
            $t->index(['model_id','model_type'],'model_has_roles_model_id_model_type_index');
            $t->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            $t->primary(['role_id','model_id','model_type']);
        });
        Schema::create('role_has_permissions', function (Blueprint $t) {
            $t->unsignedBigInteger('permission_id'); $t->unsignedBigInteger('role_id');
            $t->foreign('permission_id')->references('id')->on('permissions')->onDelete('cascade');
            $t->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            $t->primary(['permission_id','role_id']);
        });
    }
    public function down(): void {
        Schema::drop('role_has_permissions'); Schema::drop('model_has_roles');
        Schema::drop('model_has_permissions'); Schema::drop('roles'); Schema::drop('permissions');
    }
};
