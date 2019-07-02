<?php
  if (isset($_POST['name']) && isset($_POST['user']) && isset($_POST['pass']) && isset($_POST['newpass'])){
    if(is_file($_POST['name'] . '/metadata.json')){
      $json = json_encode(array("private"=>$_POST['newpass'], "owner"=>$_POST['user']));
      $arr = json_decode(file_get_contents($_POST["name"] . "/metadata.json"), true);
      if($arr["private"]==$_POST['pass']){
        $file = fopen("C:/xampp/htdocs/AssetBundleBuilder/Assets/Theater/" . $_POST['name'] . '/metadata.json','w+');
        fwrite($file, $json);
        fclose($file);
      }
    }
  }
?>
