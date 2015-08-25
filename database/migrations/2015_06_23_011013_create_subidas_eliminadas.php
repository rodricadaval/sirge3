<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSubidasEliminadas extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('sistema.subidas_eliminadas', function (Blueprint $table) {
			$table->integer('id_subida');
			$table->integer('id_usuario');
			$table->timestamp('fecha_eliminado')->default(DB::raw('now()::timestamp(0)'));

			$table->foreign('id_subida')->references('id_subida')->on('sistema.subidas');
			$table->foreign('id_usuario')->references('id_usuario')->on('sistema.usuarios');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::drop('sistema.subidas_eliminadas');
	}
}
