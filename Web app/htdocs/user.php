<head>
  <title>Worlds</title>
  <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
  <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
  <link href="stylesheet.css" rel="stylesheet" />
  <script src="scripts/ListWorlds.js"></script>
  <?php
    if(!isset($_COOKIE["userid"])){
      header('Location: index.php');
    }else{
      $users = json_decode(file_get_contents("users/users.json"), true);
      $file = fopen("users/users.json",'w+');
      if($users[$_COOKIE["userid"]]["sesID"] == $_COOKIE["sesID"]){
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $sesID = '';
        for ($i = 0; $i < 10; $i++) {
            $sesID = $sesID . $chars[random_int(0, 31)];
        }
        $users[$_COOKIE["userid"]]["sesID"] = $sesID;
        setcookie("sesID", $sesID, time() + (86400 * 30), "/");
        setcookie("userid", $_COOKIE["userid"], time() + (86400 * 30), "/");
      }else{
        header('Location: logout.php');
      }
      fwrite($file, json_encode($users));
      fclose($file);
    }
  ?>
</head>
<body>
  <div class="NavBar">
    <div class="NavBarBlock">
      <a href="user.php">
        <img class="navItem" src="logo.svg" height=50px>
      </a>
    </div><div class="NavBarBlock" style="text-align:right ;">
      <a href="user.php"><div class="navButton">My Portals</div></a>
      <a href="logout.php"><div class="navButton">Log Out</div></a>
    </div>
  </div>
  <div class="hidden_label" id="uid"><?php
    $uid = $_COOKIE["userid"];
    echo $uid;
  ?></div>
  <br>
  <div class="altButton" onclick="showNewWorld()">New World</div>
  <div id="world_container"><?php
    $users = json_decode(file_get_contents("users/users.json"), true);
    foreach ($users[$uid]["worlds"] as $key => $value) {
      $world = json_decode(file_get_contents("worlds/" . $value . ".json"), true);
      echo "
      <div class=\"WorldPanel\">
        <div class=\"worldTextPanel\">
          <div class=\"worldNamePanel\"><u>Name</u> : " . $world["name"] . "</div>
          <div class=\"worldDescPanel\"><u>Description</u> : " . $world["description"] . "</div>
          <div class=\"worldNamePanel\"><u>World ID</u> :" . $value  . "</div>
        </div>
        <div class=\"worldButtonPanel\">
          <form class=\"altForm \" action=\"editor.php\"  method=\"post\">
            <input type=\"text\" name=\"room\" value=\"" . $value . "\" style=\"display:none\">
            <input type=\"text\" name=\"pass\" value=\"" . $world["private"] . "\" style=\"display:none\">
            <input class=\"altFormButton\" type=\"submit\" value=\"Edit\">
          </form>
          <div class=\"altWorldButton\" onclick=\"showEdit('" . $value . "', '" . $world["name"] . "', '" . $world["description"] . "', '" . join(",",$world["tags"]) . "', '" . $world["private"] . "')\">Details</div>
          <div class=\"altWorldButton\" onclick=\"showShare('" . $value . "')\">Share</div>
          <div class=\"altWorldButton\" onclick=\"showDelete('" . $value . "', '" . $world["private"] . "')\">Delete</div>
        </div>
      </div>";
    }
  ?></div>
  <div id="panelBack" onclick="hideENW()"></div>
  <div id="editPanel" class="worldEditPanel">
    <input class="EWFormObject" id="EW_Name" type="text" name="" placeholder="Name">
    <textarea rows="4" class="EWTextArea" id="EW_Description" type="text" name="" placeholder="Description"></textarea>
    <textarea rows="4" class="EWTextArea" id="EW_Tags" type="text" name="" placeholder="Seperate tags with commas"></textarea>
    <input class="EWFormObject" id="EW_Private" type="text" name="" placeholder="Enter Privacy Password (Leave Empty for Public)">
    <button class="EWFormObject" onclick="editWorld()" type="button" name="button">Update World</button>
    <button class="EWFormObject" onclick="hideENW()" type="button" name="button">Cancel</button>
  </div>
  <div id="newPanel" class="worldEditPanel">
    <input class="EWFormObject" id="NW_Name" type="text" name="" placeholder="Name">
    <textarea rows="4" class="EWTextArea" id="NW_Description" type="text" name="" placeholder="Description"></textarea>
    <textarea rows="4" class="EWTextArea" id="NW_Tags" type="text" name="" placeholder="Seperate tags with commas"></textarea>
    <input class="EWFormObject" id="NW_Private" type="text" name="" placeholder="Enter Privacy Password (Leave Empty for Public)">
    <button class="EWFormObject" onclick="createWorld()" type="button" name="button">Create World</button>
    <button class="EWFormObject" onclick="hideENW()" type="button" name="button">Cancel</button>
  </div>
  <div id="sharePanel" class="worldEditPanel">
    <textarea rows="4" class="EWTextArea" id="sharEmails" type="text" name="" placeholder="Seperate E-Mail Adresses With Commas, Only the first twenty will be emailed"></textarea>
    <button class="EWFormObject" onclick="shareWorld()" type="button" name="button">Share World</button>
    <button class="EWFormObject" onclick="hideENW()" type="button" name="button">Cancel</button>
  </div>
  <div id="deletePanel" class="worldEditPanel">
    <input class="EWFormObject" id="DWPassword" type="password" name="" placeholder="Enter your password to confirm">
    <button class="EWFormObject" onclick="deleteWorld()" type="button" name="button">Delete World</button>
    <button class="EWFormObject" onclick="hideENW()" type="button" name="button">Cancel</button>
  </div>
</body>
