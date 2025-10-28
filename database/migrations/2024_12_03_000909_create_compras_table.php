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
        Schema::create('compras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->onDelete('set null');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('tipodocumento_id')->constrained('tipodocumento')->onDelete('restrict');
            $table->decimal('total_cost',10,2);
            $table->date('purchase_date');
            $table->enum('status',['pending','completed','cancelled'])->default('completed');
            $table->string('codigo')->nullable();
            $table->timestamps();
        });

        Schema::create('compra_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('compra_id');
            $table->string('accion'); // created, updated, cancelled
            $table->unsignedBigInteger('user_id');
            $table->string('ip')->nullable();
            $table->json('datos_antes')->nullable();
            $table->json('datos_despues')->nullable();
            $table->timestamps();

            $table->foreign('compra_id')->references('id')->on('compras')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compras');
        Schema::dropIfExists('compra_logs');
    }
};
