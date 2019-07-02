using System.Collections.Generic;
using GoogleARCore;
using UnityEngine;
using UnityEngine.UI;

public class ARController : MonoBehaviour
{
    public GameObject gridPrefab;
    public GameObject portal;
    public GameObject arCamera;

    public Text cameraPositionText;
    public Text relativePositionText;

    private List<DetectedPlane> newDetectedPlanes = new List<DetectedPlane>();

    private void Start()
    {
        portal.SetActive(false);
    }

    void Update()
    {
        if (Session.Status == SessionStatus.Tracking)
        {
            AddDetectedPlanes();
            InstantiatePlaneGrid();
            if (!portal.activeInHierarchy)
            {
                PlacePortalIfPlaneIsTouched();
            }
        }

        DisplayCameraPosition();
    }

    // Only for debug purposes
    private void DisplayCameraPosition()
    {
        cameraPositionText.text = arCamera.transform.position.ToString("F2");
        relativePositionText.text = portal.transform.InverseTransformPoint(arCamera.transform.position).ToString("F2");
    }

    private void AddDetectedPlanes()
    {
        Session.GetTrackables(newDetectedPlanes, TrackableQueryFilter.New);
    }

    // Instantiate grid on new detected surfaces
    private void InstantiatePlaneGrid()
    {
        foreach (DetectedPlane plane in newDetectedPlanes)
        {
            GameObject grid = Instantiate(gridPrefab, Vector3.zero, Quaternion.identity, transform);
            grid.GetComponent<GridVisualiser>().Initialize(plane);
        }
    }

    private void PlacePortalIfPlaneIsTouched()
    {
        TrackableHit hit;
        if (IsBeginningOfScreenTouch() && IsDetectedPlaneTouched(out hit))
        {
            portal.SetActive(true);
            Anchor anchor = hit.Trackable.CreateAnchor(hit.Pose);
            portal.transform.position = hit.Pose.position;
            portal.transform.rotation = hit.Pose.rotation;

            Vector3 cameraPosition = arCamera.transform.position;
            cameraPosition.y = hit.Pose.position.y;
            portal.transform.LookAt(2 * portal.transform.position - cameraPosition, portal.transform.up);
            portal.transform.parent = anchor.transform;
        }
    }

    private static bool IsBeginningOfScreenTouch()
    {
        return Input.touchCount > 0 && Input.GetTouch(0).phase == TouchPhase.Began;
    }

    private static bool IsDetectedPlaneTouched(out TrackableHit hit)
    {
        Touch touch = Input.GetTouch(0);
        return Frame.Raycast(touch.position.x, touch.position.y, TrackableHitFlags.PlaneWithinPolygon, out hit);
    }
}