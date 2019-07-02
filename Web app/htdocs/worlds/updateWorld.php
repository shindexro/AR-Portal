<?php
  if(isset($_POST['json']) && isset($_POST['wold'])){
    $json = $_POST['json'];
    if (json_decode($json) != null){
      $arr = json_decode(file_get_contents($_POST["wold"] . ".json"), true);
      if($arr["private"] == $_POST["pass"]){
        $file = fopen($_POST['wold'] . '.json','w+');
        fwrite($file, $json);
        fclose($file);
      }
    }
  }
?>
