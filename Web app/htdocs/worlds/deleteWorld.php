<?php
   if(isset($_POST["name"]) && isset($_POST["pass"])){
     $arr = json_decode(file_get_contents($_POST["wold"] . ".json"), true);
     if(($arr["private"]==$_POST['pass']) && ($arr["owner"]==$_POST['user'])){
       unlink($_POST["name"] . ".json");
     }
   }
?>
