<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSistemaEntidades extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('sistema.entidades', function (Blueprint $table) {
			$table->char('id_entidad', 2)->primary();
			$table->integer('id_tipo_entidad');
			$table->integer('id_region');
			$table->string('descripcion',50);
			$table->foreign('id_tipo_entidad')->references('id_tipo_entidad')->on('sistema.tipo_entidad');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::drop('sistema.entidades');
	}
}
