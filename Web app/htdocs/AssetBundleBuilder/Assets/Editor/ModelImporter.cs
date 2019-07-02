using System.IO;
using UnityEditor;
using UnityEngine;

public class ModelImporter
{
    public static void CreateAssetBundle()
    {
        Debug.Log("Building AssetBundle");
        string[] args = System.Environment.GetCommandLineArgs();
        if (args.Length < 6) return;

        string inputPath = args[5];
        string outputPath = args[6];

        foreach (string filePath in Directory.GetFiles(inputPath, "*.fbx"))
        {
            Debug.Log("Tagging " + filePath);
            AssetImporter.GetAtPath(filePath).SetAssetBundleNameAndVariant("myAssetBundle", "");
        }

        BuildAllAssetBundles(outputPath);
        Debug.Log("Finish Building AssetBundle");
    }

    [MenuItem("Test/Build AssetBundle")]
    public static void BuildAllAssetBundles(string outputPath)
    {
        BuildPipeline.BuildAssetBundles(outputPath, BuildAssetBundleOptions.None, BuildTarget.Android);
    }
}
