

// StationExtension
using System.Collections.Generic;
using UnityEngine;

public class StationExtension : MonoBehaviour, Hoverable
{
    public CraftingStation m_craftingStation;

    public float m_maxStationDistance = 5f;

    public GameObject m_connectionPrefab;

    public GameObject m_connection;

    public Piece m_piece;

    public Collider[] m_colliders;

    public static List<StationExtension> m_allExtensions = new List<StationExtension>();

    public void Awake()
    {
        if (((Component)this).GetComponent<ZNetView>().GetZDO() != null)
        {
            m_piece = ((Component)this).GetComponent<Piece>();
            m_allExtensions.Add(this);
        }
    }

    public void OnDestroy()
    {
        if (Object.op_Implicit((Object)(object)m_connection))
        {
            Object.Destroy((Object)(object)m_connection);
            m_connection = null;
        }
        m_allExtensions.Remove(this);
    }

    public string GetHoverText()
    {
        PokeEffect();
        return Localization.get_instance().Localize(m_piece.m_name);
    }

    public string GetHoverName()
    {
        return Localization.get_instance().Localize(m_piece.m_name);
    }

    public string GetExtensionName()
    {
        return m_piece.m_name;
    }

    public static void FindExtensions(CraftingStation station, Vector3 pos, List<StationExtension> extensions)
    {
        //IL_001b: Unknown result type (might be due to invalid IL or missing references)
        //IL_0020: Unknown result type (might be due to invalid IL or missing references)
        foreach (StationExtension allExtension in m_allExtensions)
        {
            if (Vector3.Distance(((Component)allExtension).get_transform().get_position(), pos) < allExtension.m_maxStationDistance && allExtension.m_craftingStation.m_name == station.m_name && !ExtensionInList(extensions, allExtension))
            {
                extensions.Add(allExtension);
            }
        }
    }

    public static bool ExtensionInList(List<StationExtension> extensions, StationExtension extension)
    {
        foreach (StationExtension extension2 in extensions)
        {
            if (extension2.GetExtensionName() == extension.GetExtensionName())
            {
                return true;
            }
        }
        return false;
    }

    public bool OtherExtensionInRange(float radius)
    {
        //IL_0024: Unknown result type (might be due to invalid IL or missing references)
        //IL_002f: Unknown result type (might be due to invalid IL or missing references)
        foreach (StationExtension allExtension in m_allExtensions)
        {
            if (!((Object)(object)allExtension == (Object)(object)this) && Vector3.Distance(((Component)allExtension).get_transform().get_position(), ((Component)this).get_transform().get_position()) < radius)
            {
                return true;
            }
        }
        return false;
    }

    public List<CraftingStation> FindStationsInRange(Vector3 center)
    {
        //IL_0011: Unknown result type (might be due to invalid IL or missing references)
        List<CraftingStation> list = new List<CraftingStation>();
        CraftingStation.FindStationsInRange(m_craftingStation.m_name, center, m_maxStationDistance, list);
        return list;
    }

    public CraftingStation FindClosestStationInRange(Vector3 center)
    {
        //IL_000b: Unknown result type (might be due to invalid IL or missing references)
        return CraftingStation.FindClosestStationInRange(m_craftingStation.m_name, center, m_maxStationDistance);
    }

    public void PokeEffect()
    {
        //IL_0007: Unknown result type (might be due to invalid IL or missing references)
        CraftingStation craftingStation = FindClosestStationInRange(((Component)this).get_transform().get_position());
        if (Object.op_Implicit((Object)(object)craftingStation))
        {
            StartConnectionEffect(craftingStation);
        }
    }

    public void StartConnectionEffect(CraftingStation station)
    {
        //IL_0002: Unknown result type (might be due to invalid IL or missing references)
        StartConnectionEffect(station.GetConnectionEffectPoint());
    }

    public void StartConnectionEffect(Vector3 targetPos)
    {
        //IL_0001: Unknown result type (might be due to invalid IL or missing references)
        //IL_0006: Unknown result type (might be due to invalid IL or missing references)
        //IL_001c: Unknown result type (might be due to invalid IL or missing references)
        //IL_001d: Unknown result type (might be due to invalid IL or missing references)
        //IL_002c: Unknown result type (might be due to invalid IL or missing references)
        //IL_002d: Unknown result type (might be due to invalid IL or missing references)
        //IL_002e: Unknown result type (might be due to invalid IL or missing references)
        //IL_0033: Unknown result type (might be due to invalid IL or missing references)
        //IL_0036: Unknown result type (might be due to invalid IL or missing references)
        //IL_003b: Unknown result type (might be due to invalid IL or missing references)
        //IL_0040: Unknown result type (might be due to invalid IL or missing references)
        //IL_004c: Unknown result type (might be due to invalid IL or missing references)
        //IL_005d: Unknown result type (might be due to invalid IL or missing references)
        //IL_007f: Unknown result type (might be due to invalid IL or missing references)
        Vector3 center = GetCenter();
        if ((Object)(object)m_connection == (Object)null)
        {
            m_connection = Object.Instantiate<GameObject>(m_connectionPrefab, center, Quaternion.get_identity());
        }
        Vector3 val = targetPos - center;
        Quaternion rotation = Quaternion.LookRotation(((Vector3)(ref val)).get_normalized());
        m_connection.get_transform().set_position(center);
        m_connection.get_transform().set_rotation(rotation);
        m_connection.get_transform().set_localScale(new Vector3(1f, 1f, ((Vector3)(ref val)).get_magnitude()));
        ((MonoBehaviour)this).CancelInvoke("StopConnectionEffect");
        ((MonoBehaviour)this).Invoke("StopConnectionEffect", 1f);
    }

    public void StopConnectionEffect()
    {
        if (Object.op_Implicit((Object)(object)m_connection))
        {
            Object.Destroy((Object)(object)m_connection);
            m_connection = null;
        }
    }

    public Vector3 GetCenter()
    {
        //IL_001a: Unknown result type (might be due to invalid IL or missing references)
        //IL_001f: Unknown result type (might be due to invalid IL or missing references)
        //IL_0030: Unknown result type (might be due to invalid IL or missing references)
        //IL_0035: Unknown result type (might be due to invalid IL or missing references)
        //IL_0039: Unknown result type (might be due to invalid IL or missing references)
        //IL_0043: Unknown result type (might be due to invalid IL or missing references)
        //IL_004e: Unknown result type (might be due to invalid IL or missing references)
        //IL_0053: Unknown result type (might be due to invalid IL or missing references)
        //IL_0057: Unknown result type (might be due to invalid IL or missing references)
        //IL_0070: Unknown result type (might be due to invalid IL or missing references)
        if (m_colliders == null)
        {
            m_colliders = ((Component)this).GetComponentsInChildren<Collider>();
        }
        Vector3 position = ((Component)this).get_transform().get_position();
        Collider[] colliders = m_colliders;
        foreach (Collider val in colliders)
        {
            Bounds bounds = val.get_bounds();
            if (((Bounds)(ref bounds)).get_max().y > position.y)
            {
                bounds = val.get_bounds();
                position.y = ((Bounds)(ref bounds)).get_max().y;
            }
        }
        return position;
    }

    public StationExtension()
        : this()
    {
    }
}
