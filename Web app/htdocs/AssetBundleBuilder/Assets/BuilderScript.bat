@echo on
title Unity AssetBundle Builder

rem Command usage:
rem Create an AssetBundle with all .fbx models in the input directory (non-recursive)

rem Command format:
rem BuilderScript.bat {inputDirectory} {outputDirectory}

rem Example command:
rem BuilderScript.bat "Assets/Input" "Assets/AssetBundles"

"C:\Program Files\Unity\Editor\Unity.exe" -quit -batchmode -executeMethod ModelImporter.CreateAssetBundle %*