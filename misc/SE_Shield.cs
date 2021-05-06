

// SE_Shield
using UnityEngine;

public class SE_Shield : StatusEffect
{
    [Header("__SE_Shield__")]
    public float m_absorbDamage = 100f;

    public EffectList m_breakEffects = new EffectList();

    public EffectList m_hitEffects = new EffectList();

    public float m_damage;

    public override void Setup(Character character)
    {
        base.Setup(character);
    }

    public override bool IsDone()
    {
        //IL_001a: Unknown result type (might be due to invalid IL or missing references)
        //IL_002a: Unknown result type (might be due to invalid IL or missing references)
        if (m_damage > m_absorbDamage)
        {
            m_breakEffects.Create(m_character.GetCenterPoint(), ((Component)m_character).get_transform().get_rotation(), ((Component)m_character).get_transform(), m_character.GetRadius() * 2f);
            return true;
        }
        return base.IsDone();
    }

    public override void OnDamaged(HitData hit, Character attacker)
    {
        //IL_0027: Unknown result type (might be due to invalid IL or missing references)
        //IL_002c: Unknown result type (might be due to invalid IL or missing references)
        float totalDamage = hit.GetTotalDamage();
        m_damage += totalDamage;
        hit.ApplyModifier(0f);
        m_hitEffects.Create(hit.m_point, Quaternion.get_identity());
    }
}
