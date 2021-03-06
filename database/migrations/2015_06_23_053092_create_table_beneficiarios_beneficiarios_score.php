<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableBeneficiariosBeneficiariosScore extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('beneficiarios.score', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('clave_beneficiario', 16);
			$table->smallInteger('score_riesgo')->nullable();			
			$table->index('clave_beneficiario');
			$table->unique('clave_beneficiario');
			$table->foreign('clave_beneficiario')->references('clave_beneficiario')->on('beneficiarios.beneficiarios')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('beneficiarios.score');
	}
}
