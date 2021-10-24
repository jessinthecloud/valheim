<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePieceRecipeRequirementTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('piece_recipe_requirement', function (Blueprint $table) {
            $table->id();
            $table->foreignId('piece_recipe_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('requirement_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('piece_recipe_requirement');
    }
}
