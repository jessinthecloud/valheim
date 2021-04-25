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
        Schema::create('crafting_stations', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('interpolated_name')->nullable();
            $table->string('name_EN');
            $table->timestamps();
        });

        Schema::create('recipes', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('name_EN');
            $table->string('internalName')->nullable()->unique();
            $table->tinyInteger('amount');
            $table->tinyInteger('minStationLevel');

            $table->foreignId('crafting_station_id')->nullable()->constrained();
            // $table->foreignId('repairStation')->references('id')->on('repair_stations')->nullable();

            $table->timestamps();
        });

        Schema::create('shared_data', function (Blueprint $table) {
            $table->id();

            $table->string('name')->unique();
            $table->string('name_EN')->nullable();
            $table->string('description')->nullable();
            $table->string('description_EN')->nullable();
            // enums
            $table->float('skillType')->nullable();
            $table->float('itemType')->nullable();
            $table->tinyInteger('animation_state')->nullable();

            $table->string('ammoType')->nullable();
            $table->float('armor')->nullable();
            $table->float('armorPerLevel')->nullable();
            $table->float('attackForce')->nullable();
            $table->float('backstabBonus')->nullable();
            $table->float('blockable')->nullable();
            $table->float('blockPower')->nullable();
            $table->float('blockPowerPerLevel')->nullable();
            $table->float('canBeReparied')->nullable();
            $table->string('dlc')->nullable();
            $table->float('deflectionForce')->nullable();
            $table->float('deflectionForcePerLevel')->nullable();
            $table->float('dodgeable')->nullable();
            $table->float('durabilityDrain')->nullable();
            $table->float('durabilityPerLevel')->nullable();
            // how long it takes to equip item after selected
            $table->float('equipDuration')->nullable();
            // total HP granted when eaten
            $table->float('food')->nullable();
            // effects duration
            $table->float('foodBurnTime')->nullable();
            // HP per tick
            $table->float('foodRegen')->nullable();
            // total stamina granted when eaten
            $table->float('foodStamina')->nullable();
            $table->float('maxDurability')->nullable();
            // max upgradeable level
            $table->tinyInteger('maxQuality')->nullable();
            // max number you can stack
            $table->tinyInteger('maxStackSize')->nullable();
            $table->boolean('teleportable')->nullable();
            $table->boolean('questItem')->nullable();
            $table->integer('value')->nullable();
            $table->integer('variants')->nullable();
            // weight of single item
            $table->float('weight')->nullable();

            $table->timestamps();
        });
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            // $table->string('name_EN')->nullable();
            $table->foreignId('shared_data_id')->nullable()->constrained('shared_data');
            $table->timestamps();
        });

        Schema::create('resources', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('amount');
            $table->tinyInteger('amountPerLevel');
            $table->foreignId('item_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('recipe_id')->nullable()->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        /*Schema::create('recipe_resource', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recipe_id')->references('id')->on('recipes')->onDelete('cascade');
            $table->foreignId('resource_id')->references('id')->on('resources')->onDelete('cascade');
            $table->timestamps();
        });*/
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::dropIfExists('recipe_resource');
        Schema::dropIfExists('recipes');
        Schema::dropIfExists('item');
        Schema::dropIfExists('shared_data');
        Schema::dropIfExists('resource');
        Schema::dropIfExists('crafting_stations');
    }
}
