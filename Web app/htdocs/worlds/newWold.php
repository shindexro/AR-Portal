<?php
  if(isset($_POST['wold']) && isset($_POST['json'])){
    if($_POST['wold'] != ""){
       $json = $_POST['json'];

       if (json_decode($json) != null)
       {
           $file = fopen($_POST['wold'] . '.json','w+');
           fwrite($file, $json);
           fclose($file);
       }
    }
  }
?>
