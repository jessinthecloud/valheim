<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecipesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recipes', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('name_EN');
            $table->string('internalName')->unique()->nullable();
            $table->tinyint('amount');
            $table->tinyint('minStationLevel');

            $table->foreignId('craftingStation')->references('id')->on('crafting_stations')->nullable();
            $table->foreignId('repairStation')->references('id')->on('repair_stations')->nullable();
            // pivot table
            $table->foreignId('recipe_resource_id')->references('id')->on('recipe_resource');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('recipes');
    }
}
