

// SE_Harpooned
using UnityEngine;

public class SE_Harpooned : StatusEffect
{
    [Header("SE_Harpooned")]
    public float m_minForce = 2f;

    public float m_maxForce = 10f;

    public float m_minDistance = 6f;

    public float m_maxDistance = 30f;

    public float m_staminaDrain = 10f;

    public float m_staminaDrainInterval = 0.1f;

    public float m_maxMass = 50f;

    public bool m_broken;

    public Character m_attacker;

    public float m_drainStaminaTimer;

    public override void Setup(Character character)
    {
        base.Setup(character);
    }

    public override void SetAttacker(Character attacker)
    {
        //IL_0047: Unknown result type (might be due to invalid IL or missing references)
        //IL_0057: Unknown result type (might be due to invalid IL or missing references)
        ZLog.Log((object)("Setting attacker " + attacker.m_name));
        m_attacker = attacker;
        m_time = 0f;
        if (m_character.IsBoss())
        {
            m_broken = true;
            return;
        }
        if (Vector3.Distance(((Component)m_attacker).get_transform().get_position(), ((Component)m_character).get_transform().get_position()) > m_maxDistance)
        {
            m_attacker.Message(MessageHud.MessageType.Center, "Target too far");
            m_broken = true;
            return;
        }
        m_attacker.Message(MessageHud.MessageType.Center, m_character.m_name + " harpooned");
        GameObject[] startEffectInstances = m_startEffectInstances;
        foreach (GameObject val in startEffectInstances)
        {
            if (Object.op_Implicit((Object)(object)val))
            {
                LineConnect component = val.GetComponent<LineConnect>();
                if (Object.op_Implicit((Object)(object)component))
                {
                    component.SetPeer(((Component)m_attacker).GetComponent<ZNetView>());
                }
            }
        }
    }

    public override void UpdateStatusEffect(float dt)
    {
        //IL_0037: Unknown result type (might be due to invalid IL or missing references)
        //IL_0047: Unknown result type (might be due to invalid IL or missing references)
        //IL_004c: Unknown result type (might be due to invalid IL or missing references)
        //IL_0051: Unknown result type (might be due to invalid IL or missing references)
        //IL_0054: Unknown result type (might be due to invalid IL or missing references)
        //IL_0059: Unknown result type (might be due to invalid IL or missing references)
        //IL_006f: Unknown result type (might be due to invalid IL or missing references)
        //IL_0071: Unknown result type (might be due to invalid IL or missing references)
        //IL_0113: Unknown result type (might be due to invalid IL or missing references)
        //IL_0116: Unknown result type (might be due to invalid IL or missing references)
        base.UpdateStatusEffect(dt);
        if (!Object.op_Implicit((Object)(object)m_attacker))
        {
            return;
        }
        Rigidbody component = ((Component)m_character).GetComponent<Rigidbody>();
        if (!Object.op_Implicit((Object)(object)component))
        {
            return;
        }
        Vector3 val = ((Component)m_attacker).get_transform().get_position() - ((Component)m_character).get_transform().get_position();
        Vector3 normalized = ((Vector3)(ref val)).get_normalized();
        float radius = m_character.GetRadius();
        float magnitude = ((Vector3)(ref val)).get_magnitude();
        float num = Mathf.Clamp01(Vector3.Dot(normalized, component.get_velocity()));
        float num2 = Utils.LerpStep(m_minDistance, m_maxDistance, magnitude);
        float num3 = Mathf.Lerp(m_minForce, m_maxForce, num2);
        float num4 = Mathf.Clamp01(m_maxMass / component.get_mass());
        float num5 = num3 * num4;
        if (magnitude - radius > m_minDistance && num < num5)
        {
            normalized.y = 0f;
            ((Vector3)(ref normalized)).Normalize();
            if ((Object)(object)m_character.GetStandingOnShip() == (Object)null && !m_character.IsAttached())
            {
                component.AddForce(normalized * num5, (ForceMode)2);
            }
            m_drainStaminaTimer += dt;
            if (m_drainStaminaTimer > m_staminaDrainInterval)
            {
                m_drainStaminaTimer = 0f;
                float num6 = 1f - Mathf.Clamp01(num / num3);
                m_attacker.UseStamina(m_staminaDrain * num6);
            }
        }
        if (magnitude > m_maxDistance)
        {
            m_broken = true;
            m_attacker.Message(MessageHud.MessageType.Center, "Line broke");
        }
        if (!m_attacker.HaveStamina())
        {
            m_broken = true;
            m_attacker.Message(MessageHud.MessageType.Center, m_character.m_name + " escaped");
        }
    }

    public override bool IsDone()
    {
        if (base.IsDone())
        {
            return true;
        }
        if (m_broken)
        {
            return true;
        }
        if (!Object.op_Implicit((Object)(object)m_attacker))
        {
            return true;
        }
        if (m_time > 2f && (m_attacker.IsBlocking() || m_attacker.InAttack()))
        {
            m_attacker.Message(MessageHud.MessageType.Center, m_character.m_name + " released");
            return true;
        }
        return false;
    }
}
