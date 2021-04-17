// Piece.Requirement
using System;
using UnityEngine;

[Serializable]
public class Requirement
{
    [Header("Resource")]
    public ItemDrop m_resItem;

    public int m_amount = 1;

    [Header("Item")]
    public int m_amountPerLevel = 1;

    [Header("Piece")]
    public bool m_recover = true;

    public int GetAmount(int qualityLevel)
    {
        if (qualityLevel <= 1)
        {
            return m_amount;
        }
        return (qualityLevel - 1) * m_amountPerLevel;
    }
}
