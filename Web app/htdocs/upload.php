<?php
$target_dir = "AssetBundleBuilder/Assets/Theater/" . $_POST["wID"] . "/fbx/";
$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

if(isset($_POST["submit"])) {
    $phpFileUploadErrors = array(
      0 => 'There is no error, the file uploaded with success',
      1 => 'The uploaded file is too large',
      2 => 'The uploaded file is too large',
      3 => 'The uploaded file was only partially uploaded',
      4 => 'No file was uploaded',
      6 => 'Missing a temporary folder',
      7 => 'Failed to write file to disk.',
      8 => 'A PHP extension stopped the file upload.',
    );
    if ($_FILES["fileToUpload"]["error"] > 0) {
      echo $phpFileUploadErrors[$_FILES["fileToUpload"]["error"]];
      $uploadOk = 0;
    }
    if($imageFileType != "fbx") {
      echo "Sorry, only FBX files are allowed.";
      $uploadOk = 0;
    }
    if (file_exists($target_file)) {
      echo "Sorry, file already exists.";
      $uploadOk = 0;
    }
}

if ($uploadOk == 0) {
    echo "<br>Sorry, your file was not uploaded.";
} else {
    if(move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
        echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded. Please refresh the editor to view it.";
    } else {
        echo "Sorry, there was an unkown error uploading your file";
    }
}
?>
<script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
<script>
$(document).ready(function() {
  $.ajax({
    type: "POST",
    url: "AssetBundleBuilder/createAssetBundle.php",
    data: {
      wold:"<?php echo $_POST["wID"] ?>"
    }
  });
})
</script>
