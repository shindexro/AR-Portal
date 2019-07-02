<?php
   $users = json_decode(file_get_contents("users.json"), true);
   $file = fopen("users.json",'w+');
   if (isset($_POST['name']) && isset($_POST['user']) && isset($_POST['pass'])){
     if($_POST['pass'] == $users[$_POST['user']]["password"]){
       $users[$_POST['user']]["worlds"] = array_diff($users[$_POST['user']]["worlds"],[$_POST['name']]);
       echo "ture";
     }
   }
   fwrite($file, json_encode($users));
   fclose($file);
?>
