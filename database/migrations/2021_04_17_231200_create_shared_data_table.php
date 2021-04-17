<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSharedDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shared_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained();
            // recipe if craftable
            $table->float('recipe_id')->nullable();

            $table->string('name')->unique();
            $table->string('name_EN')->nullable();
            $table->string('description')->nullable();
            $table->string('description_EN')->nullable();
            // enums
            $table->float('skillType')->nullable();
            $table->float('itemType')->nullable();
            $table->tinyint('animation_state')->nullable();

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
            $table->tinyint('maxQuality')->nullable();
            // max number you can stack
            $table->tinyint('maxStackSize')->nullable();
            $table->boolean('teleportable')->nullable();
            $table->boolean('questItem')->nullable();
            $table->int('value')->nullable();
            $table->int('variants')->nullable();
            // weight of single item
            $table->float('weight')->nullable();



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
        Schema::dropIfExists('shared_data');
    }
}
