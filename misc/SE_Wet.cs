

// SE_Wet
using UnityEngine;

public class SE_Wet : SE_Stats
{
    [Header("__SE_Wet__")]
    public float m_waterDamage;

    public float m_damageInterval = 0.5f;

    public float m_timer;

    public override void Setup(Character character)
    {
        base.Setup(character);
    }

    public override void UpdateStatusEffect(float dt)
    {
        //IL_004d: Unknown result type (might be due to invalid IL or missing references)
        //IL_0052: Unknown result type (might be due to invalid IL or missing references)
        base.UpdateStatusEffect(dt);
        if (!m_character.m_tolerateWater)
        {
            m_timer += dt;
            if (m_timer > m_damageInterval)
            {
                m_timer = 0f;
                HitData hitData = new HitData();
                hitData.m_point = ((Component)m_character).get_transform().get_position();
                hitData.m_damage.m_damage = m_waterDamage;
                m_character.Damage(hitData);
            }
        }
        if (m_character.GetSEMan().HaveStatusEffect("CampFire"))
        {
            m_time += dt * 10f;
        }
        if (m_character.GetSEMan().HaveStatusEffect("Burning"))
        {
            m_time += dt * 50f;
        }
    }
}
