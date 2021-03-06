<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreacionDeTablasEfectoresDatosGeograficos extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('efectores.datos_geograficos', function (Blueprint $table) {
			$table->integer('id_efector')->primary();
			$table->char('id_provincia', 2);
			$table->smallInteger('id_departamento');
			$table->smallInteger('id_localidad');
			$table->string('ciudad', 200)->nullable();
			$table->nullableTimestamps();
			$table->float('latitud')->nullable();
			$table->float('longitud')->nullable();
			//$table->integer('msnm')->nullable();

			$table->foreign('id_departamento')->references('id')->on('geo.departamentos');
			$table->foreign('id_localidad')->references('id')->on('geo.localidades');
			$table->foreign('id_efector')->references('id_efector')->on('efectores.efectores')->onUpdate('NO ACTION')->onDelete('CASCADE');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::drop('efectores.datos_geograficos');
	}
}
