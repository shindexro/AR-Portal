<?php
   if(isset($_POST['user']) && isset($_POST['name'])){
     if(($_POST['user'] != "") && ($_POST['name'] != "")){
       $users = json_decode(file_get_contents("users.json"), true);
       $file = fopen("users.json",'w+');
       array_push($users[$_POST['user']]["worlds"],$_POST['name']);
       fwrite($file, json_encode($users));
       fclose($file);
     }
   }
?>
