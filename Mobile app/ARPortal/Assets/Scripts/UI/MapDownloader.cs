using System.Collections;
using System.IO;
using UnityEngine;
using UnityEngine.Networking;
using UnityEngine.SceneManagement;
using UnityEngine.UI;

public class MapDownloader : MonoBehaviour
{
    public InputField mapCodeField;
    public InputField mapPasswordField;
    public GameObject popupPanel;
    public Text popupText;

    private IEnumerator coroutine;
    
    // Change the URL if server IP is changed
    private const string MapServerUrl = "http://100.24.29.223/worlds/getWorld.php";
    private const string AssetBundleUrl = "http://100.24.29.223/dlab.php";

    public void LoadMap()
    {
        if (MapCodeInputHandler.MapCodeFormatValid(mapCodeField.text))
        {
            string decodedMapCode = MapCodeInputHandler.DecodeMapCode(mapCodeField.text);
            Debug.Log("Downloading map: " + decodedMapCode);
            popupText.text = "Downloading map...";
            popupPanel.SetActive(true);
            coroutine = DownloadAssetBundle(decodedMapCode, mapPasswordField.text);
            StartCoroutine(coroutine);
            coroutine = DownloadMapJson(decodedMapCode, mapPasswordField.text);
            StartCoroutine(coroutine);
        }
    }

    public void CancelDownload()
    {
        StopCoroutine(coroutine);
        popupPanel.SetActive(false);
    }

    private IEnumerator DownloadMapJson(string code, string password)
    {
        WWWForm form = new WWWForm();
        form.AddField("wold", code);
        form.AddField("pass", password);
        UnityWebRequest request = UnityWebRequest.Post(MapServerUrl, form);
        yield return request.SendWebRequest();
        HandleMapJsonResponse(request);
    }

    private IEnumerator DownloadAssetBundle(string code, string password)
    {
        WWWForm form = new WWWForm();
        form.AddField("wold", code);
        form.AddField("pass", password);
        UnityWebRequest request = UnityWebRequest.Post(AssetBundleUrl, form);
        yield return request.SendWebRequest();
        Debug.Log("Fetched map AssetBundle. Size: " + request.downloadedBytes);
        string path = Path.Combine(Application.persistentDataPath, code);
        File.WriteAllBytes(path, request.downloadHandler.data);
    }

    private void HandleMapJsonResponse(UnityWebRequest response)
    {
        if (response.isNetworkError)
        {
            popupText.text = "Trouble connecting to server, make sure you have good network connection.";
            return;
        }

        string mapJson = response.downloadHandler.text;
        if (string.IsNullOrEmpty(mapJson))
        {
            popupText.text = "Cannot find map, make sure you have entered the correct map code.";
            return;
        }

        Debug.Log("Fetched map json: " + mapJson);
        DatabaseService.SaveMap(MapCodeInputHandler.DecodeMapCode(mapCodeField.text), mapJson);
        popupText.text = "Successfully downloaded and saved the map. Check the portal list.";
    }
}