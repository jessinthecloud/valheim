// Heightmap.Biome
public enum Biome
{
    None = 0,
    Meadows = 1,
    Swamp = 2,
    Mountain = 4,
    BlackForest = 8,
    Plains = 0x10,
    AshLands = 0x20,
    DeepNorth = 0x40,
    Ocean = 0x100,
    Mistlands = 0x200,
    BiomesMax = 513
}

// Player.Food
// food that's been eaten?
public class Food
{
    public string m_name = "";

    public ItemDrop.ItemData m_item;

    public float m_health;

    public float m_stamina;

    public bool CanEatAgain()
    {
        return m_health < m_item.m_shared.m_food / 2f;
    }
}
