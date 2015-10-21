<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableIndicadoresIndicadoresMedicaRangos extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('indicadores.indicadores_medica_rangos', function(Blueprint $table)
		{
			$table->char('id_provincia', 2);
			$table->integer('periodo')->unsigned();
			$table->string('codigo_indicador', 5);
			$table->integer('max_rojo')->unsigned();
			$table->integer('max_verde')->unsigned();
			$table->integer('min_rojo')->unsigned();
			$table->integer('min_verde')->unsigned();
			$table->primary(['id_provincia', 'periodo', 'codigo_indicador']);
			$table->foreign('id_provincia')->references('id_entidad')->on('sistema.entidades');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('indicadores.indicadores_medica_rangos');
	}
}
