<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('url_path')->nullable();
            $table->string('raw_name');
            $table->string('true_name')->nullable(); // kind of secret name
            $table->string('var_name')->nullable();
            $table->foreignId('shared_data_id')->nullable()->constrained('shared_data');
            $table->timestamps();
            // GameObject
            //$table->foreignId('drop_prefab_id')->nullable()->references('id')->on('game_objects');
            // is probably instanced data?
            // $table->integer('stack')->default(1);
            // $table->integer('quality')->default(1);
            // $table->integer('variant')->nullable();
            // $table->float('durability')->default(100);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('items');
    }
}
