<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemRecipesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_recipes', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            // item being created
            // allow null for initialization during conversion
            $table->foreignId('creation_id')->nullable()->references('id')->on('items')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('crafting_station_id')->nullable()->constrained();
            $table->foreignId('repair_station_id')->nullable()->references('id')->on('crafting_stations');
            $table->boolean('enabled')->default(true);
            // amount of items the recipe creates
            $table->integer('amount')->default(1);
            $table->tinyInteger('min_station_level')->default(1);
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
        Schema::dropIfExists('item_recipes');
    }
}
