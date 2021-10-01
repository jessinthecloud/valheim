<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePieceTablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('piece_tables', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->string('raw_name')->unique();
            $table->string('raw_slug')->unique();
            $table->string('true_name')->nullable(); // kind of secret name
            $table->string('true_slug')->nullable()->unique();
            $table->string('var_name')->nullable();
            $table->integer('num_pieces')->default(1);
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
        Schema::dropIfExists('piece_tables');
    }
}
