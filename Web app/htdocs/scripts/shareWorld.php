<?php
  if(isset($_POST['adresses']) && isset($_POST['wold'])){
    $recips = json_decode($_POST['adresses']);
    if($recips != null){
      if(gettype($recips) == 'array'){
        $trimrecips = array_slice($recips, 0, 20);
        $cleanrecips = array();
        foreach ($trimrecips as &$value) {
          if(gettype($value) == 'string'){
            if(filter_var($value, FILTER_VALIDATE_EMAIL)){
              array_push($cleanrecips,$value);
            }
          }
        }
        foreach($cleanrecips as &$value){
          mail($value,"You have been invited to view a new world!","Hello,\n\nYou have been invited to view a new world using the AR Portal!\n\nThe world code is : ".$_POST['wold']."\n\nPlase contact the owner of this world if a password is needed to access this world.\n\nCheers!\nAR Portal");
        }
      }
    }
  }
?>
