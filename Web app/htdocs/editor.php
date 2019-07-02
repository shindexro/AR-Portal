<head>
  <title>World Editor</title>
  <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
  <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
  <link href="stylesheet.css" rel="stylesheet" />
</head>
<?php
  if(!isset($_COOKIE["userid"])){
    header('Location: index.php');
  }elseif(!isset($_POST["room"])){
    header('Location: user.php');
  }else{
    $users = json_decode(file_get_contents("users/users.json"), true);
    $file = fopen("users/users.json",'w+');
    if($users[$_COOKIE["userid"]]["sesID"] == $_COOKIE["sesID"]){
      if(in_array($_POST["room"], $users[$_COOKIE["userid"]]["worlds"])){
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $sesID = '';
        for ($i = 0; $i < 10; $i++) {
            $sesID = $sesID . $chars[random_int(0, 31)];
        }
        $users[$_COOKIE["userid"]]["sesID"] = $sesID;
        setcookie("sesID", $sesID, time() + (86400 * 30), "/");
        setcookie("userid", $_COOKIE["userid"], time() + (86400 * 30), "/");
      }else{
        header('Location: user.php');
      }
    }else{
      header('Location: logout.php');
    }
    fwrite($file, json_encode($users));
    fclose($file);
  }
?>
<body>
  <script src="scripts/three.js"></script>
  <script src="scripts/OrbitControls.js"></script>
  <script src="scripts/TransformControls.js"></script>
  <script src="scripts/inflate.min.js"></script>
  <script src="scripts/FBXLoader.js"></script>
  <script src="scripts/WebGL.js"></script>
  <script src="scripts/dat.gui.min.js"></script>
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
  <div id="editorCanvas" onmouseup="updateActive()"> </div><div id="editorControls">
    <div id="panelSelector">
      <div class="panelButton" id="tab0" onclick="selectPanel(0)">Active Object</div>
      <div class="panelButton" id="tab1" onclick="selectPanel(1)">Objects</div>
      <div class="panelButton" id="tab2" onclick="selectPanel(2)">Assets</div>
      <div class="panelButton" id="tab3" onclick="selectPanel(3)">Upload</div>
      <div class="panelButton" id="tab4" onclick="selectPanel(4)">Controls</div>
    </div>
    <div class="controlPanel" id="panel0">
      <div class="dataLabel">X-coordinate : </div><input id="xCord" name="xCord" type="number" class="dataFeild">
      <div class="dataLabel">Y-coordinate : </div><input id="yCord" name="yCord" type="number" class="dataFeild">
      <div class="dataLabel">Z-coordinate : </div><input id="zCord" name="zCord" type="number" class="dataFeild">
      <div class="dataLabel">X-scale : </div><input id="xScale" name="xScale" type="number" class="dataFeild">
      <div class="dataLabel">Y-scale : </div><input id="yScale" name="yScale" type="number" class="dataFeild">
      <div class="dataLabel">Z-scale : </div><input id="zScale" name="zScale" type="number" class="dataFeild">
      <div class="dataLabel">X-rotation : </div><input id="xRot" name="xRot" type="number" class="dataFeild">
      <div class="dataLabel">Y-rotation : </div><input id="yRot" name="yRot" type="number" class="dataFeild">
      <div class="dataLabel">Z-rotation : </div><input id="zRot" name="zRot" type="number" class="dataFeild">
      <div class="dataLabel">Script : </div><select name="script" class="dataFeild" id="action_selector">
        <option value="0">No Script</option>
        <option value="1">360° Video</option>
      </select>
      <div class="dataLabel" id="vidURLLabel">360° Video URL : </div><input name="vidURL" type="text" class="dataFeild" id="ivdURLValue">
      <div class="altPanelButton" onclick="updateObject()">Update Object</div>
      <div class="altPanelButton" onclick="deleteActive()">Delete Object</div>
    </div>
    <div class="controlPanel" id="panel1"></div>
    <div class="controlPanel" id="panel2">
      <?php
          $files2 = scandir("models/fbx/");
          $files1 = array_diff($files2, array('.', '..'));
          foreach ($files1 as $value) {
            echo "<div class=\"altPanelButton\" onclick=\"loadFBX('models/fbx/" . $value . "','" . $value . "','0')\">" . $value . "</div>";
          }
      ?>
    </div>
    <div class="controlPanel" id="panel3">
      <form action="upload.php" method="post" enctype="multipart/form-data" target="_blank">
        Select 3D Model to upload (Only FBX binary version 7400 or later supported)
        <input type="file" name="fileToUpload" id="fileToUpload">
        <input type="submit" value="Upload FBX" name="submit">
        <input name="wID" type="text" value="<?php echo $_POST["room"] ?>" style="display:none;">
      </form>
      <br><br>
      <?php
          $files2 = scandir("AssetBundleBuilder/Assets/Theater/" . $_POST["room"] . "/fbx/");
          $files1 = array_diff($files2, array('.', '..'));
          foreach ($files1 as $value) {
            if(strpos($value, '.meta') == false){
              echo "<div class=\"altPanelButton\" onclick=\"loadFBX('AssetBundleBuilder/Assets/Theater/" . $_POST["room"] . "/fbx/" . $value . "','" . $value . "','1')\">" . $value . "</div>";
            }
          }
      ?>
    </div>
    <div class="controlPanel" id="panel4">
      w : move x,y,z<br>
      e : scale x,y,z<br>
      r : rotate x,y,z<br>
      x : show x control<br>
      y : show y control<br>
      z : show z control<br>
      [space] : lock controls<br>
      [ctrl] : snap to grid<br>
      [ : decrement model<br>
      ] : increment model<br>
      - : reduce size of controls<br>
      + : increase size of controls
    </div>
  </div>
  <input name="wID" type="text" value="<?php echo $_POST["room"] ?>" class="dataFeild" id="worldID" style="display:none;">
  <input name="wPass" type="password" value="<?php echo $_POST["pass"] ?>" class="dataFeild" id="worldPass" style="display:none;">
  <div class="altButton" onclick="generateSave()" id="saveButton">Save</div>
  <script src="scripts/editor.js"></script>
</body>
