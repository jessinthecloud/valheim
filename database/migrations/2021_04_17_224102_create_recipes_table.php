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
            $table->string('slug')->nullable();
            $table->string('interpolated_name')->nullable();
            $table->string('name_EN');
            // belong to Crafting station itself (data not in recipes json that we have)
            // $table->float('discoverRange')->default(4);
            // $table->float('rangeBuild')->default(10);
            // $table->boolean('craftRequireRoof')->default(true);
            // $table->boolean('craftRequireFire')->default(true);
            // $table->float('useTimer')->default(10);
            // $table->float('useDistance')->default(2);
            $table->timestamps();
        });

        // is this needed?
        Schema::create('repair_stations', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->nullable();
            $table->string('interpolated_name')->nullable();
            $table->string('name_EN');
            $table->timestamps();
        });

        Schema::create('recipes', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->nullable();
            $table->string('name_EN');
            $table->boolean('enabled')->default(true);
            $table->string('internalName')->nullable()->unique();
            $table->tinyInteger('amount')->default(1);
            $table->tinyInteger('minStationLevel')->default(1);

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
            $table->float('skillType')->default(1); // Skills.SkillType.Swords
            $table->float('itemType')->default(0x10);
            $table->string('animationState')->default('OneHanded');

            $table->string('ammoType')->default("");
            $table->float('armor')->default(10);
            $table->float('armorPerLevel')->default(1);
            $table->float('attackForce')->default(30);
            $table->float('backstabBonus')->default(4);
            $table->float('blockable')->nullable();
            $table->float('blockPower')->default(10);
            $table->float('blockPowerPerLevel')->nullable();
            $table->float('canBeReparied')->default(true);
            $table->float('deflectionForce')->nullable();
            $table->float('deflectionForcePerLevel')->nullable();
            $table->boolean('destroyBroken')->default(true);
            $table->string('dlc')->nullable();
            $table->float('dodgeable')->nullable();
            $table->float('durabilityDrain')->default(1);
            $table->float('durabilityPerLevel')->default(50);
            // how long it takes to equip item after selected
            $table->float('equipDuration')->default(1);
            // total HP granted when eaten
            $table->float('food')->nullable();
            // effects duration
            $table->float('foodBurnTime')->nullable();
            // HP per tick
            $table->float('foodRegen')->nullable();
            // total stamina granted when eaten
            $table->float('foodStamina')->nullable();
            $table->boolean('helmetHideHair')->default(true);
            $table->string('holdAnimationState')->default("");
            $table->float('holdDurationMin')->default(0);
            $table->float('holdStaminaDrain')->default(0);
            $table->float('maxDurability')->default(100);
            // max upgradeable level
            $table->tinyInteger('maxQuality')->default(1);
            // max number you can stack
            $table->tinyInteger('maxStackSize')->default(1);
            $table->float('movementModifier')->default(0);
            $table->boolean('questItem')->nullable();
            $table->boolean('teleportable')->default(true);
            $table->float('timedBlockBonus')->default(1.5);
            $table->tinyInteger('toolTier')->nullable();
            $table->boolean('useDurability')->nullable();
            $table->float('useDurabilityDrain')->default(1);
            $table->integer('value')->nullable();
            $table->integer('variants')->nullable();
            // weight of single item
            $table->float('weight')->default(1);
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
            $table->string('slug')->nullable();
            // $table->string('name_EN')->nullable();
            $table->integer('quality')->default(1);
            $table->integer('variant')->nullable();
            $table->float('durability')->default(100);
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
