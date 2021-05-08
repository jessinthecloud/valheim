

// Attack
using System;
using System.Collections.Generic;
using UnityEngine;
using UnityEngine.Serialization;

[Serializable]
public class Attack
{
    public class HitPoint
    {
        public GameObject go;

        public Vector3 avgPoint = Vector3.get_zero();

        public int count;

        public Vector3 firstPoint;

        public Collider collider;

        public Vector3 closestPoint;

        public float closestDistance = 999999f;
    }

    public enum AttackType
    {
        Horizontal,
        Vertical,
        Projectile,
        None,
        Area,
        TriggerProjectile
    }

    public enum HitPointType
    {
        Closest,
        Average,
        First
    }

    [Header("Common")]
    public AttackType m_attackType;

    public string m_attackAnimation = "";

    public int m_attackRandomAnimations;

    public int m_attackChainLevels;

    public bool m_consumeItem;

    public bool m_hitTerrain = true;

    public float m_attackStamina = 20f;

    public float m_speedFactor = 0.2f;

    public float m_speedFactorRotation = 0.2f;

    public float m_attackStartNoise = 10f;

    public float m_attackHitNoise = 30f;

    public float m_damageMultiplier = 1f;

    public float m_forceMultiplier = 1f;

    public float m_staggerMultiplier = 1f;

    [Header("Misc")]
    public string m_attackOriginJoint = "";

    public float m_attackRange = 1.5f;

    public float m_attackHeight = 0.6f;

    public float m_attackOffset;

    public GameObject m_spawnOnTrigger;

    [Header("Melee/AOE")]
    public float m_attackAngle = 90f;

    public float m_attackRayWidth;

    public float m_maxYAngle;

    public bool m_lowerDamagePerHit = true;

    public HitPointType m_hitPointtype;

    public bool m_hitThroughWalls;

    public bool m_multiHit = true;

    public float m_lastChainDamageMultiplier = 2f;

    [BitMask(typeof(DestructibleType))]
    public DestructibleType m_resetChainIfHit;

    [Header("Melee special-skill")]
    public Skills.SkillType m_specialHitSkill;

    [BitMask(typeof(DestructibleType))]
    public DestructibleType m_specialHitType;

    [Header("Projectile")]
    public GameObject m_attackProjectile;

    public float m_projectileVel = 10f;

    public float m_projectileVelMin = 2f;

    public float m_projectileAccuracy = 10f;

    public float m_projectileAccuracyMin = 20f;

    public bool m_useCharacterFacing;

    public bool m_useCharacterFacingYAim;

    [FormerlySerializedAs("m_useCharacterFacingAngle")]
    public float m_launchAngle;

    public int m_projectiles = 1;

    public int m_projectileBursts = 1;

    public float m_burstInterval;

    public bool m_destroyPreviousProjectile;

    [Header("Attack-Effects")]
    public EffectList m_hitEffect = new EffectList();

    public EffectList m_hitTerrainEffect = new EffectList();

    public EffectList m_startEffect = new EffectList();

    public EffectList m_triggerEffect = new EffectList();

    public EffectList m_trailStartEffect = new EffectList();

    public static int m_attackMask;

    public static int m_attackMaskTerrain;

    public Humanoid m_character;

    public BaseAI m_baseAI;

    public Rigidbody m_body;

    public ZSyncAnimation m_zanim;

    public CharacterAnimEvent m_animEvent;

    [NonSerialized]
    public ItemDrop.ItemData m_weapon;

    public VisEquipment m_visEquipment;

    public float m_attackDrawPercentage;

    public const float m_freezeFrameDuration = 0.15f;

    public const float m_chainAttackMaxTime = 0.2f;

    public int m_nextAttackChainLevel;

    public int m_currentAttackCainLevel;

    public bool m_wasInAttack;

    public float m_time;

    public bool m_projectileAttackStarted;

    public float m_projectileFireTimer = -1f;

    public int m_projectileBurstsFired;

    [NonSerialized]
    public ItemDrop.ItemData m_ammoItem;

    public bool StartDraw(Humanoid character, ItemDrop.ItemData weapon)
    {
        if (!HaveAmmo(character, weapon))
        {
            return false;
        }
        EquipAmmoItem(character, weapon);
        return true;
    }

    public bool Start(Humanoid character, Rigidbody body, ZSyncAnimation zanim, CharacterAnimEvent animEvent, VisEquipment visEquipment, ItemDrop.ItemData weapon, Attack previousAttack, float timeSinceLastAttack, float attackDrawPercentage)
    {
        //IL_027f: Unknown result type (might be due to invalid IL or missing references)
        //IL_0295: Unknown result type (might be due to invalid IL or missing references)
        //IL_02b1: Unknown result type (might be due to invalid IL or missing references)
        //IL_02b6: Unknown result type (might be due to invalid IL or missing references)
        //IL_02cc: Unknown result type (might be due to invalid IL or missing references)
        //IL_02d1: Unknown result type (might be due to invalid IL or missing references)
        //IL_02e7: Unknown result type (might be due to invalid IL or missing references)
        if (m_attackAnimation == "")
        {
            return false;
        }
        m_character = character;
        m_baseAI = ((Component)m_character).GetComponent<BaseAI>();
        m_body = body;
        m_zanim = zanim;
        m_animEvent = animEvent;
        m_visEquipment = visEquipment;
        m_weapon = weapon;
        m_attackDrawPercentage = attackDrawPercentage;
        if (m_attackMask == 0)
        {
            m_attackMask = LayerMask.GetMask(new string[11]
            {
                "Default", "static_solid", "Default_small", "piece", "piece_nonsolid", "character", "character_net", "character_ghost", "hitbox", "character_noenv",
                "vehicle"
            });
            m_attackMaskTerrain = LayerMask.GetMask(new string[12]
            {
                "Default", "static_solid", "Default_small", "piece", "piece_nonsolid", "terrain", "character", "character_net", "character_ghost", "hitbox",
                "character_noenv", "vehicle"
            });
        }
        float staminaUsage = GetStaminaUsage();
        if (staminaUsage > 0f && !character.HaveStamina(staminaUsage + 0.1f))
        {
            if (character.IsPlayer())
            {
                Hud.instance.StaminaBarNoStaminaFlash();
            }
            return false;
        }
        if (!HaveAmmo(character, m_weapon))
        {
            return false;
        }
        EquipAmmoItem(character, m_weapon);
        if (m_attackChainLevels > 1)
        {
            if (previousAttack != null && previousAttack.m_attackAnimation == m_attackAnimation)
            {
                m_currentAttackCainLevel = previousAttack.m_nextAttackChainLevel;
            }
            if (m_currentAttackCainLevel >= m_attackChainLevels || timeSinceLastAttack > 0.2f)
            {
                m_currentAttackCainLevel = 0;
            }
            m_zanim.SetTrigger(m_attackAnimation + m_currentAttackCainLevel);
        }
        else if (m_attackRandomAnimations >= 2)
        {
            int num = Random.Range(0, m_attackRandomAnimations);
            m_zanim.SetTrigger(m_attackAnimation + num);
        }
        else
        {
            m_zanim.SetTrigger(m_attackAnimation);
        }
        if (character.IsPlayer() && m_attackType != AttackType.None && m_currentAttackCainLevel == 0)
        {
            if (ZInput.IsMouseActive() || m_attackType == AttackType.Projectile)
            {
                ((Component)character).get_transform().set_rotation(character.GetLookYaw());
                m_body.set_rotation(((Component)character).get_transform().get_rotation());
            }
            else if (ZInput.IsGamepadActive() && !character.IsBlocking())
            {
                Vector3 moveDir = character.GetMoveDir();
                if (((Vector3)(ref moveDir)).get_magnitude() > 0.3f)
                {
                    ((Component)character).get_transform().set_rotation(Quaternion.LookRotation(character.GetMoveDir()));
                    m_body.set_rotation(((Component)character).get_transform().get_rotation());
                }
            }
        }
        weapon.m_lastAttackTime = Time.get_time();
        m_animEvent.ResetChain();
        return true;
    }

    public float GetStaminaUsage()
    {
        if (m_attackStamina <= 0f)
        {
            return 0f;
        }
        float attackStamina = m_attackStamina;
        float skillFactor = m_character.GetSkillFactor(m_weapon.m_shared.m_skillType);
        return attackStamina - attackStamina * 0.33f * skillFactor;
    }

    public void Update(float dt)
    {
        //IL_0052: Unknown result type (might be due to invalid IL or missing references)
        //IL_0062: Unknown result type (might be due to invalid IL or missing references)
        //IL_007a: Unknown result type (might be due to invalid IL or missing references)
        //IL_008a: Unknown result type (might be due to invalid IL or missing references)
        m_time += dt;
        if (m_character.InAttack())
        {
            if (!m_wasInAttack)
            {
                m_character.UseStamina(GetStaminaUsage());
                Transform attackOrigin = GetAttackOrigin();
                m_weapon.m_shared.m_startEffect.Create(attackOrigin.get_position(), ((Component)m_character).get_transform().get_rotation(), attackOrigin);
                m_startEffect.Create(attackOrigin.get_position(), ((Component)m_character).get_transform().get_rotation(), attackOrigin);
                m_character.AddNoise(m_attackStartNoise);
                m_nextAttackChainLevel = m_currentAttackCainLevel + 1;
                if (m_nextAttackChainLevel >= m_attackChainLevels)
                {
                    m_nextAttackChainLevel = 0;
                }
            }
            m_wasInAttack = true;
        }
        else if (m_wasInAttack)
        {
            OnAttackDone();
            m_wasInAttack = false;
        }
        UpdateProjectile(dt);
    }

    public void OnAttackDone()
    {
        if (Object.op_Implicit((Object)(object)m_visEquipment))
        {
            m_visEquipment.SetWeaponTrails(enabled: false);
        }
    }

    public void Stop()
    {
        if (m_wasInAttack)
        {
            OnAttackDone();
            m_wasInAttack = false;
        }
    }

    public void OnAttackTrigger()
    {
        if (UseAmmo())
        {
            switch (m_attackType)
            {
            case AttackType.Horizontal:
            case AttackType.Vertical:
                DoMeleeAttack();
                break;
            case AttackType.Area:
                DoAreaAttack();
                break;
            case AttackType.Projectile:
                ProjectileAttackTriggered();
                break;
            case AttackType.None:
                DoNonAttack();
                break;
            }
            if (m_consumeItem)
            {
                ConsumeItem();
            }
        }
    }

    public void ConsumeItem()
    {
        if (m_weapon.m_shared.m_maxStackSize > 1 && m_weapon.m_stack > 1)
        {
            m_weapon.m_stack--;
            return;
        }
        m_character.UnequipItem(m_weapon, triggerEquipEffects: false);
        m_character.GetInventory().RemoveItem(m_weapon);
    }

    public static bool EquipAmmoItem(Humanoid character, ItemDrop.ItemData weapon)
    {
        if (!string.IsNullOrEmpty(weapon.m_shared.m_ammoType))
        {
            ItemDrop.ItemData ammoItem = character.GetAmmoItem();
            if (ammoItem != null && character.GetInventory().ContainsItem(ammoItem) && ammoItem.m_shared.m_ammoType == weapon.m_shared.m_ammoType)
            {
                return true;
            }
            ItemDrop.ItemData ammoItem2 = character.GetInventory().GetAmmoItem(weapon.m_shared.m_ammoType);
            if (ammoItem2.m_shared.m_itemType == ItemDrop.ItemData.ItemType.Ammo)
            {
                return character.EquipItem(ammoItem2);
            }
        }
        return true;
    }

    public static bool HaveAmmo(Humanoid character, ItemDrop.ItemData weapon)
    {
        if (!string.IsNullOrEmpty(weapon.m_shared.m_ammoType))
        {
            ItemDrop.ItemData itemData = character.GetAmmoItem();
            if (itemData != null && (!character.GetInventory().ContainsItem(itemData) || itemData.m_shared.m_ammoType != weapon.m_shared.m_ammoType))
            {
                itemData = null;
            }
            if (itemData == null)
            {
                itemData = character.GetInventory().GetAmmoItem(weapon.m_shared.m_ammoType);
            }
            if (itemData == null)
            {
                character.Message(MessageHud.MessageType.Center, "$msg_outof " + weapon.m_shared.m_ammoType);
                return false;
            }
            if (itemData.m_shared.m_itemType == ItemDrop.ItemData.ItemType.Consumable)
            {
                return character.CanConsumeItem(itemData);
            }
            return true;
        }
        return true;
    }

    public bool UseAmmo()
    {
        m_ammoItem = null;
        ItemDrop.ItemData itemData = null;
        if (!string.IsNullOrEmpty(m_weapon.m_shared.m_ammoType))
        {
            itemData = m_character.GetAmmoItem();
            if (itemData != null && (!m_character.GetInventory().ContainsItem(itemData) || itemData.m_shared.m_ammoType != m_weapon.m_shared.m_ammoType))
            {
                itemData = null;
            }
            if (itemData == null)
            {
                itemData = m_character.GetInventory().GetAmmoItem(m_weapon.m_shared.m_ammoType);
            }
            if (itemData == null)
            {
                m_character.Message(MessageHud.MessageType.Center, "$msg_outof " + m_weapon.m_shared.m_ammoType);
                return false;
            }
            if (itemData.m_shared.m_itemType == ItemDrop.ItemData.ItemType.Consumable)
            {
                bool num = m_character.ConsumeItem(m_character.GetInventory(), itemData);
                if (num)
                {
                    m_ammoItem = itemData;
                }
                return num;
            }
            m_character.GetInventory().RemoveItem(itemData, 1);
            m_ammoItem = itemData;
            return true;
        }
        return true;
    }

    public void ProjectileAttackTriggered()
    {
        //IL_001a: Unknown result type (might be due to invalid IL or missing references)
        //IL_001b: Unknown result type (might be due to invalid IL or missing references)
        //IL_001c: Unknown result type (might be due to invalid IL or missing references)
        //IL_0033: Unknown result type (might be due to invalid IL or missing references)
        //IL_0034: Unknown result type (might be due to invalid IL or missing references)
        //IL_0035: Unknown result type (might be due to invalid IL or missing references)
        GetProjectileSpawnPoint(out var spawnPoint, out var aimDir);
        m_weapon.m_shared.m_triggerEffect.Create(spawnPoint, Quaternion.LookRotation(aimDir));
        m_triggerEffect.Create(spawnPoint, Quaternion.LookRotation(aimDir));
        if (m_weapon.m_shared.m_useDurability && m_character.IsPlayer())
        {
            m_weapon.m_durability -= m_weapon.m_shared.m_useDurabilityDrain;
        }
        if (m_projectileBursts == 1)
        {
            FireProjectileBurst();
        }
        else
        {
            m_projectileAttackStarted = true;
        }
    }

    public void UpdateProjectile(float dt)
    {
        if (m_projectileAttackStarted && m_projectileBurstsFired < m_projectileBursts)
        {
            m_projectileFireTimer -= dt;
            if (m_projectileFireTimer <= 0f)
            {
                m_projectileFireTimer = m_burstInterval;
                FireProjectileBurst();
                m_projectileBurstsFired++;
            }
        }
    }

    public Transform GetAttackOrigin()
    {
        if (m_attackOriginJoint.Length > 0)
        {
            return Utils.FindChild(m_character.GetVisual().get_transform(), m_attackOriginJoint);
        }
        return ((Component)m_character).get_transform();
    }

    public void GetProjectileSpawnPoint(out Vector3 spawnPoint, out Vector3 aimDir)
    {
        //IL_0015: Unknown result type (might be due to invalid IL or missing references)
        //IL_001b: Unknown result type (might be due to invalid IL or missing references)
        //IL_0026: Unknown result type (might be due to invalid IL or missing references)
        //IL_002b: Unknown result type (might be due to invalid IL or missing references)
        //IL_0031: Unknown result type (might be due to invalid IL or missing references)
        //IL_003c: Unknown result type (might be due to invalid IL or missing references)
        //IL_0041: Unknown result type (might be due to invalid IL or missing references)
        //IL_0047: Unknown result type (might be due to invalid IL or missing references)
        //IL_0052: Unknown result type (might be due to invalid IL or missing references)
        //IL_0057: Unknown result type (might be due to invalid IL or missing references)
        //IL_005c: Unknown result type (might be due to invalid IL or missing references)
        //IL_0069: Unknown result type (might be due to invalid IL or missing references)
        //IL_006e: Unknown result type (might be due to invalid IL or missing references)
        //IL_0073: Unknown result type (might be due to invalid IL or missing references)
        //IL_009a: Unknown result type (might be due to invalid IL or missing references)
        //IL_00a0: Unknown result type (might be due to invalid IL or missing references)
        //IL_00a5: Unknown result type (might be due to invalid IL or missing references)
        //IL_00aa: Unknown result type (might be due to invalid IL or missing references)
        //IL_00ae: Unknown result type (might be due to invalid IL or missing references)
        //IL_00b3: Unknown result type (might be due to invalid IL or missing references)
        //IL_00c0: Unknown result type (might be due to invalid IL or missing references)
        //IL_00c5: Unknown result type (might be due to invalid IL or missing references)
        //IL_00d0: Unknown result type (might be due to invalid IL or missing references)
        //IL_00d5: Unknown result type (might be due to invalid IL or missing references)
        Transform attackOrigin = GetAttackOrigin();
        Transform transform = ((Component)m_character).get_transform();
        spawnPoint = attackOrigin.get_position() + transform.get_up() * m_attackHeight + transform.get_forward() * m_attackRange + transform.get_right() * m_attackOffset;
        aimDir = m_character.GetAimDir(spawnPoint);
        if (Object.op_Implicit((Object)(object)m_baseAI))
        {
            Character targetCreature = m_baseAI.GetTargetCreature();
            if (Object.op_Implicit((Object)(object)targetCreature))
            {
                Vector3 val = targetCreature.GetCenterPoint() - spawnPoint;
                Vector3 normalized = ((Vector3)(ref val)).get_normalized();
                aimDir = Vector3.RotateTowards(((Component)m_character).get_transform().get_forward(), normalized, (float)Math.PI / 2f, 1f);
            }
        }
    }

    public void FireProjectileBurst()
    {
        //IL_014e: Unknown result type (might be due to invalid IL or missing references)
        //IL_0153: Unknown result type (might be due to invalid IL or missing references)
        //IL_015f: Unknown result type (might be due to invalid IL or missing references)
        //IL_016d: Unknown result type (might be due to invalid IL or missing references)
        //IL_016f: Unknown result type (might be due to invalid IL or missing references)
        //IL_0174: Unknown result type (might be due to invalid IL or missing references)
        //IL_0183: Unknown result type (might be due to invalid IL or missing references)
        //IL_0188: Unknown result type (might be due to invalid IL or missing references)
        //IL_018a: Unknown result type (might be due to invalid IL or missing references)
        //IL_018f: Unknown result type (might be due to invalid IL or missing references)
        //IL_0197: Unknown result type (might be due to invalid IL or missing references)
        //IL_0199: Unknown result type (might be due to invalid IL or missing references)
        //IL_019e: Unknown result type (might be due to invalid IL or missing references)
        //IL_01a0: Unknown result type (might be due to invalid IL or missing references)
        //IL_01a5: Unknown result type (might be due to invalid IL or missing references)
        //IL_01ea: Unknown result type (might be due to invalid IL or missing references)
        //IL_01ec: Unknown result type (might be due to invalid IL or missing references)
        //IL_01ee: Unknown result type (might be due to invalid IL or missing references)
        //IL_01f0: Unknown result type (might be due to invalid IL or missing references)
        //IL_01f5: Unknown result type (might be due to invalid IL or missing references)
        //IL_01fa: Unknown result type (might be due to invalid IL or missing references)
        //IL_0206: Unknown result type (might be due to invalid IL or missing references)
        //IL_020b: Unknown result type (might be due to invalid IL or missing references)
        //IL_021a: Unknown result type (might be due to invalid IL or missing references)
        //IL_021c: Unknown result type (might be due to invalid IL or missing references)
        //IL_0221: Unknown result type (might be due to invalid IL or missing references)
        //IL_0223: Unknown result type (might be due to invalid IL or missing references)
        //IL_0228: Unknown result type (might be due to invalid IL or missing references)
        //IL_022a: Unknown result type (might be due to invalid IL or missing references)
        //IL_022c: Unknown result type (might be due to invalid IL or missing references)
        //IL_0231: Unknown result type (might be due to invalid IL or missing references)
        //IL_0234: Unknown result type (might be due to invalid IL or missing references)
        //IL_0236: Unknown result type (might be due to invalid IL or missing references)
        //IL_0238: Unknown result type (might be due to invalid IL or missing references)
        //IL_0447: Unknown result type (might be due to invalid IL or missing references)
        //IL_044a: Unknown result type (might be due to invalid IL or missing references)
        ItemDrop.ItemData ammoItem = m_ammoItem;
        GameObject attackProjectile = m_attackProjectile;
        float num = m_projectileVel;
        float num2 = m_projectileVelMin;
        float num3 = m_projectileAccuracy;
        float num4 = m_projectileAccuracyMin;
        float num5 = m_attackHitNoise;
        if (ammoItem != null && Object.op_Implicit((Object)(object)ammoItem.m_shared.m_attack.m_attackProjectile))
        {
            attackProjectile = ammoItem.m_shared.m_attack.m_attackProjectile;
            num += ammoItem.m_shared.m_attack.m_projectileVel;
            num2 += ammoItem.m_shared.m_attack.m_projectileVelMin;
            num3 += ammoItem.m_shared.m_attack.m_projectileAccuracy;
            num4 += ammoItem.m_shared.m_attack.m_projectileAccuracyMin;
            num5 += ammoItem.m_shared.m_attack.m_attackHitNoise;
        }
        float num6 = m_character.GetRandomSkillFactor(m_weapon.m_shared.m_skillType);
        if (m_weapon.m_shared.m_holdDurationMin > 0f)
        {
            num3 = Mathf.Lerp(num4, num3, Mathf.Pow(m_attackDrawPercentage, 0.5f));
            num6 *= m_attackDrawPercentage;
            num = Mathf.Lerp(num2, num, m_attackDrawPercentage);
        }
        GetProjectileSpawnPoint(out var spawnPoint, out var aimDir);
        Transform transform = ((Component)m_character).get_transform();
        if (m_useCharacterFacing)
        {
            Vector3 forward = Vector3.get_forward();
            if (m_useCharacterFacingYAim)
            {
                forward.y = aimDir.y;
            }
            aimDir = transform.TransformDirection(forward);
        }
        if (m_launchAngle != 0f)
        {
            Vector3 val = Vector3.Cross(Vector3.get_up(), aimDir);
            aimDir = Quaternion.AngleAxis(m_launchAngle, val) * aimDir;
        }
        for (int i = 0; i < m_projectiles; i++)
        {
            if (m_destroyPreviousProjectile && Object.op_Implicit((Object)(object)m_weapon.m_lastProjectile))
            {
                ZNetScene.instance.Destroy(m_weapon.m_lastProjectile);
                m_weapon.m_lastProjectile = null;
            }
            Vector3 val2 = aimDir;
            Vector3 val3 = Vector3.Cross(val2, Vector3.get_up());
            Quaternion val4 = Quaternion.AngleAxis(Random.Range(0f - num3, num3), Vector3.get_up());
            val2 = Quaternion.AngleAxis(Random.Range(0f - num3, num3), val3) * val2;
            val2 = val4 * val2;
            GameObject val5 = Object.Instantiate<GameObject>(attackProjectile, spawnPoint, Quaternion.LookRotation(val2));
            HitData hitData = new HitData();
            hitData.m_toolTier = m_weapon.m_shared.m_toolTier;
            hitData.m_pushForce = m_weapon.m_shared.m_attackForce * m_forceMultiplier;
            hitData.m_backstabBonus = m_weapon.m_shared.m_backstabBonus;
            hitData.m_staggerMultiplier = m_staggerMultiplier;
            hitData.m_damage.Add(m_weapon.GetDamage());
            hitData.m_statusEffect = (Object.op_Implicit((Object)(object)m_weapon.m_shared.m_attackStatusEffect) ? ((Object)m_weapon.m_shared.m_attackStatusEffect).get_name() : "");
            hitData.m_blockable = m_weapon.m_shared.m_blockable;
            hitData.m_dodgeable = m_weapon.m_shared.m_dodgeable;
            hitData.m_skill = m_weapon.m_shared.m_skillType;
            hitData.SetAttacker(m_character);
            if (ammoItem != null)
            {
                hitData.m_damage.Add(ammoItem.GetDamage());
                hitData.m_pushForce += ammoItem.m_shared.m_attackForce;
                if ((Object)(object)ammoItem.m_shared.m_attackStatusEffect != (Object)null)
                {
                    hitData.m_statusEffect = ((Object)ammoItem.m_shared.m_attackStatusEffect).get_name();
                }
                if (!ammoItem.m_shared.m_blockable)
                {
                    hitData.m_blockable = false;
                }
                if (!ammoItem.m_shared.m_dodgeable)
                {
                    hitData.m_dodgeable = false;
                }
            }
            hitData.m_pushForce *= num6;
            hitData.m_damage.Modify(m_damageMultiplier);
            hitData.m_damage.Modify(num6);
            hitData.m_damage.Modify(GetLevelDamageFactor());
            m_character.GetSEMan().ModifyAttack(m_weapon.m_shared.m_skillType, ref hitData);
            val5.GetComponent<IProjectile>()?.Setup(m_character, val2 * num, num5, hitData, m_weapon);
            m_weapon.m_lastProjectile = val5;
        }
    }

    public void DoNonAttack()
    {
        //IL_0059: Unknown result type (might be due to invalid IL or missing references)
        //IL_0069: Unknown result type (might be due to invalid IL or missing references)
        //IL_0081: Unknown result type (might be due to invalid IL or missing references)
        //IL_0091: Unknown result type (might be due to invalid IL or missing references)
        if (m_weapon.m_shared.m_useDurability && m_character.IsPlayer())
        {
            m_weapon.m_durability -= m_weapon.m_shared.m_useDurabilityDrain;
        }
        Transform attackOrigin = GetAttackOrigin();
        m_weapon.m_shared.m_triggerEffect.Create(attackOrigin.get_position(), ((Component)m_character).get_transform().get_rotation(), attackOrigin);
        m_triggerEffect.Create(attackOrigin.get_position(), ((Component)m_character).get_transform().get_rotation(), attackOrigin);
        if (Object.op_Implicit((Object)(object)m_weapon.m_shared.m_consumeStatusEffect))
        {
            m_character.GetSEMan().AddStatusEffect(m_weapon.m_shared.m_consumeStatusEffect, resetTime: true);
        }
        m_character.AddNoise(m_attackHitNoise);
    }

    public float GetLevelDamageFactor()
    {
        return 1f + (float)Mathf.Max(0, m_character.GetLevel() - 1) * 0.5f;
    }

    public void DoAreaAttack()
    {
        //IL_0014: Unknown result type (might be due to invalid IL or missing references)
        //IL_0019: Unknown result type (might be due to invalid IL or missing references)
        //IL_0024: Unknown result type (might be due to invalid IL or missing references)
        //IL_0029: Unknown result type (might be due to invalid IL or missing references)
        //IL_002f: Unknown result type (might be due to invalid IL or missing references)
        //IL_003a: Unknown result type (might be due to invalid IL or missing references)
        //IL_003f: Unknown result type (might be due to invalid IL or missing references)
        //IL_0045: Unknown result type (might be due to invalid IL or missing references)
        //IL_0050: Unknown result type (might be due to invalid IL or missing references)
        //IL_0055: Unknown result type (might be due to invalid IL or missing references)
        //IL_005a: Unknown result type (might be due to invalid IL or missing references)
        //IL_006b: Unknown result type (might be due to invalid IL or missing references)
        //IL_006d: Unknown result type (might be due to invalid IL or missing references)
        //IL_0084: Unknown result type (might be due to invalid IL or missing references)
        //IL_0086: Unknown result type (might be due to invalid IL or missing references)
        //IL_0097: Unknown result type (might be due to invalid IL or missing references)
        //IL_0099: Unknown result type (might be due to invalid IL or missing references)
        //IL_009e: Unknown result type (might be due to invalid IL or missing references)
        //IL_00a3: Unknown result type (might be due to invalid IL or missing references)
        //IL_00ba: Unknown result type (might be due to invalid IL or missing references)
        //IL_00bf: Unknown result type (might be due to invalid IL or missing references)
        //IL_00fa: Unknown result type (might be due to invalid IL or missing references)
        //IL_0180: Unknown result type (might be due to invalid IL or missing references)
        //IL_0181: Unknown result type (might be due to invalid IL or missing references)
        //IL_0186: Unknown result type (might be due to invalid IL or missing references)
        //IL_018c: Unknown result type (might be due to invalid IL or missing references)
        //IL_018d: Unknown result type (might be due to invalid IL or missing references)
        //IL_0192: Unknown result type (might be due to invalid IL or missing references)
        //IL_01a4: Unknown result type (might be due to invalid IL or missing references)
        //IL_01a6: Unknown result type (might be due to invalid IL or missing references)
        //IL_01a7: Unknown result type (might be due to invalid IL or missing references)
        //IL_01ac: Unknown result type (might be due to invalid IL or missing references)
        //IL_01ba: Unknown result type (might be due to invalid IL or missing references)
        //IL_01bb: Unknown result type (might be due to invalid IL or missing references)
        //IL_01cd: Unknown result type (might be due to invalid IL or missing references)
        //IL_01cf: Unknown result type (might be due to invalid IL or missing references)
        //IL_01d3: Unknown result type (might be due to invalid IL or missing references)
        //IL_01d8: Unknown result type (might be due to invalid IL or missing references)
        //IL_01dd: Unknown result type (might be due to invalid IL or missing references)
        //IL_02e2: Unknown result type (might be due to invalid IL or missing references)
        //IL_02e4: Unknown result type (might be due to invalid IL or missing references)
        //IL_02eb: Unknown result type (might be due to invalid IL or missing references)
        //IL_02ed: Unknown result type (might be due to invalid IL or missing references)
        //IL_03fc: Unknown result type (might be due to invalid IL or missing references)
        //IL_03fe: Unknown result type (might be due to invalid IL or missing references)
        //IL_0400: Unknown result type (might be due to invalid IL or missing references)
        //IL_0405: Unknown result type (might be due to invalid IL or missing references)
        //IL_0420: Unknown result type (might be due to invalid IL or missing references)
        //IL_0425: Unknown result type (might be due to invalid IL or missing references)
        //IL_042a: Unknown result type (might be due to invalid IL or missing references)
        //IL_043c: Unknown result type (might be due to invalid IL or missing references)
        //IL_043e: Unknown result type (might be due to invalid IL or missing references)
        //IL_0455: Unknown result type (might be due to invalid IL or missing references)
        //IL_0457: Unknown result type (might be due to invalid IL or missing references)
        //IL_04f1: Unknown result type (might be due to invalid IL or missing references)
        //IL_04f2: Unknown result type (might be due to invalid IL or missing references)
        //IL_051a: Unknown result type (might be due to invalid IL or missing references)
        Transform transform = ((Component)m_character).get_transform();
        Transform attackOrigin = GetAttackOrigin();
        Vector3 val = attackOrigin.get_position() + Vector3.get_up() * m_attackHeight + transform.get_forward() * m_attackRange + transform.get_right() * m_attackOffset;
        m_weapon.m_shared.m_triggerEffect.Create(val, transform.get_rotation(), attackOrigin);
        m_triggerEffect.Create(val, transform.get_rotation(), attackOrigin);
        Vector3 val2 = val - transform.get_position();
        val2.y = 0f;
        ((Vector3)(ref val2)).Normalize();
        int num = 0;
        Vector3 val3 = Vector3.get_zero();
        bool flag = false;
        bool flag2 = false;
        float randomSkillFactor = m_character.GetRandomSkillFactor(m_weapon.m_shared.m_skillType);
        int num2 = (m_hitTerrain ? m_attackMaskTerrain : m_attackMask);
        Collider[] array = Physics.OverlapSphere(val, m_attackRayWidth, num2, (QueryTriggerInteraction)0);
        HashSet<GameObject> hashSet = new HashSet<GameObject>();
        Collider[] array2 = array;
        foreach (Collider val4 in array2)
        {
            if ((Object)(object)((Component)val4).get_gameObject() == (Object)(object)((Component)m_character).get_gameObject())
            {
                continue;
            }
            GameObject val5 = Projectile.FindHitObject(val4);
            if ((Object)(object)val5 == (Object)(object)((Component)m_character).get_gameObject() || hashSet.Contains(val5))
            {
                continue;
            }
            hashSet.Add(val5);
            Vector3 val6 = ((!(val4 is MeshCollider)) ? val4.ClosestPoint(val) : val4.ClosestPointOnBounds(val));
            IDestructible component = val5.GetComponent<IDestructible>();
            if (component != null)
            {
                Vector3 val7 = val6 - val;
                val7.y = 0f;
                float num3 = Vector3.Dot(val2, val7);
                if (num3 < 0f)
                {
                    val7 += val2 * (0f - num3);
                }
                ((Vector3)(ref val7)).Normalize();
                HitData hitData = new HitData();
                hitData.m_toolTier = m_weapon.m_shared.m_toolTier;
                hitData.m_statusEffect = (Object.op_Implicit((Object)(object)m_weapon.m_shared.m_attackStatusEffect) ? ((Object)m_weapon.m_shared.m_attackStatusEffect).get_name() : "");
                hitData.m_pushForce = m_weapon.m_shared.m_attackForce * randomSkillFactor * m_forceMultiplier;
                hitData.m_backstabBonus = m_weapon.m_shared.m_backstabBonus;
                hitData.m_staggerMultiplier = m_staggerMultiplier;
                hitData.m_dodgeable = m_weapon.m_shared.m_dodgeable;
                hitData.m_blockable = m_weapon.m_shared.m_blockable;
                hitData.m_skill = m_weapon.m_shared.m_skillType;
                hitData.m_damage.Add(m_weapon.GetDamage());
                hitData.m_point = val6;
                hitData.m_dir = val7;
                hitData.m_hitCollider = val4;
                hitData.SetAttacker(m_character);
                hitData.m_damage.Modify(m_damageMultiplier);
                hitData.m_damage.Modify(randomSkillFactor);
                hitData.m_damage.Modify(GetLevelDamageFactor());
                if (m_attackChainLevels > 1 && m_currentAttackCainLevel == m_attackChainLevels - 1 && m_lastChainDamageMultiplier > 1f)
                {
                    hitData.m_damage.Modify(m_lastChainDamageMultiplier);
                    hitData.m_pushForce *= 1.2f;
                }
                m_character.GetSEMan().ModifyAttack(m_weapon.m_shared.m_skillType, ref hitData);
                Character character = component as Character;
                if (Object.op_Implicit((Object)(object)character))
                {
                    if ((!m_character.IsPlayer() && !BaseAI.IsEnemy(m_character, character)) || (hitData.m_dodgeable && character.IsDodgeInvincible()))
                    {
                        continue;
                    }
                    flag2 = true;
                }
                component.Damage(hitData);
                flag = true;
            }
            num++;
            val3 += val6;
        }
        if (num > 0)
        {
            val3 /= (float)num;
            m_weapon.m_shared.m_hitEffect.Create(val3, Quaternion.get_identity());
            m_hitEffect.Create(val3, Quaternion.get_identity());
            if (m_weapon.m_shared.m_useDurability && m_character.IsPlayer())
            {
                m_weapon.m_durability -= 1f;
            }
            m_character.AddNoise(m_attackHitNoise);
            if (flag)
            {
                m_character.RaiseSkill(m_weapon.m_shared.m_skillType, flag2 ? 1.5f : 1f);
            }
        }
        if (Object.op_Implicit((Object)(object)m_spawnOnTrigger))
        {
            Object.Instantiate<GameObject>(m_spawnOnTrigger, val, Quaternion.get_identity()).GetComponent<IProjectile>()?.Setup(m_character, ((Component)m_character).get_transform().get_forward(), -1f, null, null);
        }
    }

    public void GetMeleeAttackDir(out Transform originJoint, out Vector3 attackDir)
    {
        //IL_0013: Unknown result type (might be due to invalid IL or missing references)
        //IL_0018: Unknown result type (might be due to invalid IL or missing references)
        //IL_0021: Unknown result type (might be due to invalid IL or missing references)
        //IL_0026: Unknown result type (might be due to invalid IL or missing references)
        //IL_002b: Unknown result type (might be due to invalid IL or missing references)
        //IL_002e: Unknown result type (might be due to invalid IL or missing references)
        //IL_003b: Unknown result type (might be due to invalid IL or missing references)
        //IL_0059: Unknown result type (might be due to invalid IL or missing references)
        //IL_005e: Unknown result type (might be due to invalid IL or missing references)
        //IL_0070: Unknown result type (might be due to invalid IL or missing references)
        //IL_0075: Unknown result type (might be due to invalid IL or missing references)
        originJoint = GetAttackOrigin();
        Vector3 forward = ((Component)m_character).get_transform().get_forward();
        Vector3 aimDir = m_character.GetAimDir(originJoint.get_position());
        aimDir.x = forward.x;
        aimDir.z = forward.z;
        ((Vector3)(ref aimDir)).Normalize();
        attackDir = Vector3.RotateTowards(((Component)m_character).get_transform().get_forward(), aimDir, (float)Math.PI / 180f * m_maxYAngle, 10f);
    }

    public void AddHitPoint(List<HitPoint> list, GameObject go, Collider collider, Vector3 point, float distance)
    {
        //IL_004b: Unknown result type (might be due to invalid IL or missing references)
        //IL_004d: Unknown result type (might be due to invalid IL or missing references)
        //IL_005b: Unknown result type (might be due to invalid IL or missing references)
        //IL_0060: Unknown result type (might be due to invalid IL or missing references)
        //IL_0062: Unknown result type (might be due to invalid IL or missing references)
        //IL_0067: Unknown result type (might be due to invalid IL or missing references)
        //IL_0085: Unknown result type (might be due to invalid IL or missing references)
        //IL_0087: Unknown result type (might be due to invalid IL or missing references)
        HitPoint hitPoint = null;
        for (int num = list.Count - 1; num >= 0; num--)
        {
            if ((Object)(object)list[num].go == (Object)(object)go)
            {
                hitPoint = list[num];
                break;
            }
        }
        if (hitPoint == null)
        {
            hitPoint = new HitPoint();
            hitPoint.go = go;
            hitPoint.collider = collider;
            hitPoint.firstPoint = point;
            list.Add(hitPoint);
        }
        HitPoint hitPoint2 = hitPoint;
        hitPoint2.avgPoint += point;
        hitPoint.count++;
        if (distance < hitPoint.closestDistance)
        {
            hitPoint.closestPoint = point;
            hitPoint.closestDistance = distance;
        }
    }

    public void DoMeleeAttack()
    {
        //IL_0015: Unknown result type (might be due to invalid IL or missing references)
        //IL_0016: Unknown result type (might be due to invalid IL or missing references)
        //IL_001b: Unknown result type (might be due to invalid IL or missing references)
        //IL_001c: Unknown result type (might be due to invalid IL or missing references)
        //IL_001d: Unknown result type (might be due to invalid IL or missing references)
        //IL_0022: Unknown result type (might be due to invalid IL or missing references)
        //IL_0027: Unknown result type (might be due to invalid IL or missing references)
        //IL_0039: Unknown result type (might be due to invalid IL or missing references)
        //IL_003e: Unknown result type (might be due to invalid IL or missing references)
        //IL_0052: Unknown result type (might be due to invalid IL or missing references)
        //IL_0057: Unknown result type (might be due to invalid IL or missing references)
        //IL_0065: Unknown result type (might be due to invalid IL or missing references)
        //IL_006a: Unknown result type (might be due to invalid IL or missing references)
        //IL_0075: Unknown result type (might be due to invalid IL or missing references)
        //IL_007a: Unknown result type (might be due to invalid IL or missing references)
        //IL_008a: Unknown result type (might be due to invalid IL or missing references)
        //IL_0095: Unknown result type (might be due to invalid IL or missing references)
        //IL_009a: Unknown result type (might be due to invalid IL or missing references)
        //IL_009f: Unknown result type (might be due to invalid IL or missing references)
        //IL_00ec: Unknown result type (might be due to invalid IL or missing references)
        //IL_00f1: Unknown result type (might be due to invalid IL or missing references)
        //IL_0108: Unknown result type (might be due to invalid IL or missing references)
        //IL_010d: Unknown result type (might be due to invalid IL or missing references)
        //IL_0126: Unknown result type (might be due to invalid IL or missing references)
        //IL_012b: Unknown result type (might be due to invalid IL or missing references)
        //IL_0138: Unknown result type (might be due to invalid IL or missing references)
        //IL_013a: Unknown result type (might be due to invalid IL or missing references)
        //IL_013b: Unknown result type (might be due to invalid IL or missing references)
        //IL_0140: Unknown result type (might be due to invalid IL or missing references)
        //IL_0145: Unknown result type (might be due to invalid IL or missing references)
        //IL_0147: Unknown result type (might be due to invalid IL or missing references)
        //IL_0149: Unknown result type (might be due to invalid IL or missing references)
        //IL_014b: Unknown result type (might be due to invalid IL or missing references)
        //IL_014f: Unknown result type (might be due to invalid IL or missing references)
        //IL_0154: Unknown result type (might be due to invalid IL or missing references)
        //IL_016b: Unknown result type (might be due to invalid IL or missing references)
        //IL_0173: Unknown result type (might be due to invalid IL or missing references)
        //IL_0194: Unknown result type (might be due to invalid IL or missing references)
        //IL_0196: Unknown result type (might be due to invalid IL or missing references)
        //IL_01da: Unknown result type (might be due to invalid IL or missing references)
        //IL_01df: Unknown result type (might be due to invalid IL or missing references)
        //IL_0204: Unknown result type (might be due to invalid IL or missing references)
        //IL_0209: Unknown result type (might be due to invalid IL or missing references)
        //IL_0227: Unknown result type (might be due to invalid IL or missing references)
        //IL_0229: Unknown result type (might be due to invalid IL or missing references)
        //IL_022d: Unknown result type (might be due to invalid IL or missing references)
        //IL_0232: Unknown result type (might be due to invalid IL or missing references)
        //IL_0237: Unknown result type (might be due to invalid IL or missing references)
        //IL_0242: Unknown result type (might be due to invalid IL or missing references)
        //IL_0244: Unknown result type (might be due to invalid IL or missing references)
        //IL_0249: Unknown result type (might be due to invalid IL or missing references)
        //IL_0258: Unknown result type (might be due to invalid IL or missing references)
        //IL_025a: Unknown result type (might be due to invalid IL or missing references)
        //IL_025c: Unknown result type (might be due to invalid IL or missing references)
        //IL_0261: Unknown result type (might be due to invalid IL or missing references)
        //IL_030d: Unknown result type (might be due to invalid IL or missing references)
        //IL_0347: Unknown result type (might be due to invalid IL or missing references)
        //IL_034c: Unknown result type (might be due to invalid IL or missing references)
        //IL_0376: Unknown result type (might be due to invalid IL or missing references)
        //IL_0383: Unknown result type (might be due to invalid IL or missing references)
        //IL_0388: Unknown result type (might be due to invalid IL or missing references)
        //IL_038a: Unknown result type (might be due to invalid IL or missing references)
        //IL_038c: Unknown result type (might be due to invalid IL or missing references)
        //IL_03ab: Unknown result type (might be due to invalid IL or missing references)
        //IL_03ad: Unknown result type (might be due to invalid IL or missing references)
        //IL_03b3: Unknown result type (might be due to invalid IL or missing references)
        //IL_03b8: Unknown result type (might be due to invalid IL or missing references)
        //IL_03be: Unknown result type (might be due to invalid IL or missing references)
        //IL_03c3: Unknown result type (might be due to invalid IL or missing references)
        //IL_03cb: Unknown result type (might be due to invalid IL or missing references)
        //IL_03cd: Unknown result type (might be due to invalid IL or missing references)
        //IL_03cf: Unknown result type (might be due to invalid IL or missing references)
        //IL_03d4: Unknown result type (might be due to invalid IL or missing references)
        //IL_03e6: Unknown result type (might be due to invalid IL or missing references)
        //IL_03e8: Unknown result type (might be due to invalid IL or missing references)
        //IL_03ff: Unknown result type (might be due to invalid IL or missing references)
        //IL_0401: Unknown result type (might be due to invalid IL or missing references)
        //IL_0574: Unknown result type (might be due to invalid IL or missing references)
        //IL_0576: Unknown result type (might be due to invalid IL or missing references)
        //IL_057d: Unknown result type (might be due to invalid IL or missing references)
        //IL_057f: Unknown result type (might be due to invalid IL or missing references)
        //IL_0581: Unknown result type (might be due to invalid IL or missing references)
        //IL_0586: Unknown result type (might be due to invalid IL or missing references)
        //IL_058a: Unknown result type (might be due to invalid IL or missing references)
        //IL_058f: Unknown result type (might be due to invalid IL or missing references)
        //IL_0696: Unknown result type (might be due to invalid IL or missing references)
        //IL_0698: Unknown result type (might be due to invalid IL or missing references)
        //IL_06ab: Unknown result type (might be due to invalid IL or missing references)
        //IL_06ad: Unknown result type (might be due to invalid IL or missing references)
        //IL_06d2: Unknown result type (might be due to invalid IL or missing references)
        //IL_0717: Unknown result type (might be due to invalid IL or missing references)
        //IL_071c: Unknown result type (might be due to invalid IL or missing references)
        //IL_0721: Unknown result type (might be due to invalid IL or missing references)
        //IL_07ac: Unknown result type (might be due to invalid IL or missing references)
        //IL_07ae: Unknown result type (might be due to invalid IL or missing references)
        //IL_07c7: Unknown result type (might be due to invalid IL or missing references)
        //IL_083b: Unknown result type (might be due to invalid IL or missing references)
        //IL_083d: Unknown result type (might be due to invalid IL or missing references)
        //IL_0865: Unknown result type (might be due to invalid IL or missing references)
        GetMeleeAttackDir(out var originJoint, out var attackDir);
        Vector3 val = ((Component)m_character).get_transform().InverseTransformDirection(attackDir);
        Quaternion val2 = Quaternion.LookRotation(attackDir, Vector3.get_up());
        m_weapon.m_shared.m_triggerEffect.Create(originJoint.get_position(), val2, originJoint);
        m_triggerEffect.Create(originJoint.get_position(), val2, originJoint);
        Vector3 val3 = originJoint.get_position() + Vector3.get_up() * m_attackHeight + ((Component)m_character).get_transform().get_right() * m_attackOffset;
        float num = m_attackAngle / 2f;
        float num2 = 4f;
        float attackRange = m_attackRange;
        List<HitPoint> list = new List<HitPoint>();
        HashSet<Skills.SkillType> hashSet = new HashSet<Skills.SkillType>();
        int num3 = (m_hitTerrain ? m_attackMaskTerrain : m_attackMask);
        for (float num4 = 0f - num; num4 <= num; num4 += num2)
        {
            Quaternion val4 = Quaternion.get_identity();
            if (m_attackType == AttackType.Horizontal)
            {
                val4 = Quaternion.Euler(0f, 0f - num4, 0f);
            }
            else if (m_attackType == AttackType.Vertical)
            {
                val4 = Quaternion.Euler(num4, 0f, 0f);
            }
            Vector3 val5 = ((Component)m_character).get_transform().TransformDirection(val4 * val);
            Debug.DrawLine(val3, val3 + val5 * attackRange);
            RaycastHit[] array = ((!(m_attackRayWidth > 0f)) ? Physics.RaycastAll(val3, val5, attackRange, num3, (QueryTriggerInteraction)1) : Physics.SphereCastAll(val3, m_attackRayWidth, val5, Mathf.Max(0f, attackRange - m_attackRayWidth), num3, (QueryTriggerInteraction)1));
            Array.Sort(array, (RaycastHit x, RaycastHit y) => ((RaycastHit)(ref x)).get_distance().CompareTo(((RaycastHit)(ref y)).get_distance()));
            RaycastHit[] array2 = array;
            for (int i = 0; i < array2.Length; i++)
            {
                RaycastHit val6 = array2[i];
                if ((Object)(object)((Component)((RaycastHit)(ref val6)).get_collider()).get_gameObject() == (Object)(object)((Component)m_character).get_gameObject())
                {
                    continue;
                }
                Vector3 val7 = ((RaycastHit)(ref val6)).get_point();
                if (((RaycastHit)(ref val6)).get_distance() < float.Epsilon)
                {
                    val7 = ((!(((RaycastHit)(ref val6)).get_collider() is MeshCollider)) ? ((RaycastHit)(ref val6)).get_collider().ClosestPoint(val3) : (val3 + val5 * attackRange));
                }
                if (m_attackAngle < 180f && Vector3.Dot(val7 - val3, attackDir) <= 0f)
                {
                    continue;
                }
                GameObject val8 = Projectile.FindHitObject(((RaycastHit)(ref val6)).get_collider());
                if ((Object)(object)val8 == (Object)(object)((Component)m_character).get_gameObject())
                {
                    continue;
                }
                Vagon component = val8.GetComponent<Vagon>();
                if (Object.op_Implicit((Object)(object)component) && component.IsAttached(m_character))
                {
                    continue;
                }
                Character component2 = val8.GetComponent<Character>();
                if (!((Object)(object)component2 != (Object)null) || ((m_character.IsPlayer() || BaseAI.IsEnemy(m_character, component2)) && (!m_weapon.m_shared.m_dodgeable || !component2.IsDodgeInvincible())))
                {
                    AddHitPoint(list, val8, ((RaycastHit)(ref val6)).get_collider(), val7, ((RaycastHit)(ref val6)).get_distance());
                    if (!m_hitThroughWalls)
                    {
                        break;
                    }
                }
            }
        }
        int num5 = 0;
        Vector3 val9 = Vector3.get_zero();
        bool flag = false;
        bool flag2 = false;
        foreach (HitPoint item in list)
        {
            GameObject go = item.go;
            Vector3 val10 = item.avgPoint / (float)item.count;
            Vector3 val11 = val10;
            switch (m_hitPointtype)
            {
            case HitPointType.Average:
                val11 = val10;
                break;
            case HitPointType.First:
                val11 = item.firstPoint;
                break;
            case HitPointType.Closest:
                val11 = item.closestPoint;
                break;
            }
            num5++;
            val9 += val10;
            m_weapon.m_shared.m_hitEffect.Create(val11, Quaternion.get_identity());
            m_hitEffect.Create(val11, Quaternion.get_identity());
            IDestructible component3 = go.GetComponent<IDestructible>();
            if (component3 != null)
            {
                DestructibleType destructibleType = component3.GetDestructibleType();
                Skills.SkillType skillType = m_weapon.m_shared.m_skillType;
                if (m_specialHitSkill != 0 && (destructibleType & m_specialHitType) != 0)
                {
                    skillType = m_specialHitSkill;
                }
                float num6 = m_character.GetRandomSkillFactor(skillType);
                if (m_lowerDamagePerHit && list.Count > 1)
                {
                    num6 /= (float)list.Count * 0.75f;
                }
                HitData hitData = new HitData();
                hitData.m_toolTier = m_weapon.m_shared.m_toolTier;
                hitData.m_statusEffect = (Object.op_Implicit((Object)(object)m_weapon.m_shared.m_attackStatusEffect) ? ((Object)m_weapon.m_shared.m_attackStatusEffect).get_name() : "");
                hitData.m_pushForce = m_weapon.m_shared.m_attackForce * num6 * m_forceMultiplier;
                hitData.m_backstabBonus = m_weapon.m_shared.m_backstabBonus;
                hitData.m_staggerMultiplier = m_staggerMultiplier;
                hitData.m_dodgeable = m_weapon.m_shared.m_dodgeable;
                hitData.m_blockable = m_weapon.m_shared.m_blockable;
                hitData.m_skill = skillType;
                hitData.m_damage = m_weapon.GetDamage();
                hitData.m_point = val11;
                HitData hitData2 = hitData;
                Vector3 val12 = val11 - val3;
                hitData2.m_dir = ((Vector3)(ref val12)).get_normalized();
                hitData.m_hitCollider = item.collider;
                hitData.SetAttacker(m_character);
                hitData.m_damage.Modify(m_damageMultiplier);
                hitData.m_damage.Modify(num6);
                hitData.m_damage.Modify(GetLevelDamageFactor());
                if (m_attackChainLevels > 1 && m_currentAttackCainLevel == m_attackChainLevels - 1)
                {
                    hitData.m_damage.Modify(2f);
                    hitData.m_pushForce *= 1.2f;
                }
                m_character.GetSEMan().ModifyAttack(skillType, ref hitData);
                if (component3 is Character)
                {
                    flag2 = true;
                }
                component3.Damage(hitData);
                if ((destructibleType & m_resetChainIfHit) != 0)
                {
                    m_nextAttackChainLevel = 0;
                }
                hashSet.Add(skillType);
                if (!m_multiHit)
                {
                    break;
                }
            }
            if ((Object)(object)go.GetComponent<Heightmap>() != (Object)null && !flag)
            {
                flag = true;
                m_weapon.m_shared.m_hitTerrainEffect.Create(val10, val2);
                m_hitTerrainEffect.Create(val10, val2);
                if (Object.op_Implicit((Object)(object)m_weapon.m_shared.m_spawnOnHitTerrain))
                {
                    SpawnOnHitTerrain(val10, m_weapon.m_shared.m_spawnOnHitTerrain);
                }
                if (!m_multiHit)
                {
                    break;
                }
            }
        }
        if (num5 > 0)
        {
            val9 /= (float)num5;
            if (m_weapon.m_shared.m_useDurability && m_character.IsPlayer())
            {
                m_weapon.m_durability -= m_weapon.m_shared.m_useDurabilityDrain;
            }
            m_character.AddNoise(m_attackHitNoise);
            m_animEvent.FreezeFrame(0.15f);
            if (Object.op_Implicit((Object)(object)m_weapon.m_shared.m_spawnOnHit))
            {
                Object.Instantiate<GameObject>(m_weapon.m_shared.m_spawnOnHit, val9, val2).GetComponent<IProjectile>()?.Setup(m_character, Vector3.get_zero(), m_attackHitNoise, null, m_weapon);
            }
            foreach (Skills.SkillType item2 in hashSet)
            {
                m_character.RaiseSkill(item2, flag2 ? 1.5f : 1f);
            }
        }
        if (Object.op_Implicit((Object)(object)m_spawnOnTrigger))
        {
            Object.Instantiate<GameObject>(m_spawnOnTrigger, val3, Quaternion.get_identity()).GetComponent<IProjectile>()?.Setup(m_character, ((Component)m_character).get_transform().get_forward(), -1f, null, m_weapon);
        }
    }

    public void SpawnOnHitTerrain(Vector3 hitPoint, GameObject prefab)
    {
        //IL_000f: Unknown result type (might be due to invalid IL or missing references)
        //IL_0020: Unknown result type (might be due to invalid IL or missing references)
        //IL_0038: Unknown result type (might be due to invalid IL or missing references)
        //IL_0049: Unknown result type (might be due to invalid IL or missing references)
        //IL_0059: Unknown result type (might be due to invalid IL or missing references)
        //IL_0065: Unknown result type (might be due to invalid IL or missing references)
        //IL_006a: Unknown result type (might be due to invalid IL or missing references)
        //IL_008a: Unknown result type (might be due to invalid IL or missing references)
        TerrainModifier componentInChildren = prefab.GetComponentInChildren<TerrainModifier>();
        if (!Object.op_Implicit((Object)(object)componentInChildren) || (PrivateArea.CheckAccess(hitPoint, componentInChildren.GetRadius()) && !Location.IsInsideNoBuildLocation(hitPoint)))
        {
            TerrainOp componentInChildren2 = prefab.GetComponentInChildren<TerrainOp>();
            if (!Object.op_Implicit((Object)(object)componentInChildren2) || (PrivateArea.CheckAccess(hitPoint, componentInChildren2.GetRadius()) && !Location.IsInsideNoBuildLocation(hitPoint)))
            {
                TerrainModifier.SetTriggerOnPlaced(trigger: true);
                GameObject obj = Object.Instantiate<GameObject>(prefab, hitPoint, Quaternion.LookRotation(((Component)m_character).get_transform().get_forward()));
                TerrainModifier.SetTriggerOnPlaced(trigger: false);
                obj.GetComponent<IProjectile>()?.Setup(m_character, Vector3.get_zero(), m_attackHitNoise, null, m_weapon);
            }
        }
    }

    public Attack Clone()
    {
        return MemberwiseClone() as Attack;
    }

    public ItemDrop.ItemData GetWeapon()
    {
        return m_weapon;
    }

    public bool CanStartChainAttack()
    {
        if (m_nextAttackChainLevel > 0)
        {
            return m_animEvent.CanChain();
        }
        return false;
    }

    public void OnTrailStart()
    {
        //IL_0021: Unknown result type (might be due to invalid IL or missing references)
        //IL_0031: Unknown result type (might be due to invalid IL or missing references)
        //IL_0049: Unknown result type (might be due to invalid IL or missing references)
        //IL_0059: Unknown result type (might be due to invalid IL or missing references)
        //IL_0075: Unknown result type (might be due to invalid IL or missing references)
        //IL_0076: Unknown result type (might be due to invalid IL or missing references)
        //IL_007b: Unknown result type (might be due to invalid IL or missing references)
        //IL_0080: Unknown result type (might be due to invalid IL or missing references)
        //IL_0092: Unknown result type (might be due to invalid IL or missing references)
        //IL_0097: Unknown result type (might be due to invalid IL or missing references)
        //IL_00ab: Unknown result type (might be due to invalid IL or missing references)
        //IL_00b0: Unknown result type (might be due to invalid IL or missing references)
        if (m_attackType == AttackType.Projectile)
        {
            Transform attackOrigin = GetAttackOrigin();
            m_weapon.m_shared.m_trailStartEffect.Create(attackOrigin.get_position(), ((Component)m_character).get_transform().get_rotation());
            m_trailStartEffect.Create(attackOrigin.get_position(), ((Component)m_character).get_transform().get_rotation());
        }
        else
        {
            GetMeleeAttackDir(out var originJoint, out var attackDir);
            Quaternion rot = Quaternion.LookRotation(attackDir, Vector3.get_up());
            m_weapon.m_shared.m_trailStartEffect.Create(originJoint.get_position(), rot);
            m_trailStartEffect.Create(originJoint.get_position(), rot);
        }
    }
}
