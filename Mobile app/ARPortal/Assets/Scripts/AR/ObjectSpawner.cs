using System;
using System.Collections.Generic;
using System.IO;
using UnityEditor;
using UnityEngine;
using UnityEngine.Video;
using YoutubeExplode;
using YoutubeExplode.Models;
using YoutubeExplode.Models.MediaStreams;

public class ObjectSpawner : MonoBehaviour
{
    public static string mapCode;
    public static string mapJson;

    public Camera arCamera;
    public GameObject world;
    public GameObject videoSphere;
    public VideoPlayer videoPlayer;

    private MapData mapData;
    private List<GameObject> objects;
    private Dictionary<GameObject, Tuple<int, string>> objectActions;

    private GameObject holdingObject;
    private Vector3 holdingCenterOffset;
    private float holdingDistance;
    private float touchTime;

    void Start()
    {
        mapData = JsonUtility.FromJson<MapData>(mapJson);
        Debug.Log(mapJson);
        SetupObjects();
    }

    void Update()
    {
        // Stop playing video if user tap on the Android back button
        if (Input.GetKeyDown(KeyCode.Escape))
        {
            world.SetActive(videoSphere.activeSelf || world.activeSelf);
            videoSphere.SetActive(false);
            ActivateAllObjects();
        }
        TrackObjectTap();
    }

    // Keeps a track of what object is being tapped and for how long
    private void TrackObjectTap()
    {
        foreach (Touch touch in Input.touches)
        {
            touchTime += touch.deltaTime;
            Ray ray = arCamera.ScreenPointToRay(Input.GetTouch(0).position);
            RaycastHit hit;
            switch (touch.phase)
            {
                case TouchPhase.Began:
                    if (Physics.Raycast(ray, out hit))
                    {
                        touchTime = 0f;
                        Transform objectHit = hit.transform;
                        holdingObject = TrackedParent(objectHit);
                        holdingDistance = Vector3.Distance(ray.origin, holdingObject.transform.position);
                        holdingCenterOffset = holdingObject.transform.position - hit.point;
                    }

                    break;

                case TouchPhase.Moved:
                case TouchPhase.Stationary:
                    if (holdingObject && touchTime > 2.0f)
                    {
                        holdingObject.transform.position =
                            ray.origin + ray.direction * holdingDistance + holdingCenterOffset;
                    }

                    break;

                case TouchPhase.Ended:
                case TouchPhase.Canceled:
                    if (touchTime < 2.0f && Physics.Raycast(ray, out hit))
                    {
                        Transform objectHit = hit.transform;
                        TriggerInteraction(TrackedParent(objectHit));
                    }

                    holdingObject = null;
                    touchTime = 0f;
                    break;
            }
        }
    }

    private void TriggerInteraction(GameObject obj)
    {
        PlayVideo(objectActions[obj].Item2);
    }

    private async void PlayVideo(string youtubeUrl)
    {
        YoutubeClient client = new YoutubeClient();
        string videoId = YoutubeClient.ParseVideoId(youtubeUrl);
        MediaStreamInfoSet streamInfoSet = await client.GetVideoMediaStreamInfosAsync(videoId);

        MuxedStreamInfo streamInfo = streamInfoSet.Muxed.WithHighestVideoQuality();
        videoPlayer.url = streamInfo.Url;

        videoSphere.SetActive(true);
        world.SetActive(false);
        DeactivateAllObjects();
    }

    private void ActivateAllObjects()
    {
        foreach (GameObject obj in objects)
        {
            obj.SetActive(true);
        }
    }

    private void DeactivateAllObjects()
    {
        foreach (GameObject obj in objects)
        {
            obj.SetActive(false);
        }
    }

    // Returns a parent of a tranform, the parent must be in the objects[] list
    // Useful for GameObjects with multiple layer hierarchy,
    // the user might tap on one of the children and we need to find which object did the user tap on
    private GameObject TrackedParent(Transform obj)
    {
        while (obj)
        {
            if (objects.Contains(obj.gameObject))
            {
                return obj.gameObject;
            }

            obj = obj.parent;
        }

        return null;
    }
    
    // Load room data from JSON and instantiate objects accordingly
    private void SetupObjects()
    {
        objects = new List<GameObject>();
        objectActions = new Dictionary<GameObject, Tuple<int, string>>();
        foreach (ObjectData data in mapData.objects)
        {
            try
            {
                GameObject obj = InstantiateObject(data);
                objects.Add(obj);
                objectActions.Add(obj, new Tuple<int, string>(data.action, data.url));
            }
            catch (ArgumentException e)
            {
                Debug.Log($"Missing prefab: {data.model}");
            }
        }
    }

    private GameObject InstantiateObject(ObjectData data)
    {
        GameObject prefab = GetPrefab(data.type, data.model);
        GameObject obj = Instantiate(prefab, transform);
        obj.transform.localPosition = new Vector3(-data.posX, data.posY, data.posZ);
        obj.transform.Rotate(RadianToDegree(data.rotX), RadianToDegree(data.rotY), RadianToDegree(data.rotZ));
        obj.transform.localScale = new Vector3(data.scaleX, data.scaleY, data.scaleZ);
        AddMeshColliderToObject(obj.transform);
        AddMeshColliderToAllChildren(obj.transform);
        ApplyPortalShader(obj);
        return obj;
    }

    private static float RadianToDegree(float rad)
    {
        return (float) (rad * 180 / Math.PI);
    }

    private static void ApplyPortalShader(GameObject obj)
    {
        foreach (MeshRenderer renderer in obj.GetComponentsInChildren<MeshRenderer>())
        {
            ApplyShader(renderer);
        }
    }

    private static void ApplyShader(Renderer renderer)
    {
        if (renderer != null)
        {
            foreach (Material material in renderer.sharedMaterials)
            {
                material.shader = Shader.Find("Custom/Specular Stencil Filter");
            }
        }
    }

    private static void AddMeshColliderToObject(Component obj)
    {
        Mesh mesh = GetObjectMesh(obj);

        if (mesh != null)
        {
            MeshCollider meshCollider = obj.gameObject.AddComponent<MeshCollider>();
            meshCollider.sharedMesh = mesh;
        }
    }

    private static void AddMeshColliderToAllChildren(Component obj)
    {
        foreach (Transform child in obj.transform)
        {
            AddMeshColliderToObject(child);
            AddMeshColliderToAllChildren(child);
        }
    }

    private static Mesh GetObjectMesh(Component obj)
    {
        SkinnedMeshRenderer skinnedMeshRenderer = obj.GetComponent<SkinnedMeshRenderer>();
        if (skinnedMeshRenderer != null)
        {
            return skinnedMeshRenderer.sharedMesh;
        }

        MeshFilter meshFilter = obj.GetComponent<MeshFilter>();
        return meshFilter ? meshFilter.sharedMesh : null;
    }

    private GameObject GetPrefab(int type, string fileName)
    {
        // If it is a default model, load prefab from the Resources folder
        if (type == 0)
        {
            return Resources.Load($"Models/{fileName.Substring(0, fileName.Length - 4)}", typeof(GameObject)) as
                GameObject;
        }

        // If it is a custom model, load prefab from the AssetBundle
        string path = Path.Combine(Application.persistentDataPath, mapCode);
        AssetBundle assetBundle = AssetBundle.LoadFromFile(path);
        if (assetBundle == null)
        {
            Debug.Log("Failed to load AssetBundle!");
        }

        GameObject prefab = assetBundle.LoadAsset<GameObject>(fileName.Substring(0, fileName.Length - 4));
        return prefab;
    }
}