using System.Linq;
using UnityEngine;
using UnityEngine.SceneManagement;
using UnityEngine.UI;

public class PortalListItem : MonoBehaviour
{
    public Button enterPortalButton;
    public Button optionsButton;
    public Text mapNameText;
    public Text mapDescriptionText;
    public Text mapCodeText;

    private Card card;
    private PortalScrollList scrollList;

    void Start()
    {
        enterPortalButton.onClick.AddListener(LoadPortal);
        optionsButton.onClick.AddListener(DeletePortal);
    }

    public void Setup(Card currentCard, PortalScrollList currentScrollList)
    {
        card = currentCard;
        scrollList = currentScrollList;
        mapNameText.text = card.mapName;
        mapDescriptionText.text = card.mapDescription;
        mapCodeText.text = card.mapCode;
    }

    private void LoadPortal()
    {
        Map map = DatabaseService.GetMaps().First(m => m.Id == card.mapId);
        ObjectSpawner.mapCode = map.Code;
        ObjectSpawner.mapJson = map.MapJson;
        SceneManager.LoadScene("Room");
    }

    private void DeletePortal()
    {
        DatabaseService.DeleteMap(card.mapId);
        scrollList.RefreshDisplay();
    }
}