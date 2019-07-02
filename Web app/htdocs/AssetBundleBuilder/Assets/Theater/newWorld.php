<?php
  if(isset($_POST['name']) && isset($_POST['user']) && isset($_POST['pass'])){
    if(($_POST['name'] != "") && ($_POST['user'] != "")){
      $json = json_encode(array("private"=>$_POST['pass'], "owner"=>$_POST['user']));

      mkdir($_POST['name']);
      mkdir($_POST['name']. "/fbx");
      mkdir($_POST['name']. "/assetbundle");
      $file = fopen($_POST['name'] . '/metadata.json','w+');
      fwrite($file, $json);
      fclose($file);
    }
  }
?>
