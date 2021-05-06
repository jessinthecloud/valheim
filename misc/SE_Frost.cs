

// SE_Frost
using UnityEngine;

public class SE_Frost : StatusEffect
{
    [Header("SE_Frost")]
    public float m_freezeTimeEnemy = 10f;

    public float m_freezeTimePlayer = 10f;

    public float m_minSpeedFactor = 0.1f;

    public override void UpdateStatusEffect(float dt)
    {
        base.UpdateStatusEffect(dt);
    }

    public void AddDamage(float damage)
    {
        float num = (m_character.IsPlayer() ? m_freezeTimePlayer : m_freezeTimeEnemy);
        float num2 = Mathf.Clamp01(damage / m_character.GetMaxHealth()) * num;
        float num3 = m_ttl - m_time;
        if (num2 > num3)
        {
            m_ttl = num2;
            ResetTime();
            TriggerStartEffects();
        }
    }

    public override void ModifySpeed(ref float speed)
    {
        float num = Mathf.Clamp01(m_time / m_ttl);
        num = Mathf.Pow(num, 2f);
        speed *= Mathf.Clamp(num, m_minSpeedFactor, 1f);
    }
}
