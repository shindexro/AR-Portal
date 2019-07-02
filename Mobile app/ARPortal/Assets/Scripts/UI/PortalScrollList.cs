using System;
using System.Collections;
using System.Collections.Generic;
using System.Linq;
using UnityEngine;
using UnityEngine.SceneManagement;

[Serializable]
public class Card
{
    public int mapId;
    public string mapName;
    public string mapDescription;
    public string mapCode;
}

public class PortalScrollList : MonoBehaviour
{
    public Transform contentPanel;
    public SimpleObjectPool itemObjectPool;

    private List<Card> cards;

    void Start()
    {
        RefreshDisplay();
    }

    public void RefreshDisplay()
    {
        GetCards();
        RemoveCards();
        SetupCards();
    }

    // Load portals from the database into a list
    private void GetCards()
    {
        cards = new List<Card>();
        foreach (Map map in DatabaseService.GetMaps())
        {
            Card card = new Card();
            MapData mapData = JsonUtility.FromJson<MapData>(map.MapJson);

            card.mapId = map.Id;
            card.mapName = mapData.name;
            card.mapDescription = mapData.description;
            card.mapCode = mapData.idCode;
            cards.Add(card);
        }
    }

    // Set up a list item for each portal
    private void SetupCards()
    {
        foreach (Card card in cards)
        {
            GameObject newItem = itemObjectPool.GetObject();
            newItem.transform.SetParent(contentPanel);
            newItem.transform.localScale = Vector3.one;

            PortalListItem portalListItem = newItem.GetComponent<PortalListItem>();
            portalListItem.Setup(card, this);
        }
    }

    private void RemoveCards()
    {
        while (contentPanel.childCount > 0)
        {
            GameObject toRemove = transform.GetChild(0).gameObject;
            itemObjectPool.ReturnObject(toRemove);
        }
    }
}