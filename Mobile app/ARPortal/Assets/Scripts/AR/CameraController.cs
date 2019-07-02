using System.Collections;
using System.Collections.Generic;
using UnityEngine;

public class CameraController : MonoBehaviour
{
    // This class is only for testing purposes
    // used for controlling the camera in the Unity Editor
    
    void Start()
    {
    }

    void Update()
    {
        transform.Translate(Input.GetAxis("Horizontal"), 0, Input.GetAxis("Vertical"));
        float rotation = 0;
        if (Input.GetKey(KeyCode.Q))
        {
            rotation--;
        }
        else if (Input.GetKey(KeyCode.E))
        {
            rotation++;
        }

        transform.Rotate(0, rotation, 0);
    }
}