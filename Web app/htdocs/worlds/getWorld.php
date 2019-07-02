<?php
  if(isset($_POST["wold"])){
    $file = file_get_contents($_POST["wold"] . ".json");
    $arr = json_decode($file, true);
    if($arr["private"] == $_POST["pass"]){
      echo $file;
    }
  }
?>
