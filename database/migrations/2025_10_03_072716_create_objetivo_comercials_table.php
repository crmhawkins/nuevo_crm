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
        Schema::create('objetivo_comercials', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('comercial_id'); // ID del comercial
            $table->unsignedBigInteger('admin_user_id'); // ID del admin que establece el objetivo
            $table->date('fecha_inicio'); // Fecha de inicio del objetivo
            $table->date('fecha_fin'); // Fecha de fin del objetivo
            $table->string('tipo_objetivo'); // 'diario' o 'mensual'
            
            // Objetivos de visitas
            $table->integer('visitas_presenciales_diarias')->default(0);
            $table->integer('visitas_telefonicas_diarias')->default(0);
            $table->integer('visitas_mixtas_diarias')->default(0);
            
            // Objetivos de ventas mensuales
            $table->integer('planes_esenciales_mensuales')->default(0);
            $table->integer('planes_profesionales_mensuales')->default(0);
            $table->integer('planes_avanzados_mensuales')->default(0);
            $table->decimal('ventas_euros_mensuales', 10, 2)->default(0);
            
            // Precios de los planes
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
        Schema::dropIfExists('objetivo_comercials');
    }
};
