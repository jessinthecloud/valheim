<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePiecesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pieces', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->string('raw_name')->unique();
            $table->string('raw_slug')->unique();
            $table->string('true_name')->nullable(); // kind of secret name
            $table->string('true_slug')->nullable()->unique();
            $table->string('var_name')->nullable();
            $table->string('prefab_name')->nullable();
            $table->string('description')->nullable();
            $table->float('category')->nullable();
            $table->boolean('is_upgrade')->nullable();
            $table->integer('comfort')->nullable();
            $table->float('comfort_group')->nullable();
            $table->boolean('ground_piece')->nullable();
            $table->boolean('allow_alt_ground_placement')->nullable();
            $table->boolean('ground_only')->nullable();
            $table->boolean('cultivated_ground_only')->nullable();
            $table->boolean('water_piece')->nullable();
            $table->boolean("clip_ground")->nullable();
            $table->boolean("clip_everything")->nullable();
            $table->boolean("no_in_water")->nullable();
            $table->boolean("not_on_wood")->nullable();
            $table->boolean("not_on_tilting_surface")->nullable();
            $table->boolean("in_ceiling_only")->nullable();
            $table->boolean("not_on_floor")->nullable();
            $table->boolean("no_clipping")->nullable();
            $table->boolean("only_in_teleport_area")->nullable();
            $table->boolean("allowed_in_dungeons")->nullable();
            $table->float('space_requirement')->nullable();
            $table->boolean("repair_piece")->nullable();
            $table->boolean("can_be_removed")->nullable();
            $table->float('only_in_biome')->nullable();
            $table->string('dlc')->default("");

//            $table->boolean('enabled')->default(true);
//            $table->foreignId('piece_table_id')->nullable()->constrained();
//            $table->foreignId('crafting_station_id')->nullable()->constrained();

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
        Schema::dropIfExists('pieces');
    }
}
