<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePieceRecipesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('piece_recipes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('creation_id')->references('id')->on('items')->onDelete('cascade')->onUpdate('cascade');
            // amount of items the recipe creates
            $table->foreignId('piece_table_id')->nullable()->constrained();
            $table->foreignId('crafting_station_id')->nullable()->constrained();
            $table->boolean('enabled')->default(true);
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
        Schema::dropIfExists('piece_recipes');
    }
}
