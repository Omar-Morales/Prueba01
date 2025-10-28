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
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->onDelete('set null');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('tipodocumento_id')->constrained('tipodocumento')->onDelete('restrict');
            $table->decimal('total_price',10,2);
            $table->date('sale_date');
            $table->enum('status',['pending','completed','cancelled'])->default('completed');
            $table->string('codigo')->nullable();
            $table->enum('payment_method',['cash','card','transfer'])->nullable();
            $table->timestamps();
        });

            Schema::create('venta_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('venta_id');
            $table->string('accion'); // created, updated, cancelled
            $table->unsignedBigInteger('user_id');
            $table->string('ip')->nullable();
            $table->json('datos_antes')->nullable();
            $table->json('datos_despues')->nullable();
            $table->timestamps();

            $table->foreign('venta_id')->references('id')->on('ventas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventas');
        Schema::dropIfExists('venta_logs');
    }
};
