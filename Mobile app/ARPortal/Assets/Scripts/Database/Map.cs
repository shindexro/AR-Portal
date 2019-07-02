using SQLite4Unity3d;

public class Map
{
    [PrimaryKey, AutoIncrement] public int Id { get; set; }
    public string Code { get; set; }
    public string MapJson { get; set; }
}