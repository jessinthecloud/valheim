// CraftingStation
using System.Collections.Generic;
using UnityEngine;

public class CraftingStation : MonoBehaviour, Hoverable, Interactable
{
    public string m_name = "";

    public Sprite m_icon;

    public float m_discoverRange = 4f;

    public float m_rangeBuild = 10f;

    public bool m_craftRequireRoof = true;

    public bool m_craftRequireFire = true;

    public Transform m_roofCheckPoint;

    public Transform m_connectionPoint;

    public bool m_showBasicRecipies;

    public float m_useDistance = 2f;

    public int m_useAnimation;

    public GameObject m_areaMarker;

    public GameObject m_inUseObject;

    public GameObject m_haveFireObject;

    public EffectList m_craftItemEffects = new EffectList();

    public EffectList m_craftItemDoneEffects = new EffectList();

    public EffectList m_repairItemDoneEffects = new EffectList();

    private const float m_updateExtensionInterval = 2f;

    private float m_updateExtensionTimer;

    private float m_useTimer = 10f;

    private bool m_haveFire;

    private ZNetView m_nview;

    private List<StationExtension> m_attachedExtensions = new List<StationExtension>();

    private static List<CraftingStation> m_allStations = new List<CraftingStation>();

    private static int m_triggerMask = 0;

    private void Start()
    {
        m_nview = GetComponent<ZNetView>();
        if (!m_nview || m_nview.GetZDO() != null)
        {
            m_allStations.Add(this);
            if ((bool)m_areaMarker)
            {
                m_areaMarker.SetActive(value: false);
            }
            if (m_craftRequireFire)
            {
                InvokeRepeating("CheckFire", 1f, 1f);
            }
        }
    }

    private void OnDestroy()
    {
        m_allStations.Remove(this);
    }

    public bool Interact(Humanoid user, bool repeat)
    {
        if (repeat)
        {
            return false;
        }
        if (user == Player.m_localPlayer)
        {
            if (!InUseDistance(user))
            {
                return false;
            }
            Player player = user as Player;
            if (CheckUsable(player, showMessage: true))
            {
                player.SetCraftingStation(this);
                InventoryGui.instance.Show(null);
                return false;
            }
        }
        return false;
    }

    public bool UseItem(Humanoid user, ItemDrop.ItemData item)
    {
        return false;
    }

    public bool CheckUsable(Player player, bool showMessage)
    {
        if (m_craftRequireRoof)
        {
            Cover.GetCoverForPoint(m_roofCheckPoint.position, out var coverPercentage, out var underRoof);
            if (!underRoof)
            {
                if (showMessage)
                {
                    player.Message(MessageHud.MessageType.Center, "$msg_stationneedroof");
                }
                return false;
            }
            if (coverPercentage < 0.7f)
            {
                if (showMessage)
                {
                    player.Message(MessageHud.MessageType.Center, "$msg_stationtooexposed");
                }
                return false;
            }
        }
        if (m_craftRequireFire && !m_haveFire)
        {
            if (showMessage)
            {
                player.Message(MessageHud.MessageType.Center, "$msg_needfire");
            }
            return false;
        }
        return true;
    }

    public string GetHoverText()
    {
        if (!InUseDistance(Player.m_localPlayer))
        {
            return Localization.instance.Localize("<color=grey>$piece_toofar</color>");
        }
        return Localization.instance.Localize(m_name + "\n[<color=yellow><b>$KEY_Use</b></color>] $piece_use ");
    }

    public string GetHoverName()
    {
        return m_name;
    }

    public void ShowAreaMarker()
    {
        if ((bool)m_areaMarker)
        {
            m_areaMarker.SetActive(value: true);
            CancelInvoke("HideMarker");
            Invoke("HideMarker", 0.5f);
            PokeInUse();
        }
    }

    private void HideMarker()
    {
        m_areaMarker.SetActive(value: false);
    }

    public static void UpdateKnownStationsInRange(Player player)
    {
        Vector3 position = player.transform.position;
        foreach (CraftingStation allStation in m_allStations)
        {
            if (Vector3.Distance(allStation.transform.position, position) < allStation.m_discoverRange)
            {
                player.AddKnownStation(allStation);
            }
        }
    }

    private void FixedUpdate()
    {
        if (!(m_nview == null) && m_nview.IsValid())
        {
            m_useTimer += Time.fixedDeltaTime;
            m_updateExtensionTimer += Time.fixedDeltaTime;
            if ((bool)m_inUseObject)
            {
                m_inUseObject.SetActive(m_useTimer < 1f);
            }
        }
    }

    private void CheckFire()
    {
        m_haveFire = EffectArea.IsPointInsideArea(base.transform.position, EffectArea.Type.Burning, 0.25f);
        if ((bool)m_haveFireObject)
        {
            m_haveFireObject.SetActive(m_haveFire);
        }
    }

    public void PokeInUse()
    {
        m_useTimer = 0f;
        TriggerExtensionEffects();
    }

    public static CraftingStation GetCraftingStation(Vector3 point)
    {
        if (m_triggerMask == 0)
        {
            m_triggerMask = LayerMask.GetMask("character_trigger");
        }
        Collider[] array = Physics.OverlapSphere(point, 0.1f, m_triggerMask, (QueryTriggerInteraction)2);
        foreach (Collider val in array)
        {
            if (((Component)(object)val).gameObject.CompareTag("StationUseArea"))
            {
                CraftingStation componentInParent = ((Component)(object)val).GetComponentInParent<CraftingStation>();
                if (componentInParent != null)
                {
                    return componentInParent;
                }
            }
        }
        return null;
    }

    public static CraftingStation HaveBuildStationInRange(string name, Vector3 point)
    {
        foreach (CraftingStation allStation in m_allStations)
        {
            if (!(allStation.m_name != name))
            {
                float rangeBuild = allStation.m_rangeBuild;
                if (Vector3.Distance(allStation.transform.position, point) < rangeBuild)
                {
                    return allStation;
                }
            }
        }
        return null;
    }

    public static void FindStationsInRange(string name, Vector3 point, float range, List<CraftingStation> stations)
    {
        foreach (CraftingStation allStation in m_allStations)
        {
            if (!(allStation.m_name != name) && Vector3.Distance(allStation.transform.position, point) < range)
            {
                stations.Add(allStation);
            }
        }
    }

    public static CraftingStation FindClosestStationInRange(string name, Vector3 point, float range)
    {
        CraftingStation craftingStation = null;
        float num = 99999f;
        foreach (CraftingStation allStation in m_allStations)
        {
            if (!(allStation.m_name != name))
            {
                float num2 = Vector3.Distance(allStation.transform.position, point);
                if (num2 < range && (num2 < num || craftingStation == null))
                {
                    craftingStation = allStation;
                    num = num2;
                }
            }
        }
        return craftingStation;
    }

    private List<StationExtension> GetExtensions()
    {
        if (m_updateExtensionTimer > 2f)
        {
            m_updateExtensionTimer = 0f;
            m_attachedExtensions.Clear();
            StationExtension.FindExtensions(this, base.transform.position, m_attachedExtensions);
        }
        return m_attachedExtensions;
    }

    private void TriggerExtensionEffects()
    {
        Vector3 connectionEffectPoint = GetConnectionEffectPoint();
        foreach (StationExtension extension in GetExtensions())
        {
            if ((bool)extension)
            {
                extension.StartConnectionEffect(connectionEffectPoint);
            }
        }
    }

    public Vector3 GetConnectionEffectPoint()
    {
        if ((bool)m_connectionPoint)
        {
            return m_connectionPoint.position;
        }
        return base.transform.position;
    }

    public int GetLevel()
    {
        return 1 + GetExtensions().Count;
    }

    public bool InUseDistance(Humanoid human)
    {
        return Vector3.Distance(human.transform.position, base.transform.position) < m_useDistance;
    }
}
