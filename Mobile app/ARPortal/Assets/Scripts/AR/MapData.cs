using System;
using System.Collections.Generic;

[Serializable]
public class MapData
{
    public string idCode;
    public string name;
    public string description;
    public List<string> tags;
    public List<ObjectData> objects;
}

[Serializable]
public class ObjectData
{
    public int type;
    public string model;
    
    public float scaleX;
    public float scaleY;
    public float scaleZ;
    
    public float posX;
    public float posY;
    public float posZ;

    public float rotX;
    public float rotY;
    public float rotZ;

    public int action;
    public string url;
}