<?php
  if(isset($_POST["item"])){
    $users = json_decode(file_get_contents("users.json"), true);
    echo json_encode($users[$_POST["item"]]["worlds"]);
  }
?>
