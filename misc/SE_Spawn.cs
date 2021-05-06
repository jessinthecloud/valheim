

// SE_Spawn
using UnityEngine;

public class SE_Spawn : StatusEffect
{
    [Header("__SE_Spawn__")]
    public float m_delay = 10f;

    public GameObject m_prefab;

    public Vector3 m_spawnOffset = new Vector3(0f, 0f, 0f);

    public EffectList m_spawnEffect = new EffectList();

    public bool m_spawned;

    public override void UpdateStatusEffect(float dt)
    {
        //IL_0031: Unknown result type (might be due to invalid IL or missing references)
        //IL_0036: Unknown result type (might be due to invalid IL or missing references)
        //IL_003b: Unknown result type (might be due to invalid IL or missing references)
        //IL_0042: Unknown result type (might be due to invalid IL or missing references)
        //IL_0043: Unknown result type (might be due to invalid IL or missing references)
        //IL_0064: Unknown result type (might be due to invalid IL or missing references)
        //IL_0081: Unknown result type (might be due to invalid IL or missing references)
        //IL_008c: Unknown result type (might be due to invalid IL or missing references)
        base.UpdateStatusEffect(dt);
        if (!m_spawned && m_time > m_delay)
        {
            m_spawned = true;
            Vector3 val = ((Component)m_character).get_transform().TransformVector(m_spawnOffset);
            GameObject val2 = Object.Instantiate<GameObject>(m_prefab, val, Quaternion.get_identity());
            Projectile component = val2.GetComponent<Projectile>();
            if (Object.op_Implicit((Object)(object)component))
            {
                component.Setup(m_character, Vector3.get_zero(), -1f, null, null);
            }
            m_spawnEffect.Create(val2.get_transform().get_position(), val2.get_transform().get_rotation());
        }
    }
}
