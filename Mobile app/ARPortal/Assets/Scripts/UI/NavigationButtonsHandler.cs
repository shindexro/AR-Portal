using UnityEngine;
using UnityEngine.SceneManagement;

public class NavigationButtonsHandler : MonoBehaviour {

    public void ToSearchTab()
    {
        SceneManager.LoadScene("SearchTab");
    }

    public void ToPortalListTab()
    {
        SceneManager.LoadScene("PortalListTab");
    }
}
