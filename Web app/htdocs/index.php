<head>
  <title>temp</title>
  <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
  <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
  <link href="stylesheet.css" rel="stylesheet" />
  <?php
    if(isset($_COOKIE["sesID"])){
      header('Location: user.php');
    }else{
      if(isset($_POST["username"])){
        $users = json_decode(file_get_contents("users/users.json"), true);
        foreach($users as $key => $value) {
          if(($users[$key]["username"] == $_POST["username"]) && ($users[$key]["password"] == $_POST["password"])){
            $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
            $sesID = '';
            for ($i = 0; $i < 10; $i++) {
                $sesID = $sesID . $chars[random_int(0, 31)];
            }
            $users[$key]["sesID"] = $sesID;
            $file = fopen("users/users.json",'w+');
            fwrite($file, json_encode($users));
            fclose($file);
            setcookie("userid", $key, time() + (86400 * 30), "/");
            setcookie("sesID", $sesID, time() + (86400 * 30), "/");
            header('Location: user.php');
            break;
          }
        }
      }
    }
  ?>
</head>
<body>
  <div class="NavBar">
    <div class="NavBarBlock">
        <img class="navItem" src="logo.svg" height=50px>
    </div><div class="NavBarBlock" style="text-align:right ;">
    </div>
  </div>
  <div class="loginContainer">
    <form class="" action="" method="post">
      <input class="LoginField" type="text" name="username" placeholder="Username">
      <input class="LoginField" type="password" name="password" placeholder="Password">
      <input class="LoginButton" type="submit" value="Submit">
    </form>
  </div>
</body>
