using System.Collections;
using System.Collections.Generic;
using System.IO;
using UnityEngine;

public class AssetBundleLoader : MonoBehaviour{

	void Start()
	{
		AssetBundle myLoadedAssetBundle = AssetBundle.LoadFromFile(Path.Combine(Application.streamingAssetsPath, "AssetBundles/test"));
		if (myLoadedAssetBundle == null)
		{
			Debug.Log("Failed to load AssetBundle!");
			return;
		}

		GameObject prefab = myLoadedAssetBundle.LoadAsset<GameObject>("william");
		Instantiate(prefab);

		myLoadedAssetBundle.Unload(false);
	}
}
