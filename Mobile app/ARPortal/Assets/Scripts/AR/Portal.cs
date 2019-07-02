using UnityEngine;
using UnityEngine.Rendering;

public class Portal : MonoBehaviour
{
    public Camera device;

    private bool hasCollided;

    private static readonly int StencilTest = Shader.PropertyToID("_StencilTest");

    void Start()
    {
        HideMaterialsBehindPortal();
    }

    void Update()
    {
        WhileDeviceColliding();
    }

    void OnDestroy()
    {
        ShowMaterialsInsidePortal();
    }

    void OnTriggerEnter(Collider other)
    {
        if (other.transform != device.transform)
            return;
        hasCollided = true;
    }

    void OnTriggerExit(Collider other)
    {
        if (other.transform != device.transform)
            return;
        hasCollided = false;
    }

    private void WhileDeviceColliding()
    {
        if (!hasCollided)
            return;

        if (IsInsidePortal())
        {
            ShowMaterialsInsidePortal();
        }
        else
        {
            HideMaterialsBehindPortal();
        }
    }

    private bool IsInsidePortal()
    {
        Vector3 relativePosToPortal = transform.InverseTransformPoint(device.transform.position);
        return relativePosToPortal.z > -0.3f;
        // this value is obtain from test trail for smooth transition into the portal
    }

    private void ShowMaterialsInsidePortal()
    {
        SetMaterialsStencilTest(CompareFunction.Always);
    }

    private void HideMaterialsBehindPortal()
    {
        SetMaterialsStencilTest(CompareFunction.Equal);
    }

    private void SetMaterialsStencilTest(CompareFunction compare)
    {
        Shader.SetGlobalInt(StencilTest, (int) compare);
    }
}