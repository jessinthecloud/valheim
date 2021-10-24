<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCraftingStationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('crafting_stations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('image')->nullable();
            $table->string('raw_name');
            $table->string('true_name')->nullable(); // kind of secret name
            $table->string('var_name')->nullable();
            $table->float('discover_range')->default(4);
            $table->float('range_build')->default(10);
            $table->boolean('craft_require_roof')->default(true);
            $table->boolean('craft_require_fire')->default(true);
            $table->boolean('have_fire')->nullable();
            $table->boolean('show_basic_recipes')->nullable();
            $table->float('use_distance')->default(2);
            $table->float('use_timer')->default(10);
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
        Schema::dropIfExists('crafting_stations');
    }
}
