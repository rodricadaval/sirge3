<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEstados extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sistema.estados', function (Blueprint $table) {
            $table->increments('id_estado');
            $table->string('descripcion' , 100)->unique();
            $table->timestamps();
            $table->string('css' , 10)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('sistema.estados');
    }
}
