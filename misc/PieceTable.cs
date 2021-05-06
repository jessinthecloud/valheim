

// PieceTable
using System;
using System.Collections.Generic;
using UnityEngine;

public class PieceTable : MonoBehaviour
{
    public const int m_gridWidth = 10;

    public const int m_gridHeight = 5;

    public List<GameObject> m_pieces = new List<GameObject>();

    public bool m_useCategories = true;

    public bool m_canRemovePieces = true;

    [NonSerialized]
    public List<List<Piece>> m_availablePieces = new List<List<Piece>>();

    [NonSerialized]
    public Piece.PieceCategory m_selectedCategory;

    [NonSerialized]
    public Vector2Int[] m_selectedPiece = (Vector2Int[])(object)new Vector2Int[5];

    public void UpdateAvailable(HashSet<string> knownRecipies, Player player, bool hideUnavailable, bool noPlacementCost)
    {
        if (m_availablePieces.Count == 0)
        {
            for (int i = 0; i < 4; i++)
            {
                m_availablePieces.Add(new List<Piece>());
            }
        }
        foreach (List<Piece> availablePiece in m_availablePieces)
        {
            availablePiece.Clear();
        }
        foreach (GameObject piece in m_pieces)
        {
            Piece component = piece.GetComponent<Piece>();
            if (!noPlacementCost && (!knownRecipies.Contains(component.m_name) || !component.m_enabled || (hideUnavailable && !player.HaveRequirements(component, Player.RequirementMode.CanAlmostBuild))))
            {
                continue;
            }
            if (component.m_category == Piece.PieceCategory.All)
            {
                for (int j = 0; j < 4; j++)
                {
                    m_availablePieces[j].Add(component);
                }
            }
            else
            {
                m_availablePieces[(int)component.m_category].Add(component);
            }
        }
    }

    public GameObject GetSelectedPrefab()
    {
        Piece selectedPiece = GetSelectedPiece();
        if (Object.op_Implicit((Object)(object)selectedPiece))
        {
            return ((Component)selectedPiece).get_gameObject();
        }
        return null;
    }

    public Piece GetPiece(int category, Vector2Int p)
    {
        if (m_availablePieces[category].Count == 0)
        {
            return null;
        }
        int num = ((Vector2Int)(ref p)).get_y() * 10 + ((Vector2Int)(ref p)).get_x();
        if (num < 0 || num >= m_availablePieces[category].Count)
        {
            return null;
        }
        return m_availablePieces[category][num];
    }

    public Piece GetPiece(Vector2Int p)
    {
        //IL_0007: Unknown result type (might be due to invalid IL or missing references)
        return GetPiece((int)m_selectedCategory, p);
    }

    public bool IsPieceAvailable(Piece piece)
    {
        foreach (Piece item in m_availablePieces[(int)m_selectedCategory])
        {
            if ((Object)(object)item == (Object)(object)piece)
            {
                return true;
            }
        }
        return false;
    }

    public Piece GetSelectedPiece()
    {
        //IL_0001: Unknown result type (might be due to invalid IL or missing references)
        //IL_0006: Unknown result type (might be due to invalid IL or missing references)
        //IL_000e: Unknown result type (might be due to invalid IL or missing references)
        Vector2Int selectedIndex = GetSelectedIndex();
        return GetPiece((int)m_selectedCategory, selectedIndex);
    }

    public int GetAvailablePiecesInCategory(Piece.PieceCategory cat)
    {
        return m_availablePieces[(int)cat].Count;
    }

    public List<Piece> GetPiecesInSelectedCategory()
    {
        return m_availablePieces[(int)m_selectedCategory];
    }

    public int GetAvailablePiecesInSelectedCategory()
    {
        return GetAvailablePiecesInCategory(m_selectedCategory);
    }

    public Vector2Int GetSelectedIndex()
    {
        //IL_000c: Unknown result type (might be due to invalid IL or missing references)
        return m_selectedPiece[(int)m_selectedCategory];
    }

    public void SetSelected(Vector2Int p)
    {
        //IL_000c: Unknown result type (might be due to invalid IL or missing references)
        //IL_000d: Unknown result type (might be due to invalid IL or missing references)
        m_selectedPiece[(int)m_selectedCategory] = p;
    }

    public void LeftPiece()
    {
        //IL_0026: Unknown result type (might be due to invalid IL or missing references)
        //IL_002b: Unknown result type (might be due to invalid IL or missing references)
        //IL_005c: Unknown result type (might be due to invalid IL or missing references)
        //IL_005d: Unknown result type (might be due to invalid IL or missing references)
        if (m_availablePieces[(int)m_selectedCategory].Count > 1)
        {
            Vector2Int val = m_selectedPiece[(int)m_selectedCategory];
            int x = ((Vector2Int)(ref val)).get_x() - 1;
            ((Vector2Int)(ref val)).set_x(x);
            if (((Vector2Int)(ref val)).get_x() < 0)
            {
                ((Vector2Int)(ref val)).set_x(9);
            }
            m_selectedPiece[(int)m_selectedCategory] = val;
        }
    }

    public void RightPiece()
    {
        //IL_0026: Unknown result type (might be due to invalid IL or missing references)
        //IL_002b: Unknown result type (might be due to invalid IL or missing references)
        //IL_005c: Unknown result type (might be due to invalid IL or missing references)
        //IL_005d: Unknown result type (might be due to invalid IL or missing references)
        if (m_availablePieces[(int)m_selectedCategory].Count > 1)
        {
            Vector2Int val = m_selectedPiece[(int)m_selectedCategory];
            int x = ((Vector2Int)(ref val)).get_x() + 1;
            ((Vector2Int)(ref val)).set_x(x);
            if (((Vector2Int)(ref val)).get_x() >= 10)
            {
                ((Vector2Int)(ref val)).set_x(0);
            }
            m_selectedPiece[(int)m_selectedCategory] = val;
        }
    }

    public void DownPiece()
    {
        //IL_0026: Unknown result type (might be due to invalid IL or missing references)
        //IL_002b: Unknown result type (might be due to invalid IL or missing references)
        //IL_005b: Unknown result type (might be due to invalid IL or missing references)
        //IL_005c: Unknown result type (might be due to invalid IL or missing references)
        if (m_availablePieces[(int)m_selectedCategory].Count > 1)
        {
            Vector2Int val = m_selectedPiece[(int)m_selectedCategory];
            int y = ((Vector2Int)(ref val)).get_y() + 1;
            ((Vector2Int)(ref val)).set_y(y);
            if (((Vector2Int)(ref val)).get_y() >= 5)
            {
                ((Vector2Int)(ref val)).set_y(0);
            }
            m_selectedPiece[(int)m_selectedCategory] = val;
        }
    }

    public void UpPiece()
    {
        //IL_0026: Unknown result type (might be due to invalid IL or missing references)
        //IL_002b: Unknown result type (might be due to invalid IL or missing references)
        //IL_005b: Unknown result type (might be due to invalid IL or missing references)
        //IL_005c: Unknown result type (might be due to invalid IL or missing references)
        if (m_availablePieces[(int)m_selectedCategory].Count > 1)
        {
            Vector2Int val = m_selectedPiece[(int)m_selectedCategory];
            int y = ((Vector2Int)(ref val)).get_y() - 1;
            ((Vector2Int)(ref val)).set_y(y);
            if (((Vector2Int)(ref val)).get_y() < 0)
            {
                ((Vector2Int)(ref val)).set_y(4);
            }
            m_selectedPiece[(int)m_selectedCategory] = val;
        }
    }

    public void NextCategory()
    {
        if (m_useCategories)
        {
            m_selectedCategory++;
            if (m_selectedCategory == Piece.PieceCategory.Max)
            {
                m_selectedCategory = Piece.PieceCategory.Misc;
            }
        }
    }

    public void PrevCategory()
    {
        if (m_useCategories)
        {
            m_selectedCategory--;
            if (m_selectedCategory < Piece.PieceCategory.Misc)
            {
                m_selectedCategory = Piece.PieceCategory.Furniture;
            }
        }
    }

    public void SetCategory(int index)
    {
        if (m_useCategories)
        {
            m_selectedCategory = (Piece.PieceCategory)index;
            m_selectedCategory = (Piece.PieceCategory)Mathf.Clamp((int)m_selectedCategory, 0, 3);
        }
    }

    public PieceTable()
        : this()
    {
    }
}
