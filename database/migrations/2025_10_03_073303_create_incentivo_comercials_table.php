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
        Schema::create('incentivo_comercials', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('comercial_id'); // ID del comercial
            $table->unsignedBigInteger('admin_user_id'); // ID del admin que establece el incentivo
            $table->date('fecha_inicio'); // Fecha de inicio del incentivo
            $table->date('fecha_fin'); // Fecha de fin del incentivo
            
            // Configuración de incentivos
            $table->decimal('porcentaje_venta', 5, 2)->default(10.00); // 10% de la venta
            $table->decimal('porcentaje_adicional', 5, 2)->default(10.00); // 10% adicional
            $table->integer('min_clientes_mensuales')->default(50); // Mínimo 50 clientes para incentivo adicional
            $table->decimal('min_ventas_mensuales', 10, 2)->default(0); // Mínimo de ventas mensuales
            
            // Precios de planes
            $table->decimal('precio_plan_esencial', 8, 2)->default(19.00);
            $table->decimal('precio_plan_profesional', 8, 2)->default(49.00);
            $table->decimal('precio_plan_avanzado', 8, 2)->default(129.00);
            
            $table->boolean('activo')->default(true);
            $table->text('notas')->nullable();
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('comercial_id')->references('id')->on('admin_user')->onDelete('cascade');
            $table->foreign('admin_user_id')->references('id')->on('admin_user')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incentivo_comercials');
    }
};
