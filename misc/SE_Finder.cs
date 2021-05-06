

// SE_Finder
using UnityEngine;

public class SE_Finder : StatusEffect
{
    [Header("SE_Finder")]
    public EffectList m_pingEffectNear = new EffectList();

    public EffectList m_pingEffectMed = new EffectList();

    public EffectList m_pingEffectFar = new EffectList();

    public float m_closerTriggerDistance = 2f;

    public float m_furtherTriggerDistance = 4f;

    public float m_closeFrequency = 1f;

    public float m_distantFrequency = 5f;

    public float m_updateBeaconTimer;

    public float m_pingTimer;

    public Beacon m_beacon;

    public float m_lastDistance;

    public override void UpdateStatusEffect(float dt)
    {
        //IL_0031: Unknown result type (might be due to invalid IL or missing references)
        //IL_006a: Unknown result type (might be due to invalid IL or missing references)
        //IL_007a: Unknown result type (might be due to invalid IL or missing references)
        //IL_00b0: Unknown result type (might be due to invalid IL or missing references)
        //IL_00c0: Unknown result type (might be due to invalid IL or missing references)
        //IL_012f: Unknown result type (might be due to invalid IL or missing references)
        //IL_013f: Unknown result type (might be due to invalid IL or missing references)
        //IL_0178: Unknown result type (might be due to invalid IL or missing references)
        //IL_0188: Unknown result type (might be due to invalid IL or missing references)
        //IL_01b6: Unknown result type (might be due to invalid IL or missing references)
        //IL_01c6: Unknown result type (might be due to invalid IL or missing references)
        m_updateBeaconTimer += dt;
        if (m_updateBeaconTimer > 1f)
        {
            m_updateBeaconTimer = 0f;
            Beacon beacon = Beacon.FindClosestBeaconInRange(((Component)m_character).get_transform().get_position());
            if ((Object)(object)beacon != (Object)(object)m_beacon)
            {
                m_beacon = beacon;
                if (Object.op_Implicit((Object)(object)m_beacon))
                {
                    m_lastDistance = Utils.DistanceXZ(((Component)m_character).get_transform().get_position(), ((Component)m_beacon).get_transform().get_position());
                    m_pingTimer = 0f;
                }
            }
        }
        if (!((Object)(object)m_beacon != (Object)null))
        {
            return;
        }
        float num = Utils.DistanceXZ(((Component)m_character).get_transform().get_position(), ((Component)m_beacon).get_transform().get_position());
        float num2 = Mathf.Clamp01(num / m_beacon.m_range);
        float num3 = Mathf.Lerp(m_closeFrequency, m_distantFrequency, num2);
        m_pingTimer += dt;
        if (m_pingTimer > num3)
        {
            m_pingTimer = 0f;
            if (num2 < 0.2f)
            {
                m_pingEffectNear.Create(((Component)m_character).get_transform().get_position(), ((Component)m_character).get_transform().get_rotation(), ((Component)m_character).get_transform());
            }
            else if (num2 < 0.6f)
            {
                m_pingEffectMed.Create(((Component)m_character).get_transform().get_position(), ((Component)m_character).get_transform().get_rotation(), ((Component)m_character).get_transform());
            }
            else
            {
                m_pingEffectFar.Create(((Component)m_character).get_transform().get_position(), ((Component)m_character).get_transform().get_rotation(), ((Component)m_character).get_transform());
            }
            m_lastDistance = num;
        }
    }
}
