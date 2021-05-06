

// SE_Smoke
using UnityEngine;

public class SE_Smoke : StatusEffect
{
    [Header("SE_Burning")]
    public HitData.DamageTypes m_damage;

    public float m_damageInterval = 1f;

    public float m_timer;

    public override bool CanAdd(Character character)
    {
        if (character.m_tolerateSmoke)
        {
            return false;
        }
        return base.CanAdd(character);
    }

    public override void UpdateStatusEffect(float dt)
    {
        //IL_003b: Unknown result type (might be due to invalid IL or missing references)
        //IL_0040: Unknown result type (might be due to invalid IL or missing references)
        base.UpdateStatusEffect(dt);
        m_timer += dt;
        if (m_timer > m_damageInterval)
        {
            m_timer = 0f;
            HitData hitData = new HitData();
            hitData.m_point = m_character.GetCenterPoint();
            hitData.m_damage = m_damage;
            m_character.ApplyDamage(hitData, showDamageText: true, triggerEffects: false);
        }
    }
}
