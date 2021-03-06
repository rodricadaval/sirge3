<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMobileTipoConsulta extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('mobile.tipo_consulta', function(Blueprint $table)
		{
			$table->char('tipo', 2);
			$table->string('clase');
			$table->primary(['tipo', 'clase']);
			$table->foreign('tipo')->references('tipo_prestacion')->on('pss.tipo_prestacion');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('mobile.tipo_consulta');
	}
}
