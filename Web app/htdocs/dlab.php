<?php
  if(isset($_POST["wold"])){
    $worldPass;
    if(isset($_POST["pass"])){
      $worldPass = $_POST["pass"];
    }else{
      $worldPass = "";
    }
    $file = file_get_contents("worlds/" . $_POST["wold"] . ".json");
    $arr = json_decode($file, true);
    if($arr["private"] == $worldPass){
      $path = "AssetBundleBuilder/Assets/Theater/" . $_POST["wold"] . "/assetbundle/assetbundle";
      if(file_exists($path)) {
          header('Content-Description: File Transfer');
          header('Content-Type: application/octet-stream');
          header('Content-Disposition: attachment; filename="'.basename($path).'"');
          header('Expires: 0');
          header('Cache-Control: must-revalidate');
          header('Pragma: public');
          header('Content-Length: ' . filesize($path));
          flush();
          readfile($path);
          exit;
      }else{
        echo "{error:'The file you are looking for has not been found'}";
      }
    }
  }else{
    echo "{error:'Invalid credentials'}";
  }
?>
