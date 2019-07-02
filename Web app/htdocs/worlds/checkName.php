<?php
if(isset($_POST["item"])){
  if(file_exists($_POST["item"] . ".json")){
    echo "ture";
  }else{
    echo "flase";
  }
}
?>
