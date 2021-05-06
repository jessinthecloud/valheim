

// CookingStation
using System;
using System.Collections.Generic;
using UnityEngine;
using UnityEngine.Rendering;

public class CookingStation : MonoBehaviour, Interactable, Hoverable
{
    [Serializable]
    public class ItemConversion
    {
        public ItemDrop m_from;

        public ItemDrop m_to;

        public float m_cookTime = 10f;
    }

    public const float cookDelta = 1f;

    public EffectList m_addEffect = new EffectList();

    public EffectList m_doneEffect = new EffectList();

    public EffectList m_overcookedEffect = new EffectList();

    public EffectList m_pickEffector = new EffectList();

    public float m_spawnOffset = 0.5f;

    public float m_spawnForce = 5f;

    public ItemDrop m_overCookedItem;

    public List<ItemConversion> m_conversion = new List<ItemConversion>();

    public Transform[] m_slots;

    public string m_name = "";

    public ZNetView m_nview;

    public ParticleSystem[] m_ps;

    public AudioSource[] m_as;

    public void Awake()
    {
        m_nview = ((Component)this).get_gameObject().GetComponent<ZNetView>();
        if (m_nview.GetZDO() != null)
        {
            m_ps = (ParticleSystem[])(object)new ParticleSystem[m_slots.Length];
            m_as = (AudioSource[])(object)new AudioSource[m_slots.Length];
            for (int i = 0; i < m_slots.Length; i++)
            {
                m_ps[i] = ((Component)m_slots[i]).GetComponentInChildren<ParticleSystem>();
                m_as[i] = ((Component)m_slots[i]).GetComponentInChildren<AudioSource>();
            }
            m_nview.Register("RemoveDoneItem", RPC_RemoveDoneItem);
            m_nview.Register<string>("AddItem", RPC_AddItem);
            m_nview.Register<int, string>("SetSlotVisual", RPC_SetSlotVisual);
            ((MonoBehaviour)this).InvokeRepeating("UpdateCooking", 0f, 1f);
        }
    }

    public void UpdateCooking()
    {
        //IL_00a7: Unknown result type (might be due to invalid IL or missing references)
        //IL_00ac: Unknown result type (might be due to invalid IL or missing references)
        //IL_00fc: Unknown result type (might be due to invalid IL or missing references)
        //IL_0101: Unknown result type (might be due to invalid IL or missing references)
        if (!m_nview.IsValid())
        {
            return;
        }
        if (m_nview.IsOwner() && IsFireLit())
        {
            for (int i = 0; i < m_slots.Length; i++)
            {
                GetSlot(i, out var itemName, out var cookedTime);
                if (!(itemName != "") || !(itemName != ((Object)m_overCookedItem).get_name()))
                {
                    continue;
                }
                ItemConversion itemConversion = GetItemConversion(itemName);
                if (itemName == null)
                {
                    SetSlot(i, "", 0f);
                    continue;
                }
                cookedTime += 1f;
                if (cookedTime > itemConversion.m_cookTime * 2f)
                {
                    m_overcookedEffect.Create(m_slots[i].get_position(), Quaternion.get_identity());
                    SetSlot(i, ((Object)m_overCookedItem).get_name(), cookedTime);
                }
                else if (cookedTime > itemConversion.m_cookTime && itemName == ((Object)itemConversion.m_from).get_name())
                {
                    m_doneEffect.Create(m_slots[i].get_position(), Quaternion.get_identity());
                    SetSlot(i, ((Object)itemConversion.m_to).get_name(), cookedTime);
                }
                else
                {
                    SetSlot(i, itemName, cookedTime);
                }
            }
        }
        UpdateVisual();
    }

    public void UpdateVisual()
    {
        for (int i = 0; i < m_slots.Length; i++)
        {
            GetSlot(i, out var itemName, out var _);
            SetSlotVisual(i, itemName);
        }
    }

    public void RPC_SetSlotVisual(long sender, int slot, string item)
    {
        SetSlotVisual(slot, item);
    }

    public void SetSlotVisual(int i, string item)
    {
        //IL_0015: Unknown result type (might be due to invalid IL or missing references)
        //IL_001a: Unknown result type (might be due to invalid IL or missing references)
        //IL_0065: Unknown result type (might be due to invalid IL or missing references)
        //IL_006a: Unknown result type (might be due to invalid IL or missing references)
        //IL_00ff: Unknown result type (might be due to invalid IL or missing references)
        //IL_0105: Unknown result type (might be due to invalid IL or missing references)
        if (item == "")
        {
            EmissionModule emission = m_ps[i].get_emission();
            ((EmissionModule)(ref emission)).set_enabled(false);
            m_as[i].set_mute(true);
            if (m_slots[i].get_childCount() > 0)
            {
                Object.Destroy((Object)(object)((Component)m_slots[i].GetChild(0)).get_gameObject());
            }
            return;
        }
        EmissionModule emission2 = m_ps[i].get_emission();
        ((EmissionModule)(ref emission2)).set_enabled(true);
        m_as[i].set_mute(false);
        if (m_slots[i].get_childCount() == 0 || ((Object)m_slots[i].GetChild(0)).get_name() != item)
        {
            if (m_slots[i].get_childCount() > 0)
            {
                Object.Destroy((Object)(object)((Component)m_slots[i].GetChild(0)).get_gameObject());
            }
            Transform obj = ObjectDB.instance.GetItemPrefab(item).get_transform().Find("attach");
            Transform val = m_slots[i];
            GameObject obj2 = Object.Instantiate<GameObject>(((Component)obj).get_gameObject(), val.get_position(), val.get_rotation(), val);
            ((Object)obj2).set_name(item);
            Renderer[] componentsInChildren = obj2.GetComponentsInChildren<Renderer>();
            for (int j = 0; j < componentsInChildren.Length; j++)
            {
                componentsInChildren[j].set_shadowCastingMode((ShadowCastingMode)0);
            }
        }
    }

    public void RPC_RemoveDoneItem(long sender)
    {
        for (int i = 0; i < m_slots.Length; i++)
        {
            GetSlot(i, out var itemName, out var _);
            if (itemName != "" && IsItemDone(itemName))
            {
                SpawnItem(itemName);
                SetSlot(i, "", 0f);
                m_nview.InvokeRPC(ZNetView.Everybody, "SetSlotVisual", i, "");
                break;
            }
        }
    }

    public bool HaveDoneItem()
    {
        for (int i = 0; i < m_slots.Length; i++)
        {
            GetSlot(i, out var itemName, out var _);
            if (itemName != "" && IsItemDone(itemName))
            {
                return true;
            }
        }
        return false;
    }

    public bool IsItemDone(string itemName)
    {
        if (itemName == ((Object)m_overCookedItem).get_name())
        {
            return true;
        }
        ItemConversion itemConversion = GetItemConversion(itemName);
        if (itemConversion == null)
        {
            return false;
        }
        if (itemName == ((Object)itemConversion.m_to).get_name())
        {
            return true;
        }
        return false;
    }

    public void SpawnItem(string name)
    {
        //IL_0011: Unknown result type (might be due to invalid IL or missing references)
        //IL_0016: Unknown result type (might be due to invalid IL or missing references)
        //IL_0021: Unknown result type (might be due to invalid IL or missing references)
        //IL_0026: Unknown result type (might be due to invalid IL or missing references)
        //IL_002b: Unknown result type (might be due to invalid IL or missing references)
        //IL_0042: Unknown result type (might be due to invalid IL or missing references)
        //IL_0047: Unknown result type (might be due to invalid IL or missing references)
        //IL_0048: Unknown result type (might be due to invalid IL or missing references)
        //IL_0049: Unknown result type (might be due to invalid IL or missing references)
        //IL_0054: Unknown result type (might be due to invalid IL or missing references)
        //IL_005f: Unknown result type (might be due to invalid IL or missing references)
        //IL_006f: Unknown result type (might be due to invalid IL or missing references)
        //IL_0070: Unknown result type (might be due to invalid IL or missing references)
        GameObject itemPrefab = ObjectDB.instance.GetItemPrefab(name);
        Vector3 val = ((Component)this).get_transform().get_position() + Vector3.get_up() * m_spawnOffset;
        Quaternion val2 = Quaternion.Euler(0f, (float)Random.Range(0, 360), 0f);
        Object.Instantiate<GameObject>(itemPrefab, val, val2).GetComponent<Rigidbody>().set_velocity(Vector3.get_up() * m_spawnForce);
        m_pickEffector.Create(val, Quaternion.get_identity());
    }

    public string GetHoverText()
    {
        return Localization.get_instance().Localize(m_name + "\n[<color=yellow><b>$KEY_Use</b></color>] $piece_cstand_cook\n[<color=yellow><b>1-8</b></color>] $piece_cstand_cook");
    }

    public string GetHoverName()
    {
        return m_name;
    }

    public bool Interact(Humanoid user, bool hold)
    {
        if (hold)
        {
            return false;
        }
        if (HaveDoneItem())
        {
            m_nview.InvokeRPC("RemoveDoneItem");
            return true;
        }
        ItemDrop.ItemData itemData = FindCookableItem(user.GetInventory());
        if (itemData == null)
        {
            user.Message(MessageHud.MessageType.Center, "$msg_nocookitems");
            return false;
        }
        UseItem(user, itemData);
        return true;
    }

    public bool UseItem(Humanoid user, ItemDrop.ItemData item)
    {
        if (!IsFireLit())
        {
            user.Message(MessageHud.MessageType.Center, "$msg_needfire");
            return false;
        }
        if (GetFreeSlot() == -1)
        {
            user.Message(MessageHud.MessageType.Center, "$msg_nocookroom");
            return false;
        }
        return CookItem(user.GetInventory(), item);
    }

    public bool IsFireLit()
    {
        //IL_0006: Unknown result type (might be due to invalid IL or missing references)
        if (Object.op_Implicit((Object)(object)EffectArea.IsPointInsideArea(((Component)this).get_transform().get_position(), EffectArea.Type.Burning, 0.25f)))
        {
            return true;
        }
        return false;
    }

    public ItemDrop.ItemData FindCookableItem(Inventory inventory)
    {
        foreach (ItemConversion item2 in m_conversion)
        {
            ItemDrop.ItemData item = inventory.GetItem(item2.m_from.m_itemData.m_shared.m_name);
            if (item != null)
            {
                return item;
            }
        }
        return null;
    }

    public bool CookItem(Inventory inventory, ItemDrop.ItemData item)
    {
        string name = ((Object)item.m_dropPrefab).get_name();
        if (!m_nview.HasOwner())
        {
            m_nview.ClaimOwnership();
        }
        if (!IsItemAllowed(item))
        {
            return false;
        }
        if (GetFreeSlot() == -1)
        {
            return false;
        }
        inventory.RemoveOneItem(item);
        m_nview.InvokeRPC("AddItem", name);
        return true;
    }

    public void RPC_AddItem(long sender, string itemName)
    {
        //IL_0059: Unknown result type (might be due to invalid IL or missing references)
        //IL_005e: Unknown result type (might be due to invalid IL or missing references)
        if (IsItemAllowed(itemName))
        {
            int freeSlot = GetFreeSlot();
            if (freeSlot != -1)
            {
                SetSlot(freeSlot, itemName, 0f);
                m_nview.InvokeRPC(ZNetView.Everybody, "SetSlotVisual", freeSlot, itemName);
                m_addEffect.Create(m_slots[freeSlot].get_position(), Quaternion.get_identity());
            }
        }
    }

    public void SetSlot(int slot, string itemName, float cookedTime)
    {
        m_nview.GetZDO().Set("slot" + slot, itemName);
        m_nview.GetZDO().Set("slot" + slot, cookedTime);
    }

    public void GetSlot(int slot, out string itemName, out float cookedTime)
    {
        itemName = m_nview.GetZDO().GetString("slot" + slot);
        cookedTime = m_nview.GetZDO().GetFloat("slot" + slot);
    }

    public int GetFreeSlot()
    {
        for (int i = 0; i < m_slots.Length; i++)
        {
            if (m_nview.GetZDO().GetString("slot" + i) == "")
            {
                return i;
            }
        }
        return -1;
    }

    public bool IsItemAllowed(ItemDrop.ItemData item)
    {
        return IsItemAllowed(((Object)item.m_dropPrefab).get_name());
    }

    public bool IsItemAllowed(string itemName)
    {
        foreach (ItemConversion item in m_conversion)
        {
            if (((Object)((Component)item.m_from).get_gameObject()).get_name() == itemName)
            {
                return true;
            }
        }
        return false;
    }

    public ItemConversion GetItemConversion(string itemName)
    {
        foreach (ItemConversion item in m_conversion)
        {
            if (((Object)((Component)item.m_from).get_gameObject()).get_name() == itemName || ((Object)((Component)item.m_to).get_gameObject()).get_name() == itemName)
            {
                return item;
            }
        }
        return null;
    }

    public CookingStation()
        : this()
    {
    }
}
