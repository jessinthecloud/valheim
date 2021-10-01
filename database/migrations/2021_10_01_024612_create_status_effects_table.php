<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStatusEffectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('status_effects', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->string('raw_name')->unique();
            $table->string('raw_slug')->unique();
            $table->string('true_name')->nullable(); // kind of secret name
            $table->string('true_slug')->nullable()->unique();
            $table->string('var_name')->nullable();
            $table->string('category')->nullable();
            $table->string('tooltip')->nullable();
            $table->string('attributes')->nullable();
            $table->string('start_message')->nullable();
            $table->string('stop_message')->nullable();
            $table->string('repeat_message')->nullable();
            $table->float('repeat_interval')->default(0);
            $table->float('cooldown')->default(0);
            $table->string('activation_animation')->default("gpower");
            // $table->string('start_message_type')->default("TopLeft");
            // $table->string('stop_message_type')->default("TopLeft");
            // $table->string('repeat_message_type')->default("TopLeft");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('status_effects');
    }
}
