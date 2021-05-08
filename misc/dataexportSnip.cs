SimpleJson.JsonArray jsonInfo = new SimpleJson.JsonArray();

foreach (GameObject obj in ObjectDB.instance.m_items)
{
    ItemDrop item = obj.GetComponent<ItemDrop>();
    ItemDrop.ItemData.SharedData shared = item.m_itemData.m_shared;


    SimpleJson.JsonObject jsonObj = new SimpleJson.JsonObject();
    
    SimpleJson.JsonObject jsonInfoObj = new SimpleJson.JsonObject();
    jsonInfoObj.Add("name", TestMod.Localize(shared.m_name));
    jsonInfoObj.Add("itemType", shared.m_itemType.ToString()); // ItemType
    jsonInfoObj.Add("description", TestMod.Localize(shared.m_description));
    jsonInfoObj.Add("aiAttackInterval", shared.m_aiAttackInterval);
    jsonInfoObj.Add("aiAttackMaxAngle", shared.m_aiAttackMaxAngle);
    jsonInfoObj.Add("aiAttackRange", shared.m_aiAttackRange);
    // jsonInfoObj.Add("aiAttackRangeMin", shared.m_aiAttackRangeMin); // null
    // jsonInfoObj.Add("aiPrioritized", shared.m_aiPrioritized); // null
    // jsonInfoObj.Add("aiTargetType", shared.m_aiTargetType); // AiTarget
    jsonInfoObj.Add("aiWhenFlying", shared.m_aiWhenFlying);
    jsonInfoObj.Add("aiWhenSwiming", shared.m_aiWhenSwiming);
    jsonInfoObj.Add("aiWhenWalking", shared.m_aiWhenWalking);
    jsonInfoObj.Add("animationState", shared.m_animationState); // AnimationState
    jsonInfoObj.Add("armor", shared.m_armor);
    jsonInfoObj.Add("attackForce", shared.m_attackForce);
    jsonInfoObj.Add("backstabBonus", shared.m_backstabBonus);
    jsonInfoObj.Add("blockable", shared.m_blockable);
    jsonInfoObj.Add("blockPower", shared.m_blockPower);
    jsonInfoObj.Add("blockPowerPerLevel", shared.m_blockPowerPerLevel);
    jsonInfoObj.Add("canBeReparied", shared.m_canBeReparied);
    jsonInfoObj.Add("deflectionForce", shared.m_deflectionForce);
    jsonInfoObj.Add("deflectionForcePerLevel", shared.m_deflectionForcePerLevel);
    jsonInfoObj.Add("destroyBroken", shared.m_destroyBroken);
    jsonInfoObj.Add("dodgeable", shared.m_dodgeable);
    jsonInfoObj.Add("durabilityDrain", shared.m_durabilityDrain);
    jsonInfoObj.Add("durabilityPerLevel", shared.m_durabilityPerLevel);
    jsonInfoObj.Add("equipDuration", shared.m_equipDuration);
    jsonInfoObj.Add("food", shared.m_food);
    jsonInfoObj.Add("foodBurnTime", shared.m_foodBurnTime);
    jsonInfoObj.Add("foodRegen", shared.m_foodRegen);
    jsonInfoObj.Add("foodStamina", shared.m_foodStamina);
    jsonInfoObj.Add("helmetHideHair", shared.m_helmetHideHair);
    jsonInfoObj.Add("holdDurationMin", shared.m_holdDurationMin);
    jsonInfoObj.Add("holdStaminaDrain", shared.m_holdStaminaDrain);
    jsonInfoObj.Add("maxDurability", shared.m_maxDurability);
    jsonInfoObj.Add("maxQuality", shared.m_maxQuality);
    jsonInfoObj.Add("maxStackSize", shared.m_maxStackSize);
    jsonInfoObj.Add("movementModifier", shared.m_movementModifier);
    jsonInfoObj.Add("questItem", shared.m_questItem);
    jsonInfoObj.Add("setSize", shared.m_setSize);
    jsonInfoObj.Add("teleportable", shared.m_teleportable);
    jsonInfoObj.Add("timedBlockBonus", shared.m_timedBlockBonus);
    jsonInfoObj.Add("toolTier", shared.m_toolTier);
    jsonInfoObj.Add("useDurability", shared.m_useDurability);
    jsonInfoObj.Add("useDurabilityDrain", shared.m_useDurabilityDrain);
    jsonInfoObj.Add("value", shared.m_value);
    jsonInfoObj.Add("variants", shared.m_variants);
    jsonInfoObj.Add("weight", shared.m_weight);
    jsonInfoObj.Add("ammoType", shared.m_ammoType);
    //jsonInfoObj.Add("damages", shared.m_damages);
    //jsonInfoObj.Add("damagesPerLevel", shared.m_damagesPerLevel); // HitData.DamageTypes
    //jsonInfoObj.Add("damageModifiers", shared.m_damageModifiers); // List<HitData.DamageModPair>
    // jsonInfoObj.Add("skillType", shared.m_skillType); // Skills.SkillType
    // jsonInfoObj.Add("armorMaterial", shared.m_armorMaterial); // Material
    // jsonInfoObj.Add("attack", shared.m_attack); // Attack
    // jsonInfoObj.Add("secondaryAttack", shared.m_secondaryAttack); // Attack
    if (shared.m_attackStatusEffect)
    {
        jsonInfoObj.Add("attackStatusEffect", shared.m_attackStatusEffect.name);
    }
    if (shared.m_consumeStatusEffect)
    {
        jsonInfoObj.Add("consumeStatusEffect", shared.m_consumeStatusEffect.name);
    }
    if (shared.m_equipStatusEffect)
    {
        jsonInfoObj.Add("equipStatusEffect", shared.m_equipStatusEffect.name);
    }
    if (shared.m_setStatusEffect)
    {
        jsonInfoObj.Add("setStatusEffect", shared.m_setStatusEffect.name);
    }
    jsonObj.Add("name", obj.name);
    jsonObj.Add("shared", jsonInfoObj);

    jsonInfo.Add(jsonObj);

    // String jsonString = SimpleJson.SimpleJson.SerializeObject(item);
    // File.WriteAllText("G:/Steam/steamapps/common/Valheim/BepInEx/plugins/TestMod/Docs/item.json", jsonString);
    //Jotunn.Logger.LogInfo("json count: "+jsonArr.Count());
    //Jotunn.Logger.LogInfo("json count: " + jsonObj.Count());

    //if (jsonArr.Count() <= 2)
    if (jsonObj.Count() == 1)
    {
        Jotunn.Logger.LogInfo("json string: " + jsonObj.ToString());
        
        //Jotunn.Logger.LogInfo("json string: " + SimpleJson.SimpleJson.SerializeObject(jsonArr));
    }


}
AddText(jsonInfo.ToString());
