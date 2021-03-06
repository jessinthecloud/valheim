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
//            $table->string('slug')->nullable()->unique();
            // -- StatusEffects
            $table->foreignId('attack_status_effect_id')->nullable()->references('id')->on('status_effects');
            $table->foreignId('consume_status_effect_id')->nullable()->references('id')->on('status_effects');
            $table->foreignId('equip_status_effect_id')->nullable()->references('id')->on('status_effects');
            $table->foreignId('set_status_effect_id')->nullable()->references('id')->on('status_effects');
            // -- End StatusEffects -------
            $table->string('dlc')->nullable();
            $table->string('description')->nullable();
            // enum ItemDrop.ItemData.ItemType.Misc
            $table->float('item_type')->default(0x10);
            // max number you can stack
            $table->smallInteger('max_stack_size')->default(1);
            // max upgradeable level
            $table->tinyInteger('max_quality')->default(1);
            // weight of single item
            $table->float('weight')->default(1);
            $table->integer('value')->nullable();
            $table->boolean('teleportable')->default(true);
            $table->boolean('quest_item')->nullable();
            // how long it takes to equip item after selected
            $table->float('equip_duration')->default(1);
            $table->integer('variants')->nullable();
            $table->string('set_name')->default("");
            $table->integer('set_size')->nullable();
            $table->float('movement_modifier')->default(0);
            // total HP granted when eaten
            $table->float('food')->nullable();
            // total stamina granted when eaten
            $table->float('food_stamina')->nullable();
            // effects duration
            $table->float('food_burn_time')->nullable();
            // HP per tick
            $table->float('food_regen')->nullable();
            $table->boolean('helmet_hide_hair')->default(true);
            $table->float('armor')->default(10);
            $table->float('armor_per_level')->default(1);
            $table->float('block_power')->default(10);
            $table->float('block_power_per_level')->nullable();
            $table->float('deflection_force')->nullable();
            $table->float('deflection_force_per_level')->nullable();
            $table->float('timed_block_bonus')->default(1.5);
            // enum AnimationState.OneHanded
            $table->string('animation_state')->default('OneHanded');
            // enum Skills.SkillType.Swords
            $table->float('skill_type')->default(1);
            $table->tinyInteger('tool_tier')->nullable();
            $table->float('attack_force')->default(30);
            $table->float('backstab_bonus')->default(4);
            $table->float('dodgeable')->nullable();
            $table->float('blockable')->nullable();
            $table->boolean('use_durability')->nullable();
            $table->boolean('destroy_broken')->default(true);
            $table->float('can_be_repaired')->default(true); // canBeReparied
            $table->float('max_durability')->default(100);
            $table->float('durability_per_level')->default(50);
            $table->float('use_durability_drain')->default(1);
            $table->float('durability_drain')->default(1);
            $table->float('hold_duration_min')->default(0);
            $table->float('hold_stamina_drain')->default(0);
            $table->string('hold_animation_state')->default("");
            $table->string('ammo_type')->default("");
            $table->float('ai_attack_range')->default(2);
            $table->float('ai_attack_range_min')->default(2);
            $table->float('ai_attack_interval')->default(2);
            $table->float('ai_attack_max_angle')->default(5);
            $table->boolean('ai_when_flying')->default(true);
            $table->boolean('ai_when_walking')->default(true);
            $table->boolean('ai_when_swiming')->default(true);
            $table->timestamps();

            // HitData.DamageTypes
//            $table->string('damages')->nullable();
            // HitData.DamageTypes
//            $table->string('damages_per_level')->nullable();

            // $table->foreignId('damageModifiers')->nullable(); // List<HitData.DamageModPair>
            // $table->foreignId('build_pieces_id')->nullable()->references('id')->on('pieces'); // PieceTable
            // GameObject
            // $table->foreignId('spawn_on_hit_id')->nullable()->references('id')->on('game_objects');
            // GameObject
            // $table->foreignId('spawn_on_hit_terrain_id')->nullable()->references('id')->on('game_objects');

            // Attack
            // $table->foreignId('attack_id')->nullable()->references('id')->on('attacks');
            // Attack
            // $table->foreignId('secondary_attack_id')->nullable()->references('id')->on('attacks');

            // EffectList
            // $table->foreignId('hit_effect')->nullable()->references('id')->on('effect_lists');
            //
            // EffectList
            // $table->foreignId('hit_terrain_effect')->nullable()->references('id')->on('effect_lists');
            //
            // EffectList
            // $table->foreignId('block_effect')->nullable()->references('id')->on('effect_lists');
            // EffectList
            // $table->foreignId('start_effect')->nullable()->references('id')->on('effect_lists');
            //
            // EffectList
            // $table->foreignId('hold_start_effect')->nullable()->references('id')->on('effect_lists');
            //
            // EffectList
            // $table->foreignId('trigger_effect')->nullable()->references('id')->on('effect_lists');
            //
            // EffectList
            // $table->foreignId('trail_start_effect')->nullable()->references('id')->on('effect_lists');
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
