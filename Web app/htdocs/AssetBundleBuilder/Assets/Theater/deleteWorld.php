<?php
  function deleteDirectory($i){
    $files2 = scandir($i); // get all file names'
    $files1 = array_diff($files2, array('.', '..'));
    foreach($files1 as $file){ // iterate files
      if(is_file($i.$file)){
        unlink($i.$file);
      }else{
        deleteDirectory($i.$file."/");
      }
    }
    rmdir($i);
  }
  if (isset($_POST['name']) && isset($_POST['user']) && isset($_POST['pass'])){
    $arr = json_decode(file_get_contents($_POST["name"] . "/metadata.json"), true);
    if(($arr["private"]==$_POST['pass']) && ($arr["owner"]==$_POST['user'])){
      deleteDirectory($_POST["name"] . "/");
      rmdir($_POST["name"] . "/");
    }
  }
?>
