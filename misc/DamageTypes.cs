// HitData.DamageTypes
using System;
using UnityEngine;

[Serializable]
public struct DamageTypes
{
    public float m_damage;

    public float m_blunt;

    public float m_slash;

    public float m_pierce;

    public float m_chop;

    public float m_pickaxe;

    public float m_fire;

    public float m_frost;

    public float m_lightning;

    public float m_poison;

    public float m_spirit;

    public bool HaveDamage()
    {
        if (!(m_damage > 0f) && !(m_blunt > 0f) && !(m_slash > 0f) && !(m_pierce > 0f) && !(m_chop > 0f) && !(m_pickaxe > 0f) && !(m_fire > 0f) && !(m_frost > 0f) && !(m_lightning > 0f) && !(m_poison > 0f))
        {
            return m_spirit > 0f;
        }
        return true;
    }

    public float GetTotalPhysicalDamage()
    {
        return m_blunt + m_slash + m_pierce;
    }

    public float GetTotalElementalDamage()
    {
        return m_fire + m_frost + m_lightning;
    }

    public float GetTotalDamage()
    {
        return m_damage + m_blunt + m_slash + m_pierce + m_chop + m_pickaxe + m_fire + m_frost + m_lightning + m_poison + m_spirit;
    }

    public DamageTypes Clone()
    {
        return (DamageTypes)MemberwiseClone();
    }

    public void Add(DamageTypes other, int multiplier = 1)
    {
        m_damage += other.m_damage * (float)multiplier;
        m_blunt += other.m_blunt * (float)multiplier;
        m_slash += other.m_slash * (float)multiplier;
        m_pierce += other.m_pierce * (float)multiplier;
        m_chop += other.m_chop * (float)multiplier;
        m_pickaxe += other.m_pickaxe * (float)multiplier;
        m_fire += other.m_fire * (float)multiplier;
        m_frost += other.m_frost * (float)multiplier;
        m_lightning += other.m_lightning * (float)multiplier;
        m_poison += other.m_poison * (float)multiplier;
        m_spirit += other.m_spirit * (float)multiplier;
    }

    public void Modify(float multiplier)
    {
        m_damage *= multiplier;
        m_blunt *= multiplier;
        m_slash *= multiplier;
        m_pierce *= multiplier;
        m_chop *= multiplier;
        m_pickaxe *= multiplier;
        m_fire *= multiplier;
        m_frost *= multiplier;
        m_lightning *= multiplier;
        m_poison *= multiplier;
        m_spirit *= multiplier;
    }

    private float ApplyArmor(float dmg, float ac)
    {
        float result = Mathf.Clamp01(dmg / (ac * 4f)) * dmg;
        if (ac < dmg / 2f)
        {
            result = dmg - ac;
        }
        return result;
    }

    public void ApplyArmor(float ac)
    {
        if (!(ac <= 0f))
        {
            float num = m_blunt + m_chop + m_pickaxe + m_slash + m_pierce + m_fire + m_frost + m_lightning + m_spirit;
            if (!(num <= 0f))
            {
                float num2 = ApplyArmor(num, ac) / num;
                m_blunt *= num2;
                m_chop *= num2;
                m_pickaxe *= num2;
                m_slash *= num2;
                m_pierce *= num2;
                m_fire *= num2;
                m_frost *= num2;
                m_lightning *= num2;
                m_spirit *= num2;
            }
        }
    }

    private string DamageRange(float damage, float minFactor, float maxFactor)
    {
        int num = Mathf.RoundToInt(damage * minFactor);
        int num2 = Mathf.RoundToInt(damage * maxFactor);
        return "<color=orange>" + Mathf.RoundToInt(damage) + "</color> <color=yellow>(" + num.ToString() + "-" + num2.ToString() + ") </color>";
    }

    public string GetTooltipString(Skills.SkillType skillType = Skills.SkillType.None)
    {
        if (Player.m_localPlayer == null)
        {
            return "";
        }
        Player.m_localPlayer.GetSkills().GetRandomSkillRange(out var min, out var max, skillType);
        string text = "";
        if (m_damage != 0f)
        {
            text = text + "\n$inventory_damage: " + DamageRange(m_damage, min, max);
        }
        if (m_blunt != 0f)
        {
            text = text + "\n$inventory_blunt: " + DamageRange(m_blunt, min, max);
        }
        if (m_slash != 0f)
        {
            text = text + "\n$inventory_slash: " + DamageRange(m_slash, min, max);
        }
        if (m_pierce != 0f)
        {
            text = text + "\n$inventory_pierce: " + DamageRange(m_pierce, min, max);
        }
        if (m_fire != 0f)
        {
            text = text + "\n$inventory_fire: " + DamageRange(m_fire, min, max);
        }
        if (m_frost != 0f)
        {
            text = text + "\n$inventory_frost: " + DamageRange(m_frost, min, max);
        }
        if (m_lightning != 0f)
        {
            text = text + "\n$inventory_lightning: " + DamageRange(m_lightning, min, max);
        }
        if (m_poison != 0f)
        {
            text = text + "\n$inventory_poison: " + DamageRange(m_poison, min, max);
        }
        if (m_spirit != 0f)
        {
            text = text + "\n$inventory_spirit: " + DamageRange(m_spirit, min, max);
        }
        return text;
    }

    public string GetTooltipString()
    {
        string text = "";
        if (m_damage != 0f)
        {
            text = text + "\n$inventory_damage: <color=yellow>" + m_damage + "</color>";
        }
        if (m_blunt != 0f)
        {
            text = text + "\n$inventory_blunt: <color=yellow>" + m_blunt + "</color>";
        }
        if (m_slash != 0f)
        {
            text = text + "\n$inventory_slash: <color=yellow>" + m_slash + "</color>";
        }
        if (m_pierce != 0f)
        {
            text = text + "\n$inventory_pierce: <color=yellow>" + m_pierce + "</color>";
        }
        if (m_fire != 0f)
        {
            text = text + "\n$inventory_fire: <color=yellow>" + m_fire + "</color>";
        }
        if (m_frost != 0f)
        {
            text = text + "\n$inventory_frost: <color=yellow>" + m_frost + "</color>";
        }
        if (m_lightning != 0f)
        {
            text = text + "\n$inventory_lightning: <color=yellow>" + m_frost + "</color>";
        }
        if (m_poison != 0f)
        {
            text = text + "\n$inventory_poison: <color=yellow>" + m_poison + "</color>";
        }
        if (m_spirit != 0f)
        {
            text = text + "\n$inventory_spirit: <color=yellow>" + m_spirit + "</color>";
        }
        return text;
    }
}
