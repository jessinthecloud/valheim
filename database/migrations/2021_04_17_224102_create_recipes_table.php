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

        Schema::create('repair_stations', function (Blueprint $table) {
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
            $table->boolean('enabled')->default(true);
            $table->string('internalName')->nullable()->unique();
            $table->tinyInteger('amount');
            $table->tinyInteger('minStationLevel');

            $table->foreignId('crafting_station_id')->nullable()->constrained();
            $table->foreignId('repair_station_id')->nullable()->constrained();

            $table->timestamps();
        });

        Schema::create('shared_data', function (Blueprint $table) {
            $table->id();

            $table->string('interpolated_name')->unique();
            $table->string('name_EN')->nullable();
            $table->string('description')->nullable();
            $table->string('description_EN')->nullable();
            // enums
            $table->float('skillType')->nullable();
            $table->float('itemType')->nullable();
            $table->string('animationState')->nullable();

            $table->string('ammoType')->nullable();
            $table->float('armor')->nullable();
            $table->float('armorPerLevel')->nullable();
            $table->float('attackForce')->nullable();
            $table->float('backstabBonus')->nullable();
            $table->float('blockable')->nullable();
            $table->float('blockPower')->nullable();
            $table->float('blockPowerPerLevel')->nullable();
            $table->float('canBeReparied')->nullable();
            $table->float('deflectionForce')->nullable();
            $table->float('deflectionForcePerLevel')->nullable();
            $table->boolean('destroyBroken')->default(false);
            $table->string('dlc')->nullable();
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
            $table->boolean('helmetHideHair')->default(true);
            $table->string('holdAnimationState')->nullable();
            $table->float('holdDurationMin')->default(0);
            $table->float('holdStaminaDrain')->default(0);
            $table->float('maxDurability')->nullable();
            // max upgradeable level
            $table->tinyInteger('maxQuality')->nullable();
            // max number you can stack
            $table->tinyInteger('maxStackSize')->default(1);
            $table->float('movementModifier')->default(0);
            $table->boolean('questItem')->default(false);
            $table->boolean('teleportable')->default(true);
            $table->float('timedBlockBonus')->default(1.5);
            $table->tinyInteger('toolTier')->nullable();
            $table->boolean('useDurability')->nullable();
            $table->float('useDurabilityDrain')->default(1);
            $table->integer('value')->nullable();
            $table->integer('variants')->nullable();
            // weight of single item
            $table->float('weight')->default('1');
            // status effects -- not sure of type, is StatusEffect in C#
            $table->integer('attackStatusEffect')->nullable();
            $table->integer('consumeStatusEffect')->nullable();
            $table->integer('equipStatusEffect')->nullable();
            $table->integer('setStatusEffect')->nullable();


            $table->timestamps();
        });
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            // $table->string('name_EN')->nullable();
            $table->integer('quality')->nullable();
            $table->integer('variant')->nullable();
            $table->integer('durability')->nullable();
            $table->foreignId('shared_data_id')->nullable()->constrained('shared_data');
            $table->timestamps();
        });

        Schema::create('resources', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('amount')->default(1);
            $table->tinyInteger('amountPerLevel')->default(1);
            $table->boolean('recover')->default(true);
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
        Schema::dropIfExists('repair_stations');
    }
}
