

// Character
using System;
using System.Collections.Generic;
using UnityEngine;

public class Character : MonoBehaviour, IDestructible, Hoverable, IWaterInteractable
{
    public enum Faction
    {
        Players,
        AnimalsVeg,
        ForestMonsters,
        Undead,
        Demon,
        MountainMonsters,
        SeaMonsters,
        PlainsMonsters,
        Boss
    }

    public enum GroundTiltType
    {
        None,
        Pitch,
        Full,
        PitchRaycast,
        FullRaycast
    }

    public float m_underWorldCheckTimer;

    public Collider m_lowestContactCollider;

    public bool m_groundContact;

    public Vector3 m_groundContactPoint = Vector3.get_zero();

    public Vector3 m_groundContactNormal = Vector3.get_zero();

    public Action<float, Character> m_onDamaged;

    public Action m_onDeath;

    public Action<int> m_onLevelSet;

    public Action<Vector3> m_onLand;

    [Header("Character")]
    public string m_name = "";

    public Faction m_faction = Faction.AnimalsVeg;

    public bool m_boss;

    public string m_bossEvent = "";

    public string m_defeatSetGlobalKey = "";

    [Header("Movement & Physics")]
    public float m_crouchSpeed = 2f;

    public float m_walkSpeed = 5f;

    public float m_speed = 10f;

    public float m_turnSpeed = 300f;

    public float m_runSpeed = 20f;

    public float m_runTurnSpeed = 300f;

    public float m_flySlowSpeed = 5f;

    public float m_flyFastSpeed = 12f;

    public float m_flyTurnSpeed = 12f;

    public float m_acceleration = 1f;

    public float m_jumpForce = 10f;

    public float m_jumpForceForward;

    public float m_jumpForceTiredFactor = 0.7f;

    public float m_airControl = 0.1f;

    public const float m_slopeStaminaDrain = 10f;

    public const float m_minSlideDegreesPlayer = 38f;

    public const float m_minSlideDegreesMonster = 90f;

    public const float m_rootMotionMultiplier = 55f;

    public const float m_continousPushForce = 10f;

    public const float m_pushForcedissipation = 100f;

    public const float m_maxMoveForce = 20f;

    public bool m_canSwim = true;

    public float m_swimDepth = 2f;

    public float m_swimSpeed = 2f;

    public float m_swimTurnSpeed = 100f;

    public float m_swimAcceleration = 0.05f;

    public GroundTiltType m_groundTilt;

    public bool m_flying;

    public float m_jumpStaminaUsage = 10f;

    [Header("Bodyparts")]
    public Transform m_eye;

    public Transform m_head;

    [Header("Effects")]
    public EffectList m_hitEffects = new EffectList();

    public EffectList m_critHitEffects = new EffectList();

    public EffectList m_backstabHitEffects = new EffectList();

    public EffectList m_deathEffects = new EffectList();

    public EffectList m_waterEffects = new EffectList();

    public EffectList m_slideEffects = new EffectList();

    public EffectList m_jumpEffects = new EffectList();

    [Header("Health & Damage")]
    public bool m_tolerateWater = true;

    public bool m_tolerateFire;

    public bool m_tolerateSmoke = true;

    public float m_health = 10f;

    public HitData.DamageModifiers m_damageModifiers;

    public bool m_staggerWhenBlocked = true;

    public float m_staggerDamageFactor;

    public const float m_staggerResetTime = 3f;

    public float m_staggerDamage;

    public float m_staggerTimer;

    public float m_backstabTime = -99999f;

    public const float m_backstabResetTime = 300f;

    public GameObject[] m_waterEffects_instances;

    public GameObject[] m_slideEffects_instances;

    public Vector3 m_moveDir = Vector3.get_zero();

    public Vector3 m_lookDir = Vector3.get_forward();

    public Quaternion m_lookYaw = Quaternion.get_identity();

    public bool m_run;

    public bool m_walk;

    public bool m_attack;

    public bool m_attackDraw;

    public bool m_secondaryAttack;

    public bool m_blocking;

    public GameObject m_visual;

    public LODGroup m_lodGroup;

    public Rigidbody m_body;

    public CapsuleCollider m_collider;

    public ZNetView m_nview;

    public ZSyncAnimation m_zanim;

    public Animator m_animator;

    public CharacterAnimEvent m_animEvent;

    public BaseAI m_baseAI;

    public const float m_maxFallHeight = 20f;

    public const float m_minFallHeight = 4f;

    public const float m_maxFallDamage = 100f;

    public const float m_staggerDamageBonus = 2f;

    public const float m_baseVisualRange = 30f;

    public const float m_autoJumpInterval = 0.5f;

    public float m_jumpTimer;

    public float m_lastAutoJumpTime;

    public float m_lastGroundTouch;

    public Vector3 m_lastGroundNormal = Vector3.get_up();

    public Vector3 m_lastGroundPoint = Vector3.get_up();

    public Collider m_lastGroundCollider;

    public Rigidbody m_lastGroundBody;

    public Vector3 m_lastAttachPos = Vector3.get_zero();

    public Rigidbody m_lastAttachBody;

    public float m_maxAirAltitude = -10000f;

    public float m_waterLevel = -10000f;

    public float m_swimTimer = 999f;

    public SEMan m_seman;

    public float m_noiseRange;

    public float m_syncNoiseTimer;

    public bool m_tamed;

    public float m_lastTamedCheck;

    public int m_level = 1;

    public Vector3 m_currentVel = Vector3.get_zero();

    public float m_currentTurnVel;

    public float m_currentTurnVelChange;

    public Vector3 m_groundTiltNormal = Vector3.get_up();

    public Vector3 m_pushForce = Vector3.get_zero();

    public Vector3 m_rootMotion = Vector3.get_zero();

    public static int forward_speed = 0;

    public static int sideway_speed = 0;

    public static int turn_speed = 0;

    public static int inWater = 0;

    public static int onGround = 0;

    public static int encumbered = 0;

    public static int flying = 0;

    public float m_slippage;

    public bool m_wallRunning;

    public bool m_sliding;

    public bool m_running;

    public bool m_walking;

    public Vector3 m_originalLocalRef;

    public bool m_lodVisible = true;

    public static int m_smokeRayMask = 0;

    public float m_smokeCheckTimer;

    public static bool m_dpsDebugEnabled = false;

    public static List<KeyValuePair<float, float>> m_enemyDamage = new List<KeyValuePair<float, float>>();

    public static List<KeyValuePair<float, float>> m_playerDamage = new List<KeyValuePair<float, float>>();

    public static List<Character> m_characters = new List<Character>();

    public static int m_characterLayer = 0;

    public static int m_characterNetLayer = 0;

    public static int m_characterGhostLayer = 0;

    public static int m_animatorTagFreeze = Animator.StringToHash("freeze");

    public static int m_animatorTagStagger = Animator.StringToHash("stagger");

    public static int m_animatorTagSitting = Animator.StringToHash("sitting");

    public virtual void Awake()
    {
        //IL_018f: Unknown result type (might be due to invalid IL or missing references)
        //IL_0194: Unknown result type (might be due to invalid IL or missing references)
        m_characters.Add(this);
        m_collider = ((Component)this).GetComponent<CapsuleCollider>();
        m_body = ((Component)this).GetComponent<Rigidbody>();
        m_zanim = ((Component)this).GetComponent<ZSyncAnimation>();
        m_nview = ((Component)this).GetComponent<ZNetView>();
        m_animator = ((Component)this).GetComponentInChildren<Animator>();
        m_animEvent = ((Component)m_animator).GetComponent<CharacterAnimEvent>();
        m_baseAI = ((Component)this).GetComponent<BaseAI>();
        m_animator.set_logWarnings(false);
        m_visual = ((Component)((Component)this).get_transform().Find("Visual")).get_gameObject();
        m_lodGroup = m_visual.GetComponent<LODGroup>();
        m_head = m_animator.GetBoneTransform((HumanBodyBones)10);
        m_body.set_maxDepenetrationVelocity(2f);
        if (m_smokeRayMask == 0)
        {
            m_smokeRayMask = LayerMask.GetMask(new string[1] { "smoke" });
            m_characterLayer = LayerMask.NameToLayer("character");
            m_characterNetLayer = LayerMask.NameToLayer("character_net");
            m_characterGhostLayer = LayerMask.NameToLayer("character_ghost");
        }
        if (forward_speed == 0)
        {
            forward_speed = ZSyncAnimation.GetHash("forward_speed");
            sideway_speed = ZSyncAnimation.GetHash("sideway_speed");
            turn_speed = ZSyncAnimation.GetHash("turn_speed");
            inWater = ZSyncAnimation.GetHash("inWater");
            onGround = ZSyncAnimation.GetHash("onGround");
            encumbered = ZSyncAnimation.GetHash("encumbered");
            flying = ZSyncAnimation.GetHash("flying");
        }
        if (Object.op_Implicit((Object)(object)m_lodGroup))
        {
            m_originalLocalRef = m_lodGroup.get_localReferencePoint();
        }
        m_seman = new SEMan(this, m_nview);
        if (m_nview.GetZDO() == null)
        {
            return;
        }
        if (!IsPlayer())
        {
            m_tamed = m_nview.GetZDO().GetBool("tamed", m_tamed);
            m_level = m_nview.GetZDO().GetInt("level", 1);
            if (m_nview.IsOwner() && GetHealth() == GetMaxHealth())
            {
                SetupMaxHealth();
            }
        }
        m_nview.Register<HitData>("Damage", RPC_Damage);
        m_nview.Register<float, bool>("Heal", RPC_Heal);
        m_nview.Register<float>("AddNoise", RPC_AddNoise);
        m_nview.Register<Vector3>("Stagger", RPC_Stagger);
        m_nview.Register("ResetCloth", RPC_ResetCloth);
        m_nview.Register<bool>("SetTamed", RPC_SetTamed);
    }

    public void SetupMaxHealth()
    {
        //IL_0012: Unknown result type (might be due to invalid IL or missing references)
        int level = GetLevel();
        float difficultyHealthScale = Game.instance.GetDifficultyHealthScale(((Component)this).get_transform().get_position());
        SetMaxHealth(m_health * difficultyHealthScale * (float)level);
    }

    public virtual void Start()
    {
        m_nview.GetZDO();
    }

    public virtual void OnDestroy()
    {
        m_seman.OnDestroy();
        m_characters.Remove(this);
    }

    public void SetLevel(int level)
    {
        if (level >= 1)
        {
            m_level = level;
            m_nview.GetZDO().Set("level", level);
            SetupMaxHealth();
            if (m_onLevelSet != null)
            {
                m_onLevelSet(m_level);
            }
        }
    }

    public int GetLevel()
    {
        return m_level;
    }

    public virtual bool IsPlayer()
    {
        return false;
    }

    public Faction GetFaction()
    {
        return m_faction;
    }

    public virtual void FixedUpdate()
    {
        if (m_nview.IsValid())
        {
            float fixedDeltaTime = Time.get_fixedDeltaTime();
            UpdateLayer();
            UpdateContinousEffects();
            UpdateWater(fixedDeltaTime);
            UpdateGroundTilt(fixedDeltaTime);
            SetVisible(m_nview.HasOwner());
            if (m_nview.IsOwner())
            {
                UpdateGroundContact(fixedDeltaTime);
                UpdateNoise(fixedDeltaTime);
                m_seman.Update(fixedDeltaTime);
                UpdateStagger(fixedDeltaTime);
                UpdatePushback(fixedDeltaTime);
                UpdateMotion(fixedDeltaTime);
                UpdateSmoke(fixedDeltaTime);
                UnderWorldCheck(fixedDeltaTime);
                SyncVelocity();
                CheckDeath();
            }
        }
    }

    public void UpdateLayer()
    {
        if (((Component)m_collider).get_gameObject().get_layer() == m_characterLayer || ((Component)m_collider).get_gameObject().get_layer() == m_characterNetLayer)
        {
            if (m_nview.IsOwner())
            {
                ((Component)m_collider).get_gameObject().set_layer(IsAttached() ? m_characterNetLayer : m_characterLayer);
            }
            else
            {
                ((Component)m_collider).get_gameObject().set_layer(m_characterNetLayer);
            }
        }
    }

    public void UnderWorldCheck(float dt)
    {
        //IL_0042: Unknown result type (might be due to invalid IL or missing references)
        //IL_0053: Unknown result type (might be due to invalid IL or missing references)
        //IL_006c: Unknown result type (might be due to invalid IL or missing references)
        //IL_0071: Unknown result type (might be due to invalid IL or missing references)
        //IL_0086: Unknown result type (might be due to invalid IL or missing references)
        //IL_0092: Unknown result type (might be due to invalid IL or missing references)
        //IL_009e: Unknown result type (might be due to invalid IL or missing references)
        if (IsDead())
        {
            return;
        }
        m_underWorldCheckTimer += dt;
        if (m_underWorldCheckTimer > 5f || IsPlayer())
        {
            m_underWorldCheckTimer = 0f;
            float groundHeight = ZoneSystem.instance.GetGroundHeight(((Component)this).get_transform().get_position());
            if (((Component)this).get_transform().get_position().y < groundHeight - 1f)
            {
                Vector3 position = ((Component)this).get_transform().get_position();
                position.y = groundHeight + 0.5f;
                ((Component)this).get_transform().set_position(position);
                m_body.set_position(position);
                m_body.set_velocity(Vector3.get_zero());
            }
        }
    }

    public void UpdateSmoke(float dt)
    {
        //IL_0030: Unknown result type (might be due to invalid IL or missing references)
        //IL_0035: Unknown result type (might be due to invalid IL or missing references)
        //IL_003f: Unknown result type (might be due to invalid IL or missing references)
        //IL_0044: Unknown result type (might be due to invalid IL or missing references)
        if (m_tolerateSmoke)
        {
            return;
        }
        m_smokeCheckTimer += dt;
        if (m_smokeCheckTimer > 2f)
        {
            m_smokeCheckTimer = 0f;
            if (Physics.CheckSphere(GetTopPoint() + Vector3.get_up() * 0.1f, 0.5f, m_smokeRayMask))
            {
                m_seman.AddStatusEffect("Smoked", resetTime: true);
            }
            else
            {
                m_seman.RemoveStatusEffect("Smoked", quiet: true);
            }
        }
    }

    public void UpdateContinousEffects()
    {
        //IL_0007: Unknown result type (might be due to invalid IL or missing references)
        //IL_0029: Unknown result type (might be due to invalid IL or missing references)
        //IL_002e: Unknown result type (might be due to invalid IL or missing references)
        //IL_0043: Unknown result type (might be due to invalid IL or missing references)
        SetupContinousEffect(((Component)this).get_transform().get_position(), m_sliding, m_slideEffects, ref m_slideEffects_instances);
        Vector3 position = ((Component)this).get_transform().get_position();
        position.y = m_waterLevel + 0.05f;
        SetupContinousEffect(position, InWater(), m_waterEffects, ref m_waterEffects_instances);
    }

    public void SetupContinousEffect(Vector3 point, bool enabled, EffectList effects, ref GameObject[] instances)
    {
        //IL_0014: Unknown result type (might be due to invalid IL or missing references)
        //IL_0015: Unknown result type (might be due to invalid IL or missing references)
        //IL_0046: Unknown result type (might be due to invalid IL or missing references)
        //IL_008c: Unknown result type (might be due to invalid IL or missing references)
        //IL_0091: Unknown result type (might be due to invalid IL or missing references)
        if (!effects.HasEffects())
        {
            return;
        }
        if (enabled)
        {
            if (instances == null)
            {
                instances = effects.Create(point, Quaternion.get_identity(), ((Component)this).get_transform());
                return;
            }
            GameObject[] array = instances;
            foreach (GameObject val in array)
            {
                if (Object.op_Implicit((Object)(object)val))
                {
                    val.get_transform().set_position(point);
                }
            }
        }
        else
        {
            if (instances == null)
            {
                return;
            }
            GameObject[] array = instances;
            foreach (GameObject val2 in array)
            {
                if (Object.op_Implicit((Object)(object)val2))
                {
                    ParticleSystem[] componentsInChildren = val2.GetComponentsInChildren<ParticleSystem>();
                    foreach (ParticleSystem obj in componentsInChildren)
                    {
                        EmissionModule emission = obj.get_emission();
                        ((EmissionModule)(ref emission)).set_enabled(false);
                        obj.Stop();
                    }
                    CamShaker componentInChildren = val2.GetComponentInChildren<CamShaker>();
                    if (Object.op_Implicit((Object)(object)componentInChildren))
                    {
                        Object.Destroy((Object)(object)componentInChildren);
                    }
                    ZSFX componentInChildren2 = val2.GetComponentInChildren<ZSFX>();
                    if (Object.op_Implicit((Object)(object)componentInChildren2))
                    {
                        componentInChildren2.FadeOut();
                    }
                    TimedDestruction component = val2.GetComponent<TimedDestruction>();
                    if (Object.op_Implicit((Object)(object)component))
                    {
                        component.Trigger();
                    }
                    else
                    {
                        Object.Destroy((Object)(object)val2);
                    }
                }
            }
            instances = null;
        }
    }

    public virtual void OnSwiming(Vector3 targetVel, float dt)
    {
    }

    public virtual void OnSneaking(float dt)
    {
    }

    public virtual void OnJump()
    {
    }

    public virtual bool TakeInput()
    {
        return true;
    }

    public float GetSlideAngle()
    {
        if (!IsPlayer())
        {
            return 90f;
        }
        return 38f;
    }

    public void ApplySlide(float dt, ref Vector3 currentVel, Vector3 bodyVel, bool running)
    {
        //IL_0023: Unknown result type (might be due to invalid IL or missing references)
        //IL_0028: Unknown result type (might be due to invalid IL or missing references)
        //IL_0042: Unknown result type (might be due to invalid IL or missing references)
        //IL_0049: Unknown result type (might be due to invalid IL or missing references)
        //IL_004e: Unknown result type (might be due to invalid IL or missing references)
        //IL_0053: Unknown result type (might be due to invalid IL or missing references)
        //IL_0058: Unknown result type (might be due to invalid IL or missing references)
        //IL_005a: Unknown result type (might be due to invalid IL or missing references)
        //IL_005f: Unknown result type (might be due to invalid IL or missing references)
        //IL_0060: Unknown result type (might be due to invalid IL or missing references)
        //IL_0065: Unknown result type (might be due to invalid IL or missing references)
        //IL_00c7: Unknown result type (might be due to invalid IL or missing references)
        //IL_00cd: Unknown result type (might be due to invalid IL or missing references)
        //IL_00d2: Unknown result type (might be due to invalid IL or missing references)
        //IL_00d6: Unknown result type (might be due to invalid IL or missing references)
        //IL_00db: Unknown result type (might be due to invalid IL or missing references)
        //IL_00e3: Unknown result type (might be due to invalid IL or missing references)
        //IL_00e8: Unknown result type (might be due to invalid IL or missing references)
        bool flag = CanWallRun();
        float num = Mathf.Acos(Mathf.Clamp01(m_lastGroundNormal.y)) * 57.29578f;
        Vector3 lastGroundNormal = m_lastGroundNormal;
        lastGroundNormal.y = 0f;
        ((Vector3)(ref lastGroundNormal)).Normalize();
        m_body.get_velocity();
        Vector3 val = Vector3.Cross(m_lastGroundNormal, Vector3.get_up());
        Vector3 val2 = Vector3.Cross(m_lastGroundNormal, val);
        bool flag2 = ((Vector3)(ref currentVel)).get_magnitude() > 0.1f;
        if (num > GetSlideAngle())
        {
            if (running && flag && flag2)
            {
                UseStamina(10f * dt);
                m_slippage = 0f;
                m_wallRunning = true;
            }
            else
            {
                m_slippage = Mathf.MoveTowards(m_slippage, 1f, 1f * dt);
            }
            Vector3 val3 = val2 * 5f;
            currentVel = Vector3.Lerp(currentVel, val3, m_slippage);
            m_sliding = m_slippage > 0.5f;
        }
        else
        {
            m_slippage = 0f;
        }
    }

    public void UpdateMotion(float dt)
    {
        //IL_004a: Unknown result type (might be due to invalid IL or missing references)
        //IL_005f: Unknown result type (might be due to invalid IL or missing references)
        //IL_006f: Unknown result type (might be due to invalid IL or missing references)
        //IL_008f: Unknown result type (might be due to invalid IL or missing references)
        UpdateBodyFriction();
        m_sliding = false;
        m_wallRunning = false;
        m_running = false;
        m_walking = false;
        if (IsDead())
        {
            return;
        }
        if (IsDebugFlying())
        {
            UpdateDebugFly(dt);
            return;
        }
        if (InIntro())
        {
            m_maxAirAltitude = ((Component)this).get_transform().get_position().y;
            m_body.set_velocity(Vector3.get_zero());
            m_body.set_angularVelocity(Vector3.get_zero());
        }
        if (!InWaterSwimDepth() && !IsOnGround())
        {
            float y = ((Component)this).get_transform().get_position().y;
            m_maxAirAltitude = Mathf.Max(m_maxAirAltitude, y);
        }
        if (IsSwiming())
        {
            UpdateSwiming(dt);
        }
        else if (m_flying)
        {
            UpdateFlying(dt);
        }
        else
        {
            UpdateWalking(dt);
        }
        m_lastGroundTouch += Time.get_fixedDeltaTime();
        m_jumpTimer += Time.get_fixedDeltaTime();
    }

    public void UpdateDebugFly(float dt)
    {
        //IL_0011: Unknown result type (might be due to invalid IL or missing references)
        //IL_0017: Unknown result type (might be due to invalid IL or missing references)
        //IL_001c: Unknown result type (might be due to invalid IL or missing references)
        //IL_0052: Unknown result type (might be due to invalid IL or missing references)
        //IL_0057: Unknown result type (might be due to invalid IL or missing references)
        //IL_005d: Unknown result type (might be due to invalid IL or missing references)
        //IL_0062: Unknown result type (might be due to invalid IL or missing references)
        //IL_006e: Unknown result type (might be due to invalid IL or missing references)
        //IL_0096: Unknown result type (might be due to invalid IL or missing references)
        //IL_00b1: Unknown result type (might be due to invalid IL or missing references)
        //IL_00b7: Unknown result type (might be due to invalid IL or missing references)
        //IL_00c4: Unknown result type (might be due to invalid IL or missing references)
        //IL_00d4: Unknown result type (might be due to invalid IL or missing references)
        float num = (m_run ? 50 : 20);
        Vector3 val = m_moveDir * num;
        if (TakeInput())
        {
            if (ZInput.GetButton("Jump"))
            {
                val.y = num;
            }
            else if (Input.GetKey((KeyCode)306))
            {
                val.y = 0f - num;
            }
        }
        m_currentVel = Vector3.Lerp(m_currentVel, val, 0.5f);
        m_body.set_velocity(m_currentVel);
        m_body.set_useGravity(false);
        m_lastGroundTouch = 0f;
        m_maxAirAltitude = ((Component)this).get_transform().get_position().y;
        m_body.set_rotation(Quaternion.RotateTowards(((Component)this).get_transform().get_rotation(), m_lookYaw, m_turnSpeed * dt));
        m_body.set_angularVelocity(Vector3.get_zero());
        UpdateEyeRotation();
    }

    public void UpdateSwiming(float dt)
    {
        //IL_0018: Unknown result type (might be due to invalid IL or missing references)
        //IL_0043: Unknown result type (might be due to invalid IL or missing references)
        //IL_0059: Unknown result type (might be due to invalid IL or missing references)
        //IL_0063: Unknown result type (might be due to invalid IL or missing references)
        //IL_0074: Unknown result type (might be due to invalid IL or missing references)
        //IL_00ad: Unknown result type (might be due to invalid IL or missing references)
        //IL_00b3: Unknown result type (might be due to invalid IL or missing references)
        //IL_00b8: Unknown result type (might be due to invalid IL or missing references)
        //IL_00cf: Unknown result type (might be due to invalid IL or missing references)
        //IL_00d1: Unknown result type (might be due to invalid IL or missing references)
        //IL_00d6: Unknown result type (might be due to invalid IL or missing references)
        //IL_00db: Unknown result type (might be due to invalid IL or missing references)
        //IL_00df: Unknown result type (might be due to invalid IL or missing references)
        //IL_00eb: Unknown result type (might be due to invalid IL or missing references)
        //IL_00f0: Unknown result type (might be due to invalid IL or missing references)
        //IL_00fb: Unknown result type (might be due to invalid IL or missing references)
        //IL_0100: Unknown result type (might be due to invalid IL or missing references)
        //IL_0107: Unknown result type (might be due to invalid IL or missing references)
        //IL_010c: Unknown result type (might be due to invalid IL or missing references)
        //IL_0142: Unknown result type (might be due to invalid IL or missing references)
        //IL_0149: Unknown result type (might be due to invalid IL or missing references)
        //IL_014e: Unknown result type (might be due to invalid IL or missing references)
        //IL_0151: Unknown result type (might be due to invalid IL or missing references)
        //IL_0156: Unknown result type (might be due to invalid IL or missing references)
        //IL_015c: Unknown result type (might be due to invalid IL or missing references)
        //IL_0161: Unknown result type (might be due to invalid IL or missing references)
        //IL_018c: Unknown result type (might be due to invalid IL or missing references)
        //IL_0197: Unknown result type (might be due to invalid IL or missing references)
        //IL_019c: Unknown result type (might be due to invalid IL or missing references)
        //IL_01a1: Unknown result type (might be due to invalid IL or missing references)
        //IL_01be: Unknown result type (might be due to invalid IL or missing references)
        //IL_01c8: Unknown result type (might be due to invalid IL or missing references)
        //IL_01cd: Unknown result type (might be due to invalid IL or missing references)
        //IL_01d4: Unknown result type (might be due to invalid IL or missing references)
        //IL_01f0: Unknown result type (might be due to invalid IL or missing references)
        //IL_0206: Unknown result type (might be due to invalid IL or missing references)
        //IL_0237: Unknown result type (might be due to invalid IL or missing references)
        //IL_023c: Unknown result type (might be due to invalid IL or missing references)
        //IL_0240: Unknown result type (might be due to invalid IL or missing references)
        //IL_0260: Unknown result type (might be due to invalid IL or missing references)
        //IL_0271: Unknown result type (might be due to invalid IL or missing references)
        //IL_02a3: Unknown result type (might be due to invalid IL or missing references)
        //IL_02a8: Unknown result type (might be due to invalid IL or missing references)
        //IL_02ac: Unknown result type (might be due to invalid IL or missing references)
        //IL_02cd: Unknown result type (might be due to invalid IL or missing references)
        //IL_031b: Unknown result type (might be due to invalid IL or missing references)
        //IL_0338: Unknown result type (might be due to invalid IL or missing references)
        //IL_0343: Unknown result type (might be due to invalid IL or missing references)
        //IL_0350: Unknown result type (might be due to invalid IL or missing references)
        //IL_035b: Unknown result type (might be due to invalid IL or missing references)
        //IL_036d: Unknown result type (might be due to invalid IL or missing references)
        //IL_0378: Unknown result type (might be due to invalid IL or missing references)
        //IL_0438: Unknown result type (might be due to invalid IL or missing references)
        bool flag = IsOnGround();
        if (Mathf.Max(0f, m_maxAirAltitude - ((Component)this).get_transform().get_position().y) > 0.5f && m_onLand != null)
        {
            m_onLand(new Vector3(((Component)this).get_transform().get_position().x, m_waterLevel, ((Component)this).get_transform().get_position().z));
        }
        m_maxAirAltitude = ((Component)this).get_transform().get_position().y;
        float speed = m_swimSpeed * GetAttackSpeedFactorMovement();
        if (InMinorAction())
        {
            speed = 0f;
        }
        m_seman.ApplyStatusEffectSpeedMods(ref speed);
        Vector3 val = m_moveDir * speed;
        if (((Vector3)(ref val)).get_magnitude() > 0f && IsOnGround())
        {
            Vector3 val2 = Vector3.ProjectOnPlane(val, m_lastGroundNormal);
            val = ((Vector3)(ref val2)).get_normalized() * ((Vector3)(ref val)).get_magnitude();
        }
        if (IsPlayer())
        {
            m_currentVel = Vector3.Lerp(m_currentVel, val, m_swimAcceleration);
        }
        else
        {
            float magnitude = ((Vector3)(ref val)).get_magnitude();
            float magnitude2 = ((Vector3)(ref m_currentVel)).get_magnitude();
            if (magnitude > magnitude2)
            {
                magnitude = Mathf.MoveTowards(magnitude2, magnitude, m_swimAcceleration);
                val = ((Vector3)(ref val)).get_normalized() * magnitude;
            }
            m_currentVel = Vector3.Lerp(m_currentVel, val, 0.5f);
        }
        if (((Vector3)(ref val)).get_magnitude() > 0.1f)
        {
            AddNoise(15f);
        }
        AddPushbackForce(ref m_currentVel);
        Vector3 val3 = m_currentVel - m_body.get_velocity();
        val3.y = 0f;
        if (((Vector3)(ref val3)).get_magnitude() > 20f)
        {
            val3 = ((Vector3)(ref val3)).get_normalized() * 20f;
        }
        m_body.AddForce(val3, (ForceMode)2);
        float num = m_waterLevel - m_swimDepth;
        if (((Component)this).get_transform().get_position().y < num)
        {
            float num2 = Mathf.Clamp01((num - ((Component)this).get_transform().get_position().y) / 2f);
            float num3 = Mathf.Lerp(0f, 10f, num2);
            Vector3 velocity = m_body.get_velocity();
            velocity.y = Mathf.MoveTowards(velocity.y, num3, 50f * dt);
            m_body.set_velocity(velocity);
        }
        else
        {
            float num4 = Mathf.Clamp01((0f - (num - ((Component)this).get_transform().get_position().y)) / 1f);
            float num5 = Mathf.Lerp(0f, 10f, num4);
            Vector3 velocity2 = m_body.get_velocity();
            velocity2.y = Mathf.MoveTowards(velocity2.y, 0f - num5, 30f * dt);
            m_body.set_velocity(velocity2);
        }
        float num6 = 0f;
        if (((Vector3)(ref m_moveDir)).get_magnitude() > 0.1f || AlwaysRotateCamera())
        {
            float speed2 = m_swimTurnSpeed;
            m_seman.ApplyStatusEffectSpeedMods(ref speed2);
            num6 = UpdateRotation(speed2, dt);
        }
        m_body.set_angularVelocity(Vector3.get_zero());
        UpdateEyeRotation();
        m_body.set_useGravity(true);
        float num7 = Vector3.Dot(m_currentVel, ((Component)this).get_transform().get_forward());
        float value = Vector3.Dot(m_currentVel, ((Component)this).get_transform().get_right());
        float num8 = Vector3.Dot(m_body.get_velocity(), ((Component)this).get_transform().get_forward());
        m_currentTurnVel = Mathf.SmoothDamp(m_currentTurnVel, num6, ref m_currentTurnVelChange, 0.5f, 99f);
        m_zanim.SetFloat(forward_speed, IsPlayer() ? num7 : num8);
        m_zanim.SetFloat(sideway_speed, value);
        m_zanim.SetFloat(turn_speed, m_currentTurnVel);
        m_zanim.SetBool(inWater, !flag);
        m_zanim.SetBool(onGround, value: false);
        m_zanim.SetBool(encumbered, value: false);
        m_zanim.SetBool(flying, value: false);
        if (!flag)
        {
            OnSwiming(val, dt);
        }
    }

    public void UpdateFlying(float dt)
    {
        //IL_0026: Unknown result type (might be due to invalid IL or missing references)
        //IL_002e: Unknown result type (might be due to invalid IL or missing references)
        //IL_0034: Unknown result type (might be due to invalid IL or missing references)
        //IL_0039: Unknown result type (might be due to invalid IL or missing references)
        //IL_003c: Unknown result type (might be due to invalid IL or missing references)
        //IL_0041: Unknown result type (might be due to invalid IL or missing references)
        //IL_0048: Unknown result type (might be due to invalid IL or missing references)
        //IL_004d: Unknown result type (might be due to invalid IL or missing references)
        //IL_0059: Unknown result type (might be due to invalid IL or missing references)
        //IL_0081: Unknown result type (might be due to invalid IL or missing references)
        //IL_008c: Unknown result type (might be due to invalid IL or missing references)
        //IL_0091: Unknown result type (might be due to invalid IL or missing references)
        //IL_0096: Unknown result type (might be due to invalid IL or missing references)
        //IL_00a7: Unknown result type (might be due to invalid IL or missing references)
        //IL_00b1: Unknown result type (might be due to invalid IL or missing references)
        //IL_00b6: Unknown result type (might be due to invalid IL or missing references)
        //IL_00bd: Unknown result type (might be due to invalid IL or missing references)
        //IL_0119: Unknown result type (might be due to invalid IL or missing references)
        //IL_0136: Unknown result type (might be due to invalid IL or missing references)
        //IL_0141: Unknown result type (might be due to invalid IL or missing references)
        //IL_014e: Unknown result type (might be due to invalid IL or missing references)
        //IL_0159: Unknown result type (might be due to invalid IL or missing references)
        //IL_016b: Unknown result type (might be due to invalid IL or missing references)
        //IL_0176: Unknown result type (might be due to invalid IL or missing references)
        float num = (m_run ? m_flyFastSpeed : m_flySlowSpeed) * GetAttackSpeedFactorMovement();
        Vector3 val = (CanMove() ? (m_moveDir * num) : Vector3.get_zero());
        m_currentVel = Vector3.Lerp(m_currentVel, val, m_acceleration);
        m_maxAirAltitude = ((Component)this).get_transform().get_position().y;
        ApplyRootMotion(ref m_currentVel);
        AddPushbackForce(ref m_currentVel);
        Vector3 val2 = m_currentVel - m_body.get_velocity();
        if (((Vector3)(ref val2)).get_magnitude() > 20f)
        {
            val2 = ((Vector3)(ref val2)).get_normalized() * 20f;
        }
        m_body.AddForce(val2, (ForceMode)2);
        float num2 = 0f;
        if ((((Vector3)(ref m_moveDir)).get_magnitude() > 0.1f || AlwaysRotateCamera()) && !InDodge() && CanMove())
        {
            float speed = m_flyTurnSpeed;
            m_seman.ApplyStatusEffectSpeedMods(ref speed);
            num2 = UpdateRotation(speed, dt);
        }
        m_body.set_angularVelocity(Vector3.get_zero());
        UpdateEyeRotation();
        m_body.set_useGravity(false);
        float num3 = Vector3.Dot(m_currentVel, ((Component)this).get_transform().get_forward());
        float value = Vector3.Dot(m_currentVel, ((Component)this).get_transform().get_right());
        float num4 = Vector3.Dot(m_body.get_velocity(), ((Component)this).get_transform().get_forward());
        m_currentTurnVel = Mathf.SmoothDamp(m_currentTurnVel, num2, ref m_currentTurnVelChange, 0.5f, 99f);
        m_zanim.SetFloat(forward_speed, IsPlayer() ? num3 : num4);
        m_zanim.SetFloat(sideway_speed, value);
        m_zanim.SetFloat(turn_speed, m_currentTurnVel);
        m_zanim.SetBool(inWater, value: false);
        m_zanim.SetBool(onGround, value: false);
        m_zanim.SetBool(encumbered, value: false);
        m_zanim.SetBool(flying, value: true);
    }

    public void UpdateWalking(float dt)
    {
        //IL_0001: Unknown result type (might be due to invalid IL or missing references)
        //IL_0006: Unknown result type (might be due to invalid IL or missing references)
        //IL_0010: Unknown result type (might be due to invalid IL or missing references)
        //IL_00e1: Unknown result type (might be due to invalid IL or missing references)
        //IL_00e8: Unknown result type (might be due to invalid IL or missing references)
        //IL_00ea: Unknown result type (might be due to invalid IL or missing references)
        //IL_00ef: Unknown result type (might be due to invalid IL or missing references)
        //IL_0106: Unknown result type (might be due to invalid IL or missing references)
        //IL_0108: Unknown result type (might be due to invalid IL or missing references)
        //IL_010d: Unknown result type (might be due to invalid IL or missing references)
        //IL_0112: Unknown result type (might be due to invalid IL or missing references)
        //IL_0116: Unknown result type (might be due to invalid IL or missing references)
        //IL_0122: Unknown result type (might be due to invalid IL or missing references)
        //IL_0127: Unknown result type (might be due to invalid IL or missing references)
        //IL_0157: Unknown result type (might be due to invalid IL or missing references)
        //IL_015e: Unknown result type (might be due to invalid IL or missing references)
        //IL_0163: Unknown result type (might be due to invalid IL or missing references)
        //IL_0199: Unknown result type (might be due to invalid IL or missing references)
        //IL_01a0: Unknown result type (might be due to invalid IL or missing references)
        //IL_01a9: Unknown result type (might be due to invalid IL or missing references)
        //IL_01b0: Unknown result type (might be due to invalid IL or missing references)
        //IL_01b5: Unknown result type (might be due to invalid IL or missing references)
        //IL_01b8: Unknown result type (might be due to invalid IL or missing references)
        //IL_01bd: Unknown result type (might be due to invalid IL or missing references)
        //IL_01c3: Unknown result type (might be due to invalid IL or missing references)
        //IL_01c8: Unknown result type (might be due to invalid IL or missing references)
        //IL_01d3: Unknown result type (might be due to invalid IL or missing references)
        //IL_01d8: Unknown result type (might be due to invalid IL or missing references)
        //IL_01db: Unknown result type (might be due to invalid IL or missing references)
        //IL_01e0: Unknown result type (might be due to invalid IL or missing references)
        //IL_01e4: Unknown result type (might be due to invalid IL or missing references)
        //IL_020a: Unknown result type (might be due to invalid IL or missing references)
        //IL_022a: Unknown result type (might be due to invalid IL or missing references)
        //IL_0230: Unknown result type (might be due to invalid IL or missing references)
        //IL_0232: Unknown result type (might be due to invalid IL or missing references)
        //IL_0234: Unknown result type (might be due to invalid IL or missing references)
        //IL_0239: Unknown result type (might be due to invalid IL or missing references)
        //IL_0251: Unknown result type (might be due to invalid IL or missing references)
        //IL_0259: Unknown result type (might be due to invalid IL or missing references)
        //IL_025e: Unknown result type (might be due to invalid IL or missing references)
        //IL_0262: Unknown result type (might be due to invalid IL or missing references)
        //IL_0267: Unknown result type (might be due to invalid IL or missing references)
        //IL_0271: Unknown result type (might be due to invalid IL or missing references)
        //IL_0276: Unknown result type (might be due to invalid IL or missing references)
        //IL_0297: Unknown result type (might be due to invalid IL or missing references)
        //IL_02a1: Unknown result type (might be due to invalid IL or missing references)
        //IL_02a6: Unknown result type (might be due to invalid IL or missing references)
        //IL_02bc: Unknown result type (might be due to invalid IL or missing references)
        //IL_0325: Unknown result type (might be due to invalid IL or missing references)
        //IL_0327: Unknown result type (might be due to invalid IL or missing references)
        //IL_032e: Unknown result type (might be due to invalid IL or missing references)
        //IL_0339: Unknown result type (might be due to invalid IL or missing references)
        //IL_03b4: Unknown result type (might be due to invalid IL or missing references)
        //IL_03bf: Unknown result type (might be due to invalid IL or missing references)
        //IL_03c5: Unknown result type (might be due to invalid IL or missing references)
        //IL_03ca: Unknown result type (might be due to invalid IL or missing references)
        //IL_03cf: Unknown result type (might be due to invalid IL or missing references)
        //IL_03d3: Unknown result type (might be due to invalid IL or missing references)
        //IL_03e0: Unknown result type (might be due to invalid IL or missing references)
        //IL_03eb: Unknown result type (might be due to invalid IL or missing references)
        //IL_03f1: Unknown result type (might be due to invalid IL or missing references)
        //IL_03f6: Unknown result type (might be due to invalid IL or missing references)
        //IL_03fb: Unknown result type (might be due to invalid IL or missing references)
        //IL_03ff: Unknown result type (might be due to invalid IL or missing references)
        //IL_0411: Unknown result type (might be due to invalid IL or missing references)
        //IL_041c: Unknown result type (might be due to invalid IL or missing references)
        Vector3 moveDir = m_moveDir;
        bool flag = IsCrouching();
        m_running = CheckRun(moveDir, dt);
        float num = m_speed * GetJogSpeedFactor();
        if ((m_walk || InMinorAction()) && !flag)
        {
            num = m_walkSpeed;
            m_walking = ((Vector3)(ref moveDir)).get_magnitude() > 0.1f;
        }
        else if (m_running)
        {
            bool num2 = InWaterDepth() > 0.4f;
            float num3 = m_runSpeed * GetRunSpeedFactor();
            num = (num2 ? Mathf.Lerp(num, num3, 0.33f) : num3);
            if (IsPlayer() && ((Vector3)(ref moveDir)).get_magnitude() > 0f)
            {
                ((Vector3)(ref moveDir)).Normalize();
            }
        }
        else if (flag || IsEncumbered())
        {
            num = m_crouchSpeed;
        }
        num *= GetAttackSpeedFactorMovement();
        m_seman.ApplyStatusEffectSpeedMods(ref num);
        Vector3 val = (CanMove() ? (moveDir * num) : Vector3.get_zero());
        Vector3 val2;
        if (((Vector3)(ref val)).get_magnitude() > 0f && IsOnGround())
        {
            val2 = Vector3.ProjectOnPlane(val, m_lastGroundNormal);
            val = ((Vector3)(ref val2)).get_normalized() * ((Vector3)(ref val)).get_magnitude();
        }
        float magnitude = ((Vector3)(ref val)).get_magnitude();
        float magnitude2 = ((Vector3)(ref m_currentVel)).get_magnitude();
        if (magnitude > magnitude2)
        {
            magnitude = Mathf.MoveTowards(magnitude2, magnitude, m_acceleration);
            val = ((Vector3)(ref val)).get_normalized() * magnitude;
        }
        else if (IsPlayer())
        {
            magnitude = Mathf.MoveTowards(magnitude2, magnitude, m_acceleration * 2f);
            val = ((((Vector3)(ref val)).get_magnitude() > 0f) ? (((Vector3)(ref val)).get_normalized() * magnitude) : (((Vector3)(ref m_currentVel)).get_normalized() * magnitude));
        }
        m_currentVel = Vector3.Lerp(m_currentVel, val, 0.5f);
        Vector3 velocity = m_body.get_velocity();
        Vector3 vel = m_currentVel;
        vel.y = velocity.y;
        if (IsOnGround() && (Object)(object)m_lastAttachBody == (Object)null)
        {
            ApplySlide(dt, ref vel, velocity, m_running);
        }
        ApplyRootMotion(ref vel);
        AddPushbackForce(ref vel);
        ApplyGroundForce(ref vel, val);
        Vector3 val3 = vel - velocity;
        if (!IsOnGround())
        {
            val3 = ((!(((Vector3)(ref val)).get_magnitude() > 0.1f)) ? Vector3.get_zero() : (val3 * m_airControl));
        }
        if (IsAttached())
        {
            val3 = Vector3.get_zero();
        }
        if (IsSneaking())
        {
            OnSneaking(dt);
        }
        if (((Vector3)(ref val3)).get_magnitude() > 20f)
        {
            val3 = ((Vector3)(ref val3)).get_normalized() * 20f;
        }
        if (((Vector3)(ref val3)).get_magnitude() > 0.01f)
        {
            m_body.AddForce(val3, (ForceMode)2);
        }
        if (Object.op_Implicit((Object)(object)m_lastGroundBody) && ((Component)m_lastGroundBody).get_gameObject().get_layer() != ((Component)this).get_gameObject().get_layer() && m_lastGroundBody.get_mass() > m_body.get_mass())
        {
            float num4 = m_body.get_mass() / m_lastGroundBody.get_mass();
            m_lastGroundBody.AddForceAtPosition(-val3 * num4, ((Component)this).get_transform().get_position(), (ForceMode)2);
        }
        float num5 = 0f;
        if ((((Vector3)(ref moveDir)).get_magnitude() > 0.1f || AlwaysRotateCamera()) && !InDodge() && CanMove())
        {
            float speed = (m_run ? m_runTurnSpeed : m_turnSpeed);
            m_seman.ApplyStatusEffectSpeedMods(ref speed);
            num5 = UpdateRotation(speed, dt);
        }
        UpdateEyeRotation();
        m_body.set_useGravity(true);
        Vector3 currentVel = m_currentVel;
        val2 = Vector3.ProjectOnPlane(((Component)this).get_transform().get_forward(), m_lastGroundNormal);
        float num6 = Vector3.Dot(currentVel, ((Vector3)(ref val2)).get_normalized());
        Vector3 currentVel2 = m_currentVel;
        val2 = Vector3.ProjectOnPlane(((Component)this).get_transform().get_right(), m_lastGroundNormal);
        float value = Vector3.Dot(currentVel2, ((Vector3)(ref val2)).get_normalized());
        float num7 = Vector3.Dot(m_body.get_velocity(), ((Component)this).get_transform().get_forward());
        m_currentTurnVel = Mathf.SmoothDamp(m_currentTurnVel, num5, ref m_currentTurnVelChange, 0.5f, 99f);
        m_zanim.SetFloat(forward_speed, IsPlayer() ? num6 : num7);
        m_zanim.SetFloat(sideway_speed, value);
        m_zanim.SetFloat(turn_speed, m_currentTurnVel);
        m_zanim.SetBool(inWater, value: false);
        m_zanim.SetBool(onGround, IsOnGround());
        m_zanim.SetBool(encumbered, IsEncumbered());
        m_zanim.SetBool(flying, value: false);
        if (((Vector3)(ref m_currentVel)).get_magnitude() > 0.1f)
        {
            if (m_running)
            {
                AddNoise(30f);
            }
            else if (!flag)
            {
                AddNoise(15f);
            }
        }
    }

    public bool IsSneaking()
    {
        if (IsCrouching() && ((Vector3)(ref m_currentVel)).get_magnitude() > 0.1f)
        {
            return IsOnGround();
        }
        return false;
    }

    public float GetSlopeAngle()
    {
        //IL_0014: Unknown result type (might be due to invalid IL or missing references)
        //IL_001a: Unknown result type (might be due to invalid IL or missing references)
        //IL_0025: Unknown result type (might be due to invalid IL or missing references)
        if (!IsOnGround())
        {
            return 0f;
        }
        float num = Vector3.SignedAngle(((Component)this).get_transform().get_forward(), m_lastGroundNormal, ((Component)this).get_transform().get_right());
        return 0f - (90f - (0f - num));
    }

    public void AddPushbackForce(ref Vector3 velocity)
    {
        //IL_0001: Unknown result type (might be due to invalid IL or missing references)
        //IL_0006: Unknown result type (might be due to invalid IL or missing references)
        //IL_0018: Unknown result type (might be due to invalid IL or missing references)
        //IL_001d: Unknown result type (might be due to invalid IL or missing references)
        //IL_001e: Unknown result type (might be due to invalid IL or missing references)
        //IL_0020: Unknown result type (might be due to invalid IL or missing references)
        //IL_0035: Unknown result type (might be due to invalid IL or missing references)
        //IL_003a: Unknown result type (might be due to invalid IL or missing references)
        //IL_0042: Unknown result type (might be due to invalid IL or missing references)
        //IL_0047: Unknown result type (might be due to invalid IL or missing references)
        //IL_004c: Unknown result type (might be due to invalid IL or missing references)
        //IL_0063: Unknown result type (might be due to invalid IL or missing references)
        //IL_006d: Unknown result type (might be due to invalid IL or missing references)
        //IL_0072: Unknown result type (might be due to invalid IL or missing references)
        if (m_pushForce != Vector3.get_zero())
        {
            Vector3 normalized = ((Vector3)(ref m_pushForce)).get_normalized();
            float num = Vector3.Dot(normalized, velocity);
            if (num < 10f)
            {
                velocity += normalized * (10f - num);
            }
            if (IsSwiming() || m_flying)
            {
                velocity *= 0.5f;
            }
        }
    }

    public void ApplyPushback(HitData hit)
    {
        //IL_004b: Unknown result type (might be due to invalid IL or missing references)
        //IL_0051: Unknown result type (might be due to invalid IL or missing references)
        //IL_0056: Unknown result type (might be due to invalid IL or missing references)
        //IL_0078: Unknown result type (might be due to invalid IL or missing references)
        //IL_0079: Unknown result type (might be due to invalid IL or missing references)
        if (hit.m_pushForce != 0f)
        {
            float num = hit.m_pushForce * Mathf.Clamp01(1f + GetEquipmentMovementModifier() * 1.5f);
            float num2 = Mathf.Min(40f, num / m_body.get_mass() * 5f);
            Vector3 pushForce = hit.m_dir * num2;
            pushForce.y = 0f;
            if (((Vector3)(ref m_pushForce)).get_magnitude() < ((Vector3)(ref pushForce)).get_magnitude())
            {
                m_pushForce = pushForce;
            }
        }
    }

    public void UpdatePushback(float dt)
    {
        //IL_0002: Unknown result type (might be due to invalid IL or missing references)
        //IL_0007: Unknown result type (might be due to invalid IL or missing references)
        //IL_0013: Unknown result type (might be due to invalid IL or missing references)
        //IL_0018: Unknown result type (might be due to invalid IL or missing references)
        m_pushForce = Vector3.MoveTowards(m_pushForce, Vector3.get_zero(), 100f * dt);
    }

    public void ApplyGroundForce(ref Vector3 vel, Vector3 targetVel)
    {
        //IL_0000: Unknown result type (might be due to invalid IL or missing references)
        //IL_0005: Unknown result type (might be due to invalid IL or missing references)
        //IL_0027: Unknown result type (might be due to invalid IL or missing references)
        //IL_002c: Unknown result type (might be due to invalid IL or missing references)
        //IL_0031: Unknown result type (might be due to invalid IL or missing references)
        //IL_0099: Unknown result type (might be due to invalid IL or missing references)
        //IL_009e: Unknown result type (might be due to invalid IL or missing references)
        //IL_00a3: Unknown result type (might be due to invalid IL or missing references)
        //IL_00c4: Unknown result type (might be due to invalid IL or missing references)
        //IL_00c9: Unknown result type (might be due to invalid IL or missing references)
        //IL_00ce: Unknown result type (might be due to invalid IL or missing references)
        //IL_00cf: Unknown result type (might be due to invalid IL or missing references)
        //IL_00d6: Unknown result type (might be due to invalid IL or missing references)
        //IL_00db: Unknown result type (might be due to invalid IL or missing references)
        //IL_00e0: Unknown result type (might be due to invalid IL or missing references)
        //IL_00ef: Unknown result type (might be due to invalid IL or missing references)
        //IL_00f0: Unknown result type (might be due to invalid IL or missing references)
        //IL_00fa: Unknown result type (might be due to invalid IL or missing references)
        //IL_011d: Unknown result type (might be due to invalid IL or missing references)
        //IL_011e: Unknown result type (might be due to invalid IL or missing references)
        //IL_0124: Unknown result type (might be due to invalid IL or missing references)
        //IL_0129: Unknown result type (might be due to invalid IL or missing references)
        //IL_012e: Unknown result type (might be due to invalid IL or missing references)
        //IL_0137: Unknown result type (might be due to invalid IL or missing references)
        //IL_0152: Unknown result type (might be due to invalid IL or missing references)
        //IL_0157: Unknown result type (might be due to invalid IL or missing references)
        //IL_0158: Unknown result type (might be due to invalid IL or missing references)
        //IL_015d: Unknown result type (might be due to invalid IL or missing references)
        Vector3 val = Vector3.get_zero();
        if (IsOnGround() && Object.op_Implicit((Object)(object)m_lastGroundBody))
        {
            val = m_lastGroundBody.GetPointVelocity(((Component)this).get_transform().get_position());
            val.y = 0f;
        }
        Ship standingOnShip = GetStandingOnShip();
        if ((Object)(object)standingOnShip != (Object)null)
        {
            if (((Vector3)(ref targetVel)).get_magnitude() > 0.01f)
            {
                m_lastAttachBody = null;
            }
            else if ((Object)(object)m_lastAttachBody != (Object)(object)m_lastGroundBody)
            {
                m_lastAttachBody = m_lastGroundBody;
                m_lastAttachPos = ((Component)m_lastAttachBody).get_transform().InverseTransformPoint(m_body.get_position());
            }
            if (Object.op_Implicit((Object)(object)m_lastAttachBody))
            {
                Vector3 val2 = ((Component)m_lastAttachBody).get_transform().TransformPoint(m_lastAttachPos);
                Vector3 val3 = val2 - m_body.get_position();
                if (((Vector3)(ref val3)).get_magnitude() < 4f)
                {
                    Vector3 position = val2;
                    position.y = m_body.get_position().y;
                    if (standingOnShip.IsOwner())
                    {
                        val3.y = 0f;
                        val += val3 * 10f;
                    }
                    else
                    {
                        m_body.set_position(position);
                    }
                }
                else
                {
                    m_lastAttachBody = null;
                }
            }
        }
        else
        {
            m_lastAttachBody = null;
        }
        vel += val;
    }

    public float UpdateRotation(float turnSpeed, float dt)
    {
        //IL_0009: Unknown result type (might be due to invalid IL or missing references)
        //IL_000e: Unknown result type (might be due to invalid IL or missing references)
        //IL_0016: Unknown result type (might be due to invalid IL or missing references)
        //IL_001b: Unknown result type (might be due to invalid IL or missing references)
        //IL_0022: Unknown result type (might be due to invalid IL or missing references)
        //IL_0027: Unknown result type (might be due to invalid IL or missing references)
        //IL_006b: Unknown result type (might be due to invalid IL or missing references)
        //IL_0070: Unknown result type (might be due to invalid IL or missing references)
        //IL_0074: Unknown result type (might be due to invalid IL or missing references)
        //IL_0079: Unknown result type (might be due to invalid IL or missing references)
        //IL_008e: Unknown result type (might be due to invalid IL or missing references)
        Quaternion val = (AlwaysRotateCamera() ? m_lookYaw : Quaternion.LookRotation(m_moveDir));
        float yawDeltaAngle = Utils.GetYawDeltaAngle(((Component)this).get_transform().get_rotation(), val);
        float num = 1f;
        if (!IsPlayer())
        {
            num = Mathf.Clamp01(Mathf.Abs(yawDeltaAngle) / 90f);
            num = Mathf.Pow(num, 0.5f);
        }
        float num2 = turnSpeed * GetAttackSpeedFactorRotation() * num;
        Quaternion rotation = Quaternion.RotateTowards(((Component)this).get_transform().get_rotation(), val, num2 * dt);
        if (Mathf.Abs(yawDeltaAngle) > 0.001f)
        {
            ((Component)this).get_transform().set_rotation(rotation);
        }
        return num2 * Mathf.Sign(yawDeltaAngle) * ((float)Math.PI / 180f);
    }

    public void UpdateGroundTilt(float dt)
    {
        //IL_0041: Unknown result type (might be due to invalid IL or missing references)
        //IL_0046: Unknown result type (might be due to invalid IL or missing references)
        //IL_0062: Unknown result type (might be due to invalid IL or missing references)
        //IL_006d: Unknown result type (might be due to invalid IL or missing references)
        //IL_007d: Unknown result type (might be due to invalid IL or missing references)
        //IL_0082: Unknown result type (might be due to invalid IL or missing references)
        //IL_0087: Unknown result type (might be due to invalid IL or missing references)
        //IL_008f: Unknown result type (might be due to invalid IL or missing references)
        //IL_009a: Unknown result type (might be due to invalid IL or missing references)
        //IL_00aa: Unknown result type (might be due to invalid IL or missing references)
        //IL_00af: Unknown result type (might be due to invalid IL or missing references)
        //IL_00b4: Unknown result type (might be due to invalid IL or missing references)
        //IL_00bb: Unknown result type (might be due to invalid IL or missing references)
        //IL_00cc: Unknown result type (might be due to invalid IL or missing references)
        //IL_00d8: Unknown result type (might be due to invalid IL or missing references)
        //IL_00d9: Unknown result type (might be due to invalid IL or missing references)
        //IL_00db: Unknown result type (might be due to invalid IL or missing references)
        //IL_00e0: Unknown result type (might be due to invalid IL or missing references)
        //IL_00e2: Unknown result type (might be due to invalid IL or missing references)
        //IL_00e7: Unknown result type (might be due to invalid IL or missing references)
        //IL_00eb: Unknown result type (might be due to invalid IL or missing references)
        //IL_00f0: Unknown result type (might be due to invalid IL or missing references)
        //IL_00f7: Unknown result type (might be due to invalid IL or missing references)
        //IL_00f8: Unknown result type (might be due to invalid IL or missing references)
        //IL_00fd: Unknown result type (might be due to invalid IL or missing references)
        //IL_00fe: Unknown result type (might be due to invalid IL or missing references)
        //IL_0103: Unknown result type (might be due to invalid IL or missing references)
        //IL_010e: Unknown result type (might be due to invalid IL or missing references)
        //IL_0113: Unknown result type (might be due to invalid IL or missing references)
        //IL_0116: Unknown result type (might be due to invalid IL or missing references)
        //IL_011b: Unknown result type (might be due to invalid IL or missing references)
        //IL_0121: Unknown result type (might be due to invalid IL or missing references)
        //IL_0126: Unknown result type (might be due to invalid IL or missing references)
        //IL_013e: Unknown result type (might be due to invalid IL or missing references)
        //IL_0143: Unknown result type (might be due to invalid IL or missing references)
        //IL_0148: Unknown result type (might be due to invalid IL or missing references)
        //IL_014d: Unknown result type (might be due to invalid IL or missing references)
        //IL_0150: Unknown result type (might be due to invalid IL or missing references)
        //IL_0155: Unknown result type (might be due to invalid IL or missing references)
        //IL_0157: Unknown result type (might be due to invalid IL or missing references)
        //IL_015c: Unknown result type (might be due to invalid IL or missing references)
        //IL_0160: Unknown result type (might be due to invalid IL or missing references)
        //IL_0165: Unknown result type (might be due to invalid IL or missing references)
        //IL_0166: Unknown result type (might be due to invalid IL or missing references)
        //IL_0167: Unknown result type (might be due to invalid IL or missing references)
        //IL_016c: Unknown result type (might be due to invalid IL or missing references)
        //IL_0171: Unknown result type (might be due to invalid IL or missing references)
        //IL_017d: Unknown result type (might be due to invalid IL or missing references)
        //IL_017e: Unknown result type (might be due to invalid IL or missing references)
        //IL_017f: Unknown result type (might be due to invalid IL or missing references)
        //IL_01a1: Unknown result type (might be due to invalid IL or missing references)
        //IL_01a6: Unknown result type (might be due to invalid IL or missing references)
        //IL_01b2: Unknown result type (might be due to invalid IL or missing references)
        //IL_01d7: Unknown result type (might be due to invalid IL or missing references)
        //IL_01f5: Unknown result type (might be due to invalid IL or missing references)
        //IL_01fb: Unknown result type (might be due to invalid IL or missing references)
        //IL_0205: Unknown result type (might be due to invalid IL or missing references)
        //IL_020a: Unknown result type (might be due to invalid IL or missing references)
        //IL_0212: Unknown result type (might be due to invalid IL or missing references)
        //IL_0217: Unknown result type (might be due to invalid IL or missing references)
        //IL_0219: Unknown result type (might be due to invalid IL or missing references)
        //IL_021e: Unknown result type (might be due to invalid IL or missing references)
        //IL_0227: Unknown result type (might be due to invalid IL or missing references)
        //IL_0229: Unknown result type (might be due to invalid IL or missing references)
        //IL_022b: Unknown result type (might be due to invalid IL or missing references)
        //IL_0230: Unknown result type (might be due to invalid IL or missing references)
        //IL_0248: Unknown result type (might be due to invalid IL or missing references)
        //IL_024d: Unknown result type (might be due to invalid IL or missing references)
        //IL_0256: Unknown result type (might be due to invalid IL or missing references)
        //IL_0278: Unknown result type (might be due to invalid IL or missing references)
        //IL_027d: Unknown result type (might be due to invalid IL or missing references)
        //IL_0289: Unknown result type (might be due to invalid IL or missing references)
        //IL_02ae: Unknown result type (might be due to invalid IL or missing references)
        //IL_02d9: Unknown result type (might be due to invalid IL or missing references)
        //IL_02de: Unknown result type (might be due to invalid IL or missing references)
        //IL_02e3: Unknown result type (might be due to invalid IL or missing references)
        //IL_02f0: Unknown result type (might be due to invalid IL or missing references)
        if ((Object)(object)m_visual == (Object)null)
        {
            return;
        }
        if (m_nview.IsOwner())
        {
            if (m_groundTilt != 0)
            {
                if (!IsFlying() && IsOnGround())
                {
                    Vector3 val = m_lastGroundNormal;
                    if (m_groundTilt == GroundTiltType.PitchRaycast || m_groundTilt == GroundTiltType.FullRaycast)
                    {
                        Vector3 p = ((Component)this).get_transform().get_position() + ((Component)this).get_transform().get_forward() * m_collider.get_radius();
                        Vector3 p2 = ((Component)this).get_transform().get_position() - ((Component)this).get_transform().get_forward() * m_collider.get_radius();
                        ZoneSystem.instance.GetSolidHeight(p, out var _, out var normal);
                        ZoneSystem.instance.GetSolidHeight(p2, out var _, out var normal2);
                        Vector3 val2 = val + normal + normal2;
                        val = ((Vector3)(ref val2)).get_normalized();
                    }
                    Vector3 val3 = ((Component)this).get_transform().InverseTransformVector(val);
                    val3 = Vector3.RotateTowards(Vector3.get_up(), val3, 0.87266463f, 1f);
                    m_groundTiltNormal = Vector3.Lerp(m_groundTiltNormal, val3, 0.05f);
                    Vector3 val5;
                    if (m_groundTilt == GroundTiltType.Pitch || m_groundTilt == GroundTiltType.PitchRaycast)
                    {
                        Vector3 val4 = Vector3.Project(m_groundTiltNormal, Vector3.get_right());
                        val5 = m_groundTiltNormal - val4;
                    }
                    else
                    {
                        val5 = m_groundTiltNormal;
                    }
                    Vector3 val6 = Vector3.Cross(val5, Vector3.get_left());
                    m_visual.get_transform().set_localRotation(Quaternion.LookRotation(val6, val5));
                }
                else
                {
                    m_visual.get_transform().set_localRotation(Quaternion.RotateTowards(m_visual.get_transform().get_localRotation(), Quaternion.get_identity(), dt * 200f));
                }
                m_nview.GetZDO().Set("tiltrot", m_visual.get_transform().get_localRotation());
            }
            else if (CanWallRun())
            {
                if (m_wallRunning)
                {
                    Vector3 val7 = Vector3.Lerp(Vector3.get_up(), m_lastGroundNormal, 0.65f);
                    Vector3 val8 = Vector3.ProjectOnPlane(((Component)this).get_transform().get_forward(), val7);
                    ((Vector3)(ref val8)).Normalize();
                    Quaternion val9 = Quaternion.LookRotation(val8, val7);
                    m_visual.get_transform().set_rotation(Quaternion.RotateTowards(m_visual.get_transform().get_rotation(), val9, 30f * dt));
                }
                else
                {
                    m_visual.get_transform().set_localRotation(Quaternion.RotateTowards(m_visual.get_transform().get_localRotation(), Quaternion.get_identity(), 100f * dt));
                }
                m_nview.GetZDO().Set("tiltrot", m_visual.get_transform().get_localRotation());
            }
        }
        else if (m_groundTilt != 0 || CanWallRun())
        {
            Quaternion quaternion = m_nview.GetZDO().GetQuaternion("tiltrot", Quaternion.get_identity());
            m_visual.get_transform().set_localRotation(quaternion);
        }
    }

    public bool IsWallRunning()
    {
        return m_wallRunning;
    }

    public bool IsOnSnow()
    {
        return false;
    }

    public void Heal(float hp, bool showText = true)
    {
        if (!(hp <= 0f))
        {
            if (m_nview.IsOwner())
            {
                RPC_Heal(0L, hp, showText);
                return;
            }
            m_nview.InvokeRPC("Heal", hp, showText);
        }
    }

    public void RPC_Heal(long sender, float hp, bool showText)
    {
        //IL_0044: Unknown result type (might be due to invalid IL or missing references)
        //IL_0049: Unknown result type (might be due to invalid IL or missing references)
        //IL_0050: Unknown result type (might be due to invalid IL or missing references)
        if (!m_nview.IsOwner())
        {
            return;
        }
        float health = GetHealth();
        if (health <= 0f || IsDead())
        {
            return;
        }
        float num = Mathf.Min(health + hp, GetMaxHealth());
        if (num > health)
        {
            SetHealth(num);
            if (showText)
            {
                Vector3 topPoint = GetTopPoint();
                DamageText.instance.ShowText(DamageText.TextType.Heal, topPoint, hp, IsPlayer());
            }
        }
    }

    public Vector3 GetTopPoint()
    {
        //IL_0006: Unknown result type (might be due to invalid IL or missing references)
        //IL_000b: Unknown result type (might be due to invalid IL or missing references)
        //IL_000e: Unknown result type (might be due to invalid IL or missing references)
        //IL_0013: Unknown result type (might be due to invalid IL or missing references)
        //IL_001c: Unknown result type (might be due to invalid IL or missing references)
        //IL_0021: Unknown result type (might be due to invalid IL or missing references)
        //IL_0024: Unknown result type (might be due to invalid IL or missing references)
        //IL_0033: Unknown result type (might be due to invalid IL or missing references)
        Bounds bounds = ((Collider)m_collider).get_bounds();
        Vector3 center = ((Bounds)(ref bounds)).get_center();
        bounds = ((Collider)m_collider).get_bounds();
        center.y = ((Bounds)(ref bounds)).get_max().y;
        return center;
    }

    public float GetRadius()
    {
        return m_collider.get_radius();
    }

    public Vector3 GetHeadPoint()
    {
        //IL_0006: Unknown result type (might be due to invalid IL or missing references)
        return m_head.get_position();
    }

    public Vector3 GetEyePoint()
    {
        //IL_0006: Unknown result type (might be due to invalid IL or missing references)
        return m_eye.get_position();
    }

    public Vector3 GetCenterPoint()
    {
        //IL_0006: Unknown result type (might be due to invalid IL or missing references)
        //IL_000b: Unknown result type (might be due to invalid IL or missing references)
        //IL_000e: Unknown result type (might be due to invalid IL or missing references)
        Bounds bounds = ((Collider)m_collider).get_bounds();
        return ((Bounds)(ref bounds)).get_center();
    }

    public DestructibleType GetDestructibleType()
    {
        return DestructibleType.Character;
    }

    public void Damage(HitData hit)
    {
        if (m_nview.IsValid())
        {
            m_nview.InvokeRPC("Damage", hit);
        }
    }

    public void RPC_Damage(long sender, HitData hit)
    {
        //IL_00a5: Unknown result type (might be due to invalid IL or missing references)
        //IL_011f: Unknown result type (might be due to invalid IL or missing references)
        //IL_0124: Unknown result type (might be due to invalid IL or missing references)
        //IL_015c: Unknown result type (might be due to invalid IL or missing references)
        //IL_0161: Unknown result type (might be due to invalid IL or missing references)
        if (IsDebugFlying() || !m_nview.IsOwner() || GetHealth() <= 0f || IsDead() || IsTeleporting() || InCutscene() || (hit.m_dodgeable && IsDodgeInvincible()))
        {
            return;
        }
        Character attacker = hit.GetAttacker();
        if ((hit.HaveAttacker() && (Object)(object)attacker == (Object)null) || (IsPlayer() && !IsPVPEnabled() && (Object)(object)attacker != (Object)null && attacker.IsPlayer()))
        {
            return;
        }
        if ((Object)(object)attacker != (Object)null && !attacker.IsPlayer())
        {
            float difficultyDamageScale = Game.instance.GetDifficultyDamageScale(((Component)this).get_transform().get_position());
            hit.ApplyModifier(difficultyDamageScale);
        }
        m_seman.OnDamaged(hit, attacker);
        if ((Object)(object)m_baseAI != (Object)null && !m_baseAI.IsAlerted() && hit.m_backstabBonus > 1f && Time.get_time() - m_backstabTime > 300f)
        {
            m_backstabTime = Time.get_time();
            hit.ApplyModifier(hit.m_backstabBonus);
            m_backstabHitEffects.Create(hit.m_point, Quaternion.get_identity(), ((Component)this).get_transform());
        }
        if (IsStaggering() && !IsPlayer())
        {
            hit.ApplyModifier(2f);
            m_critHitEffects.Create(hit.m_point, Quaternion.get_identity(), ((Component)this).get_transform());
        }
        if (hit.m_blockable && IsBlocking())
        {
            BlockAttack(hit, attacker);
        }
        ApplyPushback(hit);
        if (!string.IsNullOrEmpty(hit.m_statusEffect))
        {
            StatusEffect statusEffect = m_seman.GetStatusEffect(hit.m_statusEffect);
            if ((Object)(object)statusEffect == (Object)null)
            {
                statusEffect = m_seman.AddStatusEffect(hit.m_statusEffect);
            }
            if ((Object)(object)statusEffect != (Object)null && (Object)(object)attacker != (Object)null)
            {
                statusEffect.SetAttacker(attacker);
            }
        }
        HitData.DamageModifiers damageModifiers = GetDamageModifiers();
        hit.ApplyResistance(damageModifiers, out var significantModifier);
        if (IsPlayer())
        {
            float bodyArmor = GetBodyArmor();
            hit.ApplyArmor(bodyArmor);
            DamageArmorDurability(hit);
        }
        float poison = hit.m_damage.m_poison;
        float fire = hit.m_damage.m_fire;
        float spirit = hit.m_damage.m_spirit;
        hit.m_damage.m_poison = 0f;
        hit.m_damage.m_fire = 0f;
        hit.m_damage.m_spirit = 0f;
        ApplyDamage(hit, showDamageText: true, triggerEffects: true, significantModifier);
        AddFireDamage(fire);
        AddSpiritDamage(spirit);
        AddPoisonDamage(poison);
        AddFrostDamage(hit.m_damage.m_frost);
        AddLightningDamage(hit.m_damage.m_lightning);
    }

    public HitData.DamageModifier GetDamageModifier(HitData.DamageType damageType)
    {
        return GetDamageModifiers().GetModifier(damageType);
    }

    public HitData.DamageModifiers GetDamageModifiers()
    {
        HitData.DamageModifiers mods = m_damageModifiers.Clone();
        ApplyArmorDamageMods(ref mods);
        m_seman.ApplyDamageMods(ref mods);
        return mods;
    }

    public void ApplyDamage(HitData hit, bool showDamageText, bool triggerEffects, HitData.DamageModifier mod = HitData.DamageModifier.Normal)
    {
        //IL_0043: Unknown result type (might be due to invalid IL or missing references)
        //IL_0095: Unknown result type (might be due to invalid IL or missing references)
        //IL_00ca: Unknown result type (might be due to invalid IL or missing references)
        //IL_00cf: Unknown result type (might be due to invalid IL or missing references)
        if (IsDebugFlying() || IsDead() || IsTeleporting() || InCutscene())
        {
            return;
        }
        float totalDamage = hit.GetTotalDamage();
        if (showDamageText && (totalDamage > 0f || !IsPlayer()))
        {
            DamageText.instance.ShowText(mod, hit.m_point, totalDamage, IsPlayer());
        }
        if (totalDamage <= 0f)
        {
            return;
        }
        if (!InGodMode() && !InGhostMode())
        {
            float health = GetHealth();
            health -= totalDamage;
            SetHealth(health);
        }
        float totalPhysicalDamage = hit.m_damage.GetTotalPhysicalDamage();
        AddStaggerDamage(totalPhysicalDamage * hit.m_staggerMultiplier, hit.m_dir);
        if (triggerEffects && totalDamage > 2f)
        {
            DoDamageCameraShake(hit);
            if (hit.m_damage.GetTotalPhysicalDamage() > 0f)
            {
                m_hitEffects.Create(hit.m_point, Quaternion.get_identity(), ((Component)this).get_transform());
            }
        }
        OnDamaged(hit);
        if (m_onDamaged != null)
        {
            m_onDamaged(totalDamage, hit.GetAttacker());
        }
        if (m_dpsDebugEnabled)
        {
            AddDPS(totalDamage, this);
        }
    }

    public virtual void DoDamageCameraShake(HitData hit)
    {
    }

    public virtual void DamageArmorDurability(HitData hit)
    {
    }

    public void AddFireDamage(float damage)
    {
        if (!(damage <= 0f))
        {
            SE_Burning sE_Burning = m_seman.GetStatusEffect("Burning") as SE_Burning;
            if ((Object)(object)sE_Burning == (Object)null)
            {
                sE_Burning = m_seman.AddStatusEffect("Burning") as SE_Burning;
            }
            sE_Burning.AddFireDamage(damage);
        }
    }

    public void AddSpiritDamage(float damage)
    {
        if (!(damage <= 0f))
        {
            SE_Burning sE_Burning = m_seman.GetStatusEffect("Spirit") as SE_Burning;
            if ((Object)(object)sE_Burning == (Object)null)
            {
                sE_Burning = m_seman.AddStatusEffect("Spirit") as SE_Burning;
            }
            sE_Burning.AddSpiritDamage(damage);
        }
    }

    public void AddPoisonDamage(float damage)
    {
        if (!(damage <= 0f))
        {
            SE_Poison sE_Poison = m_seman.GetStatusEffect("Poison") as SE_Poison;
            if ((Object)(object)sE_Poison == (Object)null)
            {
                sE_Poison = m_seman.AddStatusEffect("Poison") as SE_Poison;
            }
            sE_Poison.AddDamage(damage);
        }
    }

    public void AddFrostDamage(float damage)
    {
        if (!(damage <= 0f))
        {
            SE_Frost sE_Frost = m_seman.GetStatusEffect("Frost") as SE_Frost;
            if ((Object)(object)sE_Frost == (Object)null)
            {
                sE_Frost = m_seman.AddStatusEffect("Frost") as SE_Frost;
            }
            sE_Frost.AddDamage(damage);
        }
    }

    public void AddLightningDamage(float damage)
    {
        if (!(damage <= 0f))
        {
            m_seman.AddStatusEffect("Lightning", resetTime: true);
        }
    }

    public void AddStaggerDamage(float damage, Vector3 forceDirection)
    {
        //IL_0065: Unknown result type (might be due to invalid IL or missing references)
        if (!(m_staggerDamageFactor <= 0f) || IsPlayer())
        {
            m_staggerDamage += damage;
            m_staggerTimer = 0f;
            float maxHealth = GetMaxHealth();
            float num = (IsPlayer() ? (maxHealth / 2f) : (maxHealth * m_staggerDamageFactor));
            if (m_staggerDamage >= num)
            {
                m_staggerDamage = 0f;
                Stagger(forceDirection);
            }
        }
    }

    public static void AddDPS(float damage, Character me)
    {
        if ((Object)(object)me == (Object)(object)Player.m_localPlayer)
        {
            CalculateDPS("To-you ", m_playerDamage, damage);
        }
        else
        {
            CalculateDPS("To-others ", m_enemyDamage, damage);
        }
    }

    public static void CalculateDPS(string name, List<KeyValuePair<float, float>> damages, float damage)
    {
        float time = Time.get_time();
        if (damages.Count > 0 && Time.get_time() - damages[damages.Count - 1].Key > 5f)
        {
            damages.Clear();
        }
        damages.Add(new KeyValuePair<float, float>(time, damage));
        float num = Time.get_time() - damages[0].Key;
        if (num < 0.01f)
        {
            return;
        }
        float num2 = 0f;
        foreach (KeyValuePair<float, float> damage2 in damages)
        {
            num2 += damage2.Value;
        }
        float num3 = num2 / num;
        string text = "DPS " + name + " (" + damages.Count + " attacks): " + num3.ToString("0.0");
        ZLog.Log((object)text);
        MessageHud.instance.ShowMessage(MessageHud.MessageType.Center, text);
    }

    public void UpdateStagger(float dt)
    {
        if (!(m_staggerDamageFactor <= 0f) || IsPlayer())
        {
            m_staggerTimer += dt;
            if (m_staggerTimer > 3f)
            {
                m_staggerDamage = 0f;
            }
        }
    }

    public void Stagger(Vector3 forceDirection)
    {
        //IL_0010: Unknown result type (might be due to invalid IL or missing references)
        //IL_002a: Unknown result type (might be due to invalid IL or missing references)
        if (m_nview.IsOwner())
        {
            RPC_Stagger(0L, forceDirection);
            return;
        }
        m_nview.InvokeRPC("Stagger", forceDirection);
    }

    public void RPC_Stagger(long sender, Vector3 forceDirection)
    {
        //IL_0028: Unknown result type (might be due to invalid IL or missing references)
        //IL_0029: Unknown result type (might be due to invalid IL or missing references)
        //IL_002e: Unknown result type (might be due to invalid IL or missing references)
        if (!IsStaggering())
        {
            if (((Vector3)(ref forceDirection)).get_magnitude() > 0.01f)
            {
                forceDirection.y = 0f;
                ((Component)this).get_transform().set_rotation(Quaternion.LookRotation(-forceDirection));
            }
            m_zanim.SetTrigger("stagger");
        }
    }

    public virtual void ApplyArmorDamageMods(ref HitData.DamageModifiers mods)
    {
    }

    public virtual float GetBodyArmor()
    {
        return 0f;
    }

    public virtual bool BlockAttack(HitData hit, Character attacker)
    {
        return false;
    }

    public virtual void OnDamaged(HitData hit)
    {
    }

    public void OnCollisionStay(Collision collision)
    {
        //IL_0039: Unknown result type (might be due to invalid IL or missing references)
        //IL_003e: Unknown result type (might be due to invalid IL or missing references)
        //IL_0041: Unknown result type (might be due to invalid IL or missing references)
        //IL_0051: Unknown result type (might be due to invalid IL or missing references)
        //IL_005f: Unknown result type (might be due to invalid IL or missing references)
        //IL_0086: Unknown result type (might be due to invalid IL or missing references)
        //IL_00af: Unknown result type (might be due to invalid IL or missing references)
        //IL_00b4: Unknown result type (might be due to invalid IL or missing references)
        //IL_00bc: Unknown result type (might be due to invalid IL or missing references)
        //IL_00c1: Unknown result type (might be due to invalid IL or missing references)
        //IL_00d5: Unknown result type (might be due to invalid IL or missing references)
        //IL_00dc: Unknown result type (might be due to invalid IL or missing references)
        //IL_00e1: Unknown result type (might be due to invalid IL or missing references)
        //IL_00e6: Unknown result type (might be due to invalid IL or missing references)
        //IL_00eb: Unknown result type (might be due to invalid IL or missing references)
        //IL_00ed: Unknown result type (might be due to invalid IL or missing references)
        //IL_0102: Unknown result type (might be due to invalid IL or missing references)
        //IL_0104: Unknown result type (might be due to invalid IL or missing references)
        //IL_010b: Unknown result type (might be due to invalid IL or missing references)
        //IL_0112: Unknown result type (might be due to invalid IL or missing references)
        //IL_0117: Unknown result type (might be due to invalid IL or missing references)
        //IL_0121: Unknown result type (might be due to invalid IL or missing references)
        //IL_0126: Unknown result type (might be due to invalid IL or missing references)
        if (!m_nview.IsValid() || !m_nview.IsOwner() || m_jumpTimer < 0.1f)
        {
            return;
        }
        ContactPoint[] contacts = collision.get_contacts();
        for (int i = 0; i < contacts.Length; i++)
        {
            ContactPoint val = contacts[i];
            float num = ((ContactPoint)(ref val)).get_point().y - ((Component)this).get_transform().get_position().y;
            if (!(((ContactPoint)(ref val)).get_normal().y > 0.1f) || !(num < m_collider.get_radius()))
            {
                continue;
            }
            if (((ContactPoint)(ref val)).get_normal().y > m_groundContactNormal.y || !m_groundContact)
            {
                m_groundContact = true;
                m_groundContactNormal = ((ContactPoint)(ref val)).get_normal();
                m_groundContactPoint = ((ContactPoint)(ref val)).get_point();
                m_lowestContactCollider = collision.get_collider();
                continue;
            }
            Vector3 val2 = Vector3.Normalize(m_groundContactNormal + ((ContactPoint)(ref val)).get_normal());
            if (val2.y > m_groundContactNormal.y)
            {
                m_groundContactNormal = val2;
                m_groundContactPoint = (m_groundContactPoint + ((ContactPoint)(ref val)).get_point()) * 0.5f;
            }
        }
    }

    public void UpdateGroundContact(float dt)
    {
        //IL_0017: Unknown result type (might be due to invalid IL or missing references)
        //IL_001c: Unknown result type (might be due to invalid IL or missing references)
        //IL_0023: Unknown result type (might be due to invalid IL or missing references)
        //IL_0028: Unknown result type (might be due to invalid IL or missing references)
        //IL_00a0: Unknown result type (might be due to invalid IL or missing references)
        //IL_00c2: Unknown result type (might be due to invalid IL or missing references)
        //IL_00c7: Unknown result type (might be due to invalid IL or missing references)
        //IL_00e4: Unknown result type (might be due to invalid IL or missing references)
        //IL_012f: Unknown result type (might be due to invalid IL or missing references)
        //IL_0134: Unknown result type (might be due to invalid IL or missing references)
        //IL_013b: Unknown result type (might be due to invalid IL or missing references)
        //IL_0140: Unknown result type (might be due to invalid IL or missing references)
        //IL_0164: Unknown result type (might be due to invalid IL or missing references)
        if (!m_groundContact)
        {
            return;
        }
        m_lastGroundCollider = m_lowestContactCollider;
        m_lastGroundNormal = m_groundContactNormal;
        m_lastGroundPoint = m_groundContactPoint;
        m_lastGroundBody = (Object.op_Implicit((Object)(object)m_lastGroundCollider) ? m_lastGroundCollider.get_attachedRigidbody() : null);
        if (!IsPlayer() && (Object)(object)m_lastGroundBody != (Object)null && ((Component)m_lastGroundBody).get_gameObject().get_layer() == ((Component)this).get_gameObject().get_layer())
        {
            m_lastGroundCollider = null;
            m_lastGroundBody = null;
        }
        float num = Mathf.Max(0f, m_maxAirAltitude - ((Component)this).get_transform().get_position().y);
        if (num > 0.8f)
        {
            if (m_onLand != null)
            {
                Vector3 lastGroundPoint = m_lastGroundPoint;
                if (InWater())
                {
                    lastGroundPoint.y = m_waterLevel;
                }
                m_onLand(m_lastGroundPoint);
            }
            ResetCloth();
        }
        if (IsPlayer() && num > 4f)
        {
            HitData hitData = new HitData();
            hitData.m_damage.m_damage = Mathf.Clamp01((num - 4f) / 16f) * 100f;
            hitData.m_point = m_lastGroundPoint;
            hitData.m_dir = m_lastGroundNormal;
            Damage(hitData);
        }
        ResetGroundContact();
        m_lastGroundTouch = 0f;
        m_maxAirAltitude = ((Component)this).get_transform().get_position().y;
    }

    public void ResetGroundContact()
    {
        //IL_000f: Unknown result type (might be due to invalid IL or missing references)
        //IL_0014: Unknown result type (might be due to invalid IL or missing references)
        //IL_001a: Unknown result type (might be due to invalid IL or missing references)
        //IL_001f: Unknown result type (might be due to invalid IL or missing references)
        m_lowestContactCollider = null;
        m_groundContact = false;
        m_groundContactNormal = Vector3.get_zero();
        m_groundContactPoint = Vector3.get_zero();
    }

    public Ship GetStandingOnShip()
    {
        if (!IsOnGround())
        {
            return null;
        }
        if (Object.op_Implicit((Object)(object)m_lastGroundBody))
        {
            return ((Component)m_lastGroundBody).GetComponent<Ship>();
        }
        return null;
    }

    public bool IsOnGround()
    {
        if (!(m_lastGroundTouch < 0.2f))
        {
            return m_body.IsSleeping();
        }
        return true;
    }

    public void CheckDeath()
    {
        if (!IsDead() && GetHealth() <= 0f)
        {
            OnDeath();
            if (m_onDeath != null)
            {
                m_onDeath();
            }
        }
    }

    public virtual void OnRagdollCreated(Ragdoll ragdoll)
    {
    }

    public virtual void OnDeath()
    {
        //IL_000c: Unknown result type (might be due to invalid IL or missing references)
        //IL_0017: Unknown result type (might be due to invalid IL or missing references)
        //IL_005d: Unknown result type (might be due to invalid IL or missing references)
        //IL_0062: Unknown result type (might be due to invalid IL or missing references)
        //IL_007f: Unknown result type (might be due to invalid IL or missing references)
        //IL_0089: Unknown result type (might be due to invalid IL or missing references)
        //IL_008e: Unknown result type (might be due to invalid IL or missing references)
        //IL_00bc: Unknown result type (might be due to invalid IL or missing references)
        GameObject[] array = m_deathEffects.Create(((Component)this).get_transform().get_position(), ((Component)this).get_transform().get_rotation(), ((Component)this).get_transform());
        for (int i = 0; i < array.Length; i++)
        {
            Ragdoll component = array[i].GetComponent<Ragdoll>();
            if (Object.op_Implicit((Object)(object)component))
            {
                CharacterDrop component2 = ((Component)this).GetComponent<CharacterDrop>();
                LevelEffects componentInChildren = ((Component)this).GetComponentInChildren<LevelEffects>();
                Vector3 velocity = m_body.get_velocity();
                if (((Vector3)(ref m_pushForce)).get_magnitude() * 0.5f > ((Vector3)(ref velocity)).get_magnitude())
                {
                    velocity = m_pushForce * 0.5f;
                }
                float hue = 0f;
                float saturation = 0f;
                float value = 0f;
                if (Object.op_Implicit((Object)(object)componentInChildren))
                {
                    componentInChildren.GetColorChanges(out hue, out saturation, out value);
                }
                component.Setup(velocity, hue, saturation, value, component2);
                OnRagdollCreated(component);
                if (Object.op_Implicit((Object)(object)component2))
                {
                    component2.SetDropsEnabled(enabled: false);
                }
            }
        }
        if (!string.IsNullOrEmpty(m_defeatSetGlobalKey))
        {
            ZoneSystem.instance.SetGlobalKey(m_defeatSetGlobalKey);
        }
        ZNetScene.instance.Destroy(((Component)this).get_gameObject());
        Gogan.LogEvent("Game", "Killed", m_name, 0L);
    }

    public float GetHealth()
    {
        return m_nview.GetZDO()?.GetFloat("health", GetMaxHealth()) ?? GetMaxHealth();
    }

    public void SetHealth(float health)
    {
        ZDO zDO = m_nview.GetZDO();
        if (zDO != null && m_nview.IsOwner())
        {
            if (health < 0f)
            {
                health = 0f;
            }
            zDO.Set("health", health);
        }
    }

    public float GetHealthPercentage()
    {
        return GetHealth() / GetMaxHealth();
    }

    public virtual bool IsDead()
    {
        return false;
    }

    public void SetMaxHealth(float health)
    {
        if (m_nview.GetZDO() != null)
        {
            m_nview.GetZDO().Set("max_health", health);
        }
        if (GetHealth() > health)
        {
            SetHealth(health);
        }
    }

    public float GetMaxHealth()
    {
        if (m_nview.GetZDO() != null)
        {
            return m_nview.GetZDO().GetFloat("max_health", m_health);
        }
        return m_health;
    }

    public virtual float GetMaxStamina()
    {
        return 0f;
    }

    public virtual float GetStaminaPercentage()
    {
        return 1f;
    }

    public bool IsBoss()
    {
        return m_boss;
    }

    public void SetLookDir(Vector3 dir)
    {
        //IL_0014: Unknown result type (might be due to invalid IL or missing references)
        //IL_0019: Unknown result type (might be due to invalid IL or missing references)
        //IL_0025: Unknown result type (might be due to invalid IL or missing references)
        //IL_0026: Unknown result type (might be due to invalid IL or missing references)
        //IL_0038: Unknown result type (might be due to invalid IL or missing references)
        //IL_0039: Unknown result type (might be due to invalid IL or missing references)
        //IL_003e: Unknown result type (might be due to invalid IL or missing references)
        if (((Vector3)(ref dir)).get_magnitude() <= Mathf.Epsilon)
        {
            dir = ((Component)this).get_transform().get_forward();
        }
        else
        {
            ((Vector3)(ref dir)).Normalize();
        }
        m_lookDir = dir;
        dir.y = 0f;
        m_lookYaw = Quaternion.LookRotation(dir);
    }

    public Vector3 GetLookDir()
    {
        //IL_0006: Unknown result type (might be due to invalid IL or missing references)
        return m_eye.get_forward();
    }

    public virtual void OnAttackTrigger()
    {
    }

    public virtual void OnStopMoving()
    {
    }

    public virtual void OnWeaponTrailStart()
    {
    }

    public void SetMoveDir(Vector3 dir)
    {
        //IL_0001: Unknown result type (might be due to invalid IL or missing references)
        //IL_0002: Unknown result type (might be due to invalid IL or missing references)
        m_moveDir = dir;
    }

    public void SetRun(bool run)
    {
        m_run = run;
    }

    public void SetWalk(bool walk)
    {
        m_walk = walk;
    }

    public bool GetWalk()
    {
        return m_walk;
    }

    public virtual void UpdateEyeRotation()
    {
        //IL_0007: Unknown result type (might be due to invalid IL or missing references)
        //IL_000c: Unknown result type (might be due to invalid IL or missing references)
        m_eye.set_rotation(Quaternion.LookRotation(m_lookDir));
    }

    public void OnAutoJump(Vector3 dir, float upVel, float forwardVel)
    {
        //IL_0064: Unknown result type (might be due to invalid IL or missing references)
        //IL_0069: Unknown result type (might be due to invalid IL or missing references)
        //IL_0077: Unknown result type (might be due to invalid IL or missing references)
        //IL_007c: Unknown result type (might be due to invalid IL or missing references)
        //IL_0085: Unknown result type (might be due to invalid IL or missing references)
        //IL_0086: Unknown result type (might be due to invalid IL or missing references)
        //IL_0088: Unknown result type (might be due to invalid IL or missing references)
        //IL_008d: Unknown result type (might be due to invalid IL or missing references)
        //IL_0092: Unknown result type (might be due to invalid IL or missing references)
        //IL_0099: Unknown result type (might be due to invalid IL or missing references)
        //IL_00c1: Unknown result type (might be due to invalid IL or missing references)
        //IL_00cc: Unknown result type (might be due to invalid IL or missing references)
        if (m_nview.IsValid() && m_nview.IsOwner() && IsOnGround() && !IsDead() && !InAttack() && !InDodge() && !IsKnockedBack() && !(Time.get_time() - m_lastAutoJumpTime < 0.5f))
        {
            m_lastAutoJumpTime = Time.get_time();
            if (!(Vector3.Dot(m_moveDir, dir) < 0.5f))
            {
                Vector3 val = Vector3.get_zero();
                val.y = upVel;
                val += dir * forwardVel;
                m_body.set_velocity(val);
                m_lastGroundTouch = 1f;
                m_jumpTimer = 0f;
                m_jumpEffects.Create(((Component)this).get_transform().get_position(), ((Component)this).get_transform().get_rotation(), ((Component)this).get_transform());
                SetCrouch(crouch: false);
                UpdateBodyFriction();
            }
        }
    }

    public void Jump()
    {
        //IL_009b: Unknown result type (might be due to invalid IL or missing references)
        //IL_00a0: Unknown result type (might be due to invalid IL or missing references)
        //IL_00b8: Unknown result type (might be due to invalid IL or missing references)
        //IL_00bd: Unknown result type (might be due to invalid IL or missing references)
        //IL_00c2: Unknown result type (might be due to invalid IL or missing references)
        //IL_00c7: Unknown result type (might be due to invalid IL or missing references)
        //IL_00cb: Unknown result type (might be due to invalid IL or missing references)
        //IL_00d0: Unknown result type (might be due to invalid IL or missing references)
        //IL_00ec: Unknown result type (might be due to invalid IL or missing references)
        //IL_00ee: Unknown result type (might be due to invalid IL or missing references)
        //IL_00fc: Unknown result type (might be due to invalid IL or missing references)
        //IL_00fd: Unknown result type (might be due to invalid IL or missing references)
        //IL_0104: Unknown result type (might be due to invalid IL or missing references)
        //IL_0109: Unknown result type (might be due to invalid IL or missing references)
        //IL_010e: Unknown result type (might be due to invalid IL or missing references)
        //IL_010f: Unknown result type (might be due to invalid IL or missing references)
        //IL_0111: Unknown result type (might be due to invalid IL or missing references)
        //IL_011c: Unknown result type (might be due to invalid IL or missing references)
        //IL_0123: Unknown result type (might be due to invalid IL or missing references)
        //IL_0128: Unknown result type (might be due to invalid IL or missing references)
        //IL_012d: Unknown result type (might be due to invalid IL or missing references)
        //IL_0131: Unknown result type (might be due to invalid IL or missing references)
        //IL_0138: Unknown result type (might be due to invalid IL or missing references)
        //IL_013d: Unknown result type (might be due to invalid IL or missing references)
        //IL_014f: Unknown result type (might be due to invalid IL or missing references)
        //IL_0198: Unknown result type (might be due to invalid IL or missing references)
        //IL_01a3: Unknown result type (might be due to invalid IL or missing references)
        if (!IsOnGround() || IsDead() || InAttack() || IsEncumbered() || InDodge() || IsKnockedBack())
        {
            return;
        }
        bool flag = false;
        if (!HaveStamina(m_jumpStaminaUsage))
        {
            if (IsPlayer())
            {
                Hud.instance.StaminaBarNoStaminaFlash();
            }
            flag = true;
        }
        float num = 0f;
        Skills skills = GetSkills();
        if ((Object)(object)skills != (Object)null)
        {
            num = skills.GetSkillFactor(Skills.SkillType.Jump);
            if (!flag)
            {
                RaiseSkill(Skills.SkillType.Jump);
            }
        }
        Vector3 val = m_body.get_velocity();
        Mathf.Acos(Mathf.Clamp01(m_lastGroundNormal.y));
        Vector3 val2 = m_lastGroundNormal + Vector3.get_up();
        Vector3 normalized = ((Vector3)(ref val2)).get_normalized();
        float num2 = 1f + num * 0.4f;
        float num3 = m_jumpForce * num2;
        float num4 = Vector3.Dot(normalized, val);
        if (num4 < num3)
        {
            val += normalized * (num3 - num4);
        }
        val += m_moveDir * m_jumpForceForward * num2;
        if (flag)
        {
            val *= m_jumpForceTiredFactor;
        }
        m_body.WakeUp();
        m_body.set_velocity(val);
        ResetGroundContact();
        m_lastGroundTouch = 1f;
        m_jumpTimer = 0f;
        m_zanim.SetTrigger("jump");
        AddNoise(30f);
        m_jumpEffects.Create(((Component)this).get_transform().get_position(), ((Component)this).get_transform().get_rotation(), ((Component)this).get_transform());
        OnJump();
        SetCrouch(crouch: false);
        UpdateBodyFriction();
    }

    public void UpdateBodyFriction()
    {
        ((Collider)m_collider).get_material().set_frictionCombine((PhysicMaterialCombine)1);
        if (IsDead())
        {
            ((Collider)m_collider).get_material().set_staticFriction(1f);
            ((Collider)m_collider).get_material().set_dynamicFriction(1f);
            ((Collider)m_collider).get_material().set_frictionCombine((PhysicMaterialCombine)3);
        }
        else if (IsSwiming())
        {
            ((Collider)m_collider).get_material().set_staticFriction(0.2f);
            ((Collider)m_collider).get_material().set_dynamicFriction(0.2f);
        }
        else if (!IsOnGround())
        {
            ((Collider)m_collider).get_material().set_staticFriction(0f);
            ((Collider)m_collider).get_material().set_dynamicFriction(0f);
        }
        else if (IsFlying())
        {
            ((Collider)m_collider).get_material().set_staticFriction(0f);
            ((Collider)m_collider).get_material().set_dynamicFriction(0f);
        }
        else if (((Vector3)(ref m_moveDir)).get_magnitude() < 0.1f)
        {
            ((Collider)m_collider).get_material().set_staticFriction(0.8f * (1f - m_slippage));
            ((Collider)m_collider).get_material().set_dynamicFriction(0.8f * (1f - m_slippage));
            ((Collider)m_collider).get_material().set_frictionCombine((PhysicMaterialCombine)3);
        }
        else
        {
            ((Collider)m_collider).get_material().set_staticFriction(0.4f * (1f - m_slippage));
            ((Collider)m_collider).get_material().set_dynamicFriction(0.4f * (1f - m_slippage));
        }
    }

    public virtual bool StartAttack(Character target, bool charge)
    {
        return false;
    }

    public virtual void OnNearFire(Vector3 point)
    {
    }

    public ZDOID GetZDOID()
    {
        if (m_nview.IsValid())
        {
            return m_nview.GetZDO().m_uid;
        }
        return ZDOID.None;
    }

    public bool IsOwner()
    {
        if (m_nview.IsValid())
        {
            return m_nview.IsOwner();
        }
        return false;
    }

    public long GetOwner()
    {
        if (m_nview.IsValid())
        {
            return m_nview.GetZDO().m_owner;
        }
        return 0L;
    }

    public virtual bool UseMeleeCamera()
    {
        return false;
    }

    public virtual bool AlwaysRotateCamera()
    {
        return true;
    }

    public void SetInWater(float depth)
    {
        m_waterLevel = depth;
    }

    public virtual bool IsPVPEnabled()
    {
        return false;
    }

    public virtual bool InIntro()
    {
        return false;
    }

    public virtual bool InCutscene()
    {
        return false;
    }

    public virtual bool IsCrouching()
    {
        return false;
    }

    public virtual bool InBed()
    {
        return false;
    }

    public virtual bool IsAttached()
    {
        return false;
    }

    public virtual void SetCrouch(bool crouch)
    {
    }

    public virtual void AttachStart(Transform attachPoint, bool hideWeapons, bool isBed, string attachAnimation, Vector3 detachOffset)
    {
    }

    public virtual void AttachStop()
    {
    }

    public void UpdateWater(float dt)
    {
        m_swimTimer += dt;
        if (InWaterSwimDepth())
        {
            if (m_nview.IsOwner())
            {
                m_seman.AddStatusEffect("Wet", resetTime: true);
            }
            if (m_canSwim)
            {
                m_swimTimer = 0f;
            }
        }
    }

    public bool IsSwiming()
    {
        return m_swimTimer < 0.5f;
    }

    public bool InWaterSwimDepth()
    {
        return InWaterDepth() > Mathf.Max(0f, m_swimDepth - 0.4f);
    }

    public float InWaterDepth()
    {
        //IL_0025: Unknown result type (might be due to invalid IL or missing references)
        if ((Object)(object)GetStandingOnShip() != (Object)null)
        {
            return 0f;
        }
        return Mathf.Max(0f, m_waterLevel - ((Component)this).get_transform().get_position().y);
    }

    public bool InWater()
    {
        return InWaterDepth() > 0f;
    }

    public virtual bool CheckRun(Vector3 moveDir, float dt)
    {
        if (!m_run)
        {
            return false;
        }
        if (((Vector3)(ref moveDir)).get_magnitude() < 0.1f)
        {
            return false;
        }
        if (IsCrouching() || IsEncumbered())
        {
            return false;
        }
        if (InDodge())
        {
            return false;
        }
        return true;
    }

    public bool IsRunning()
    {
        return m_running;
    }

    public bool IsWalking()
    {
        return m_walking;
    }

    public virtual bool InPlaceMode()
    {
        return false;
    }

    public virtual bool HaveStamina(float amount = 0f)
    {
        return true;
    }

    public virtual void AddStamina(float v)
    {
    }

    public virtual void UseStamina(float stamina)
    {
    }

    public bool IsStaggering()
    {
        //IL_0007: Unknown result type (might be due to invalid IL or missing references)
        //IL_000c: Unknown result type (might be due to invalid IL or missing references)
        AnimatorStateInfo currentAnimatorStateInfo = m_animator.GetCurrentAnimatorStateInfo(0);
        return ((AnimatorStateInfo)(ref currentAnimatorStateInfo)).get_tagHash() == m_animatorTagStagger;
    }

    public virtual bool CanMove()
    {
        //IL_0015: Unknown result type (might be due to invalid IL or missing references)
        //IL_0023: Unknown result type (might be due to invalid IL or missing references)
        //IL_0028: Unknown result type (might be due to invalid IL or missing references)
        AnimatorStateInfo val = (m_animator.IsInTransition(0) ? m_animator.GetNextAnimatorStateInfo(0) : m_animator.GetCurrentAnimatorStateInfo(0));
        if (((AnimatorStateInfo)(ref val)).get_tagHash() == m_animatorTagFreeze || ((AnimatorStateInfo)(ref val)).get_tagHash() == m_animatorTagStagger || ((AnimatorStateInfo)(ref val)).get_tagHash() == m_animatorTagSitting)
        {
            return false;
        }
        return true;
    }

    public virtual bool IsEncumbered()
    {
        return false;
    }

    public virtual bool IsTeleporting()
    {
        return false;
    }

    public bool CanWallRun()
    {
        return IsPlayer();
    }

    public void ShowPickupMessage(ItemDrop.ItemData item, int amount)
    {
        Message(MessageHud.MessageType.TopLeft, "$msg_added " + item.m_shared.m_name, amount, item.GetIcon());
    }

    public void ShowRemovedMessage(ItemDrop.ItemData item, int amount)
    {
        Message(MessageHud.MessageType.TopLeft, "$msg_removed " + item.m_shared.m_name, amount, item.GetIcon());
    }

    public virtual void Message(MessageHud.MessageType type, string msg, int amount = 0, Sprite icon = null)
    {
    }

    public CapsuleCollider GetCollider()
    {
        return m_collider;
    }

    public virtual void OnStealthSuccess(Character character, float factor)
    {
    }

    public virtual float GetStealthFactor()
    {
        return 1f;
    }

    public void UpdateNoise(float dt)
    {
        m_noiseRange = Mathf.Max(0f, m_noiseRange - dt * 4f);
        m_syncNoiseTimer += dt;
        if (m_syncNoiseTimer > 0.5f)
        {
            m_syncNoiseTimer = 0f;
            m_nview.GetZDO().Set("noise", m_noiseRange);
        }
    }

    public void AddNoise(float range)
    {
        if (m_nview.IsValid())
        {
            if (m_nview.IsOwner())
            {
                RPC_AddNoise(0L, range);
                return;
            }
            m_nview.InvokeRPC("AddNoise", range);
        }
    }

    public void RPC_AddNoise(long sender, float range)
    {
        if (m_nview.IsOwner() && range > m_noiseRange)
        {
            m_noiseRange = range;
            m_seman.ModifyNoise(m_noiseRange, ref m_noiseRange);
        }
    }

    public float GetNoiseRange()
    {
        if (!m_nview.IsValid())
        {
            return 0f;
        }
        if (m_nview.IsOwner())
        {
            return m_noiseRange;
        }
        return m_nview.GetZDO().GetFloat("noise");
    }

    public virtual bool InGodMode()
    {
        return false;
    }

    public virtual bool InGhostMode()
    {
        return false;
    }

    public virtual bool IsDebugFlying()
    {
        return false;
    }

    public virtual string GetHoverText()
    {
        Tameable component = ((Component)this).GetComponent<Tameable>();
        if (Object.op_Implicit((Object)(object)component))
        {
            return component.GetHoverText();
        }
        return "";
    }

    public virtual string GetHoverName()
    {
        return Localization.get_instance().Localize(m_name);
    }

    public virtual bool IsHoldingAttack()
    {
        return false;
    }

    public virtual bool InAttack()
    {
        return false;
    }

    public virtual void StopEmote()
    {
    }

    public virtual bool InMinorAction()
    {
        return false;
    }

    public virtual bool InDodge()
    {
        return false;
    }

    public virtual bool IsDodgeInvincible()
    {
        return false;
    }

    public virtual bool InEmote()
    {
        return false;
    }

    public virtual bool IsBlocking()
    {
        return false;
    }

    public bool IsFlying()
    {
        return m_flying;
    }

    public bool IsKnockedBack()
    {
        //IL_0001: Unknown result type (might be due to invalid IL or missing references)
        //IL_0006: Unknown result type (might be due to invalid IL or missing references)
        return m_pushForce != Vector3.get_zero();
    }

    public void OnDrawGizmosSelected()
    {
        //IL_003c: Unknown result type (might be due to invalid IL or missing references)
        //IL_0047: Unknown result type (might be due to invalid IL or missing references)
        //IL_0057: Unknown result type (might be due to invalid IL or missing references)
        //IL_005c: Unknown result type (might be due to invalid IL or missing references)
        //IL_0067: Unknown result type (might be due to invalid IL or missing references)
        //IL_006c: Unknown result type (might be due to invalid IL or missing references)
        //IL_0080: Unknown result type (might be due to invalid IL or missing references)
        //IL_0092: Unknown result type (might be due to invalid IL or missing references)
        //IL_009d: Unknown result type (might be due to invalid IL or missing references)
        //IL_00a3: Unknown result type (might be due to invalid IL or missing references)
        //IL_00a9: Unknown result type (might be due to invalid IL or missing references)
        //IL_00ae: Unknown result type (might be due to invalid IL or missing references)
        if ((Object)(object)m_nview != (Object)null && m_nview.GetZDO() != null)
        {
            float @float = m_nview.GetZDO().GetFloat("noise");
            Gizmos.DrawWireSphere(((Component)this).get_transform().get_position(), @float);
        }
        Gizmos.set_color(Color.get_blue());
        Gizmos.DrawWireCube(((Component)this).get_transform().get_position() + Vector3.get_up() * m_swimDepth, new Vector3(1f, 0.05f, 1f));
        if (IsOnGround())
        {
            Gizmos.set_color(Color.get_green());
            Gizmos.DrawLine(m_lastGroundPoint, m_lastGroundPoint + m_lastGroundNormal);
        }
    }

    public virtual bool TeleportTo(Vector3 pos, Quaternion rot, bool distantTeleport)
    {
        return false;
    }

    public void SyncVelocity()
    {
        //IL_0016: Unknown result type (might be due to invalid IL or missing references)
        m_nview.GetZDO().Set("BodyVelocity", m_body.get_velocity());
    }

    public Vector3 GetVelocity()
    {
        //IL_000d: Unknown result type (might be due to invalid IL or missing references)
        //IL_0026: Unknown result type (might be due to invalid IL or missing references)
        //IL_003c: Unknown result type (might be due to invalid IL or missing references)
        //IL_0041: Unknown result type (might be due to invalid IL or missing references)
        if (!m_nview.IsValid())
        {
            return Vector3.get_zero();
        }
        if (m_nview.IsOwner())
        {
            return m_body.get_velocity();
        }
        return m_nview.GetZDO().GetVec3("BodyVelocity", Vector3.get_zero());
    }

    public void AddRootMotion(Vector3 vel)
    {
        //IL_001a: Unknown result type (might be due to invalid IL or missing references)
        //IL_001f: Unknown result type (might be due to invalid IL or missing references)
        //IL_0020: Unknown result type (might be due to invalid IL or missing references)
        //IL_0025: Unknown result type (might be due to invalid IL or missing references)
        if (InDodge() || InAttack() || InEmote())
        {
            m_rootMotion += vel;
        }
    }

    public void ApplyRootMotion(ref Vector3 vel)
    {
        //IL_0001: Unknown result type (might be due to invalid IL or missing references)
        //IL_000b: Unknown result type (might be due to invalid IL or missing references)
        //IL_0010: Unknown result type (might be due to invalid IL or missing references)
        //IL_0021: Unknown result type (might be due to invalid IL or missing references)
        //IL_0022: Unknown result type (might be due to invalid IL or missing references)
        //IL_0028: Unknown result type (might be due to invalid IL or missing references)
        //IL_002d: Unknown result type (might be due to invalid IL or missing references)
        Vector3 val = m_rootMotion * 55f;
        if (((Vector3)(ref val)).get_magnitude() > ((Vector3)(ref vel)).get_magnitude())
        {
            vel = val;
        }
        m_rootMotion = Vector3.get_zero();
    }

    public static void GetCharactersInRange(Vector3 point, float radius, List<Character> characters)
    {
        //IL_001b: Unknown result type (might be due to invalid IL or missing references)
        //IL_0020: Unknown result type (might be due to invalid IL or missing references)
        foreach (Character character in m_characters)
        {
            if (Vector3.Distance(((Component)character).get_transform().get_position(), point) < radius)
            {
                characters.Add(character);
            }
        }
    }

    public static List<Character> GetAllCharacters()
    {
        return m_characters;
    }

    public static bool IsCharacterInRange(Vector3 point, float range)
    {
        //IL_0019: Unknown result type (might be due to invalid IL or missing references)
        //IL_001e: Unknown result type (might be due to invalid IL or missing references)
        foreach (Character character in m_characters)
        {
            if (Vector3.Distance(((Component)character).get_transform().get_position(), point) < range)
            {
                return true;
            }
        }
        return false;
    }

    public virtual void OnTargeted(bool sensed, bool alerted)
    {
    }

    public GameObject GetVisual()
    {
        return m_visual;
    }

    public void UpdateLodgroup()
    {
        if (!((Object)(object)m_lodGroup == (Object)null))
        {
            Renderer[] componentsInChildren = m_visual.GetComponentsInChildren<Renderer>();
            LOD[] lODs = m_lodGroup.GetLODs();
            lODs[0].renderers = componentsInChildren;
            m_lodGroup.SetLODs(lODs);
        }
    }

    public virtual float GetEquipmentMovementModifier()
    {
        return 0f;
    }

    public virtual float GetJogSpeedFactor()
    {
        return 1f;
    }

    public virtual float GetRunSpeedFactor()
    {
        return 1f;
    }

    public virtual float GetAttackSpeedFactorMovement()
    {
        return 1f;
    }

    public virtual float GetAttackSpeedFactorRotation()
    {
        return 1f;
    }

    public virtual void RaiseSkill(Skills.SkillType skill, float value = 1f)
    {
    }

    public virtual Skills GetSkills()
    {
        return null;
    }

    public virtual float GetSkillFactor(Skills.SkillType skill)
    {
        return 0f;
    }

    public virtual float GetRandomSkillFactor(Skills.SkillType skill)
    {
        return Random.Range(0.75f, 1f);
    }

    public bool IsMonsterFaction()
    {
        if (IsTamed())
        {
            return false;
        }
        if (m_faction != Faction.ForestMonsters && m_faction != Faction.Undead && m_faction != Faction.Demon && m_faction != Faction.PlainsMonsters && m_faction != Faction.MountainMonsters)
        {
            return m_faction == Faction.SeaMonsters;
        }
        return true;
    }

    public Transform GetTransform()
    {
        if ((Object)(object)this == (Object)null)
        {
            return null;
        }
        return ((Component)this).get_transform();
    }

    public Collider GetLastGroundCollider()
    {
        return m_lastGroundCollider;
    }

    public Vector3 GetLastGroundNormal()
    {
        //IL_0001: Unknown result type (might be due to invalid IL or missing references)
        return m_groundContactNormal;
    }

    public void ResetCloth()
    {
        m_nview.InvokeRPC(ZNetView.Everybody, "ResetCloth");
    }

    public void RPC_ResetCloth(long sender)
    {
        Cloth[] componentsInChildren = ((Component)this).GetComponentsInChildren<Cloth>();
        foreach (Cloth val in componentsInChildren)
        {
            if (val.get_enabled())
            {
                val.set_enabled(false);
                val.set_enabled(true);
            }
        }
    }

    public virtual bool GetRelativePosition(out ZDOID parent, out Vector3 relativePos, out Vector3 relativeVel)
    {
        //IL_0001: Unknown result type (might be due to invalid IL or missing references)
        //IL_0006: Unknown result type (might be due to invalid IL or missing references)
        //IL_005d: Unknown result type (might be due to invalid IL or missing references)
        //IL_0062: Unknown result type (might be due to invalid IL or missing references)
        //IL_0067: Unknown result type (might be due to invalid IL or missing references)
        //IL_0079: Unknown result type (might be due to invalid IL or missing references)
        //IL_0084: Unknown result type (might be due to invalid IL or missing references)
        //IL_0089: Unknown result type (might be due to invalid IL or missing references)
        //IL_008e: Unknown result type (might be due to invalid IL or missing references)
        //IL_0093: Unknown result type (might be due to invalid IL or missing references)
        //IL_00a6: Unknown result type (might be due to invalid IL or missing references)
        //IL_00ab: Unknown result type (might be due to invalid IL or missing references)
        relativeVel = Vector3.get_zero();
        if (IsOnGround() && Object.op_Implicit((Object)(object)m_lastGroundBody))
        {
            ZNetView component = ((Component)m_lastGroundBody).GetComponent<ZNetView>();
            if (Object.op_Implicit((Object)(object)component) && component.IsValid())
            {
                parent = component.GetZDO().m_uid;
                relativePos = ((Component)component).get_transform().InverseTransformPoint(((Component)this).get_transform().get_position());
                relativeVel = ((Component)component).get_transform().InverseTransformVector(m_body.get_velocity() - m_lastGroundBody.get_velocity());
                return true;
            }
        }
        parent = ZDOID.None;
        relativePos = Vector3.get_zero();
        return false;
    }

    public Quaternion GetLookYaw()
    {
        //IL_0001: Unknown result type (might be due to invalid IL or missing references)
        return m_lookYaw;
    }

    public Vector3 GetMoveDir()
    {
        //IL_0001: Unknown result type (might be due to invalid IL or missing references)
        return m_moveDir;
    }

    public BaseAI GetBaseAI()
    {
        return m_baseAI;
    }

    public float GetMass()
    {
        return m_body.get_mass();
    }

    public void SetVisible(bool visible)
    {
        //IL_002f: Unknown result type (might be due to invalid IL or missing references)
        //IL_004f: Unknown result type (might be due to invalid IL or missing references)
        if (!((Object)(object)m_lodGroup == (Object)null) && m_lodVisible != visible)
        {
            m_lodVisible = visible;
            if (m_lodVisible)
            {
                m_lodGroup.set_localReferencePoint(m_originalLocalRef);
            }
            else
            {
                m_lodGroup.set_localReferencePoint(new Vector3(999999f, 999999f, 999999f));
            }
        }
    }

    public void SetTamed(bool tamed)
    {
        if (m_nview.IsValid() && m_tamed != tamed)
        {
            m_nview.InvokeRPC("SetTamed", tamed);
        }
    }

    public void RPC_SetTamed(long sender, bool tamed)
    {
        if (m_nview.IsOwner() && m_tamed != tamed)
        {
            m_tamed = tamed;
            m_nview.GetZDO().Set("tamed", m_tamed);
        }
    }

    public bool IsTamed()
    {
        if (!m_nview.IsValid())
        {
            return false;
        }
        if (!m_nview.IsOwner() && Time.get_time() - m_lastTamedCheck > 1f)
        {
            m_lastTamedCheck = Time.get_time();
            m_tamed = m_nview.GetZDO().GetBool("tamed", m_tamed);
        }
        return m_tamed;
    }

    public SEMan GetSEMan()
    {
        return m_seman;
    }

    public bool InInterior()
    {
        //IL_0006: Unknown result type (might be due to invalid IL or missing references)
        return ((Component)this).get_transform().get_position().y > 3000f;
    }

    public static void SetDPSDebug(bool enabled)
    {
        m_dpsDebugEnabled = enabled;
    }

    public static bool IsDPSDebugEnabled()
    {
        return m_dpsDebugEnabled;
    }

    public Character()
        : this()
    {
    }
