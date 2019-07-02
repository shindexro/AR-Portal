<?php
  if(isset($_POST["wold"])){
    exec("Assets\\BuilderScript.bat \"Assets/Theater/" . $_POST["wold"] . "/fbx\" \"Assets/Theater/" . $_POST["wold"] . "/assetbundle\"");
  }else{
    exec("Assets\\BuilderScript.bat \"Assets/Theater/" . $_GET["wold"] . "/fbx\" \"Assets/Theater/" . $_GET["wold"] . "/assetbundle\"");
  }

?>
