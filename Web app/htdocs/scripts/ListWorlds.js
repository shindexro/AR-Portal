var worlds;
var editWorldId;
var editWorldPass;
var userID;
function hideENW(){
  $("#panelBack").hide();
  $(".worldEditPanel").hide();
}
function doesExist(i){
  var temp = {};
  $.ajax({
    type: "POST",
    url: "worlds/checkName.php",
    data: {"item":i},
    async: false,
    success: function(value){
      temp = value;
    }
  });
  return temp;
}
function worldName() {
  var text = "";
  var possible = "abcdefghijklmnopqrstuvwxyz0123456789";
  for (var i = 0; i < 8; i++){
    text += possible.charAt(Math.floor(Math.random() * possible.length));
  }
  if(doesExist(text) == "ture"){
    return worldName();
  }else{
    return text;
  }
}
function getOneWold(){
  var temp = {};
  $.ajax({
    type: "POST",
    url: "worlds/getWorld.php",
    data: {
      wold:editWorldId,
      pass:editWorldPass
    },
    async: false,
    success: function(value){
      temp = JSON.parse(value);
    }
  });
  return temp;
}
function createWorld(){
  var tags = $("#NW_Tags").val().split(",");
  var uniqueTags = [];
  var worldID = worldName();
  $.each(tags, function(index, value){
    tags[index] = value.trim()
    if($.inArray(tags[index], uniqueTags) === -1) uniqueTags.push(tags[index]);
  });
  var newWorld = {
    "name" : $("#NW_Name").val(),
    "description" : $("#NW_Description").val(),
    "tags" : uniqueTags,
    "private": $("#NW_Private").val(),
    "objects" : [],
    "owner" : userID
  }
  $.ajax({
    type: 'POST',
    url: "worlds/newWold.php",
    data: {
      json : JSON.stringify(newWorld),
      wold: worldID
    },
    async:false
  });
  $.ajax({
    type: 'POST',
    url: "users/newWorld.php",
    data: {
      name: worldID,
      user:userID
    },
    async:false
  });
  $.ajax({
    type: 'POST',
    url: "AssetBundleBuilder/Assets/Theater/newWorld.php",
    data: {
      name: worldID,
      user:userID,
      pass:$("#NW_Private").val()
    },
    async:false
  });
  hideENW();
  location.reload();
}
function showNewWorld(){
  $("#panelBack").show();
  $("#newPanel").show();
}
function editWorld(){
  var currentWorld = getOneWold();
  currentWorld["name"] = $("#EW_Name").val();
  currentWorld["description"] = $("#EW_Description").val();
  currentWorld["private"] = $("#EW_Private").val();
  var tags = $("#EW_Tags").val().split(",");
  var uniqueTags = [];
  $.each(tags, function(index, value){
    tags[index] = value.trim()
    if($.inArray(tags[index], uniqueTags) === -1) uniqueTags.push(tags[index]);
  });
  currentWorld["tags"] = uniqueTags;
  console.log(editWorldId);
  console.log(editWorldPass);
  console.log(JSON.stringify(currentWorld));
  $.ajax({
    type: 'POST',
    url: "worlds/updateWorld.php",
    data: {
      json : JSON.stringify(currentWorld),
      wold:editWorldId,
      pass:editWorldPass,
      user:userID
    },
    async:false
  });
  $.ajax({
    type: 'POST',
    url: "AssetBundleBuilder/Assets/Theater/updatepass.php",
    data: {
      newpass : $("#EW_Private").val(),
      name: editWorldId,
      pass:editWorldPass,
      user:userID
    },
    async:false
  });
  hideENW();
  location.reload();
}
function showEdit(a,b,c,d,e){
  editWorldId = a;
  $("#EW_Name").val(b);
  $("#EW_Description").val(c);
  $("#EW_Tags").val(d);
  editWorldPass = e;
  $("#EW_Private").val(e);
  $("#panelBack").show();
  $("#editPanel").show();
}
function showShare(a){
  editWorldId = a;
  $("#panelBack").show();
  $("#sharePanel").show();
}
function shareWorld(){
  var tags = $("#sharEmails").val().split(",");
  var uniqueTags = [];
  var hold;
  $.each(tags, function(index, value){
    if(index>19){
      return false;
    }else{
      hold = value.trim();
      if(/(?:[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*|"(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21\x23-\x5b\x5d-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])*")@(?:(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?|\[(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?|[a-z0-9-]*[a-z0-9]:(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21-\x5a\x53-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])+)\])/.test(hold)){
        if(!uniqueTags.includes(hold)){
          uniqueTags.push(hold);
        }
      }
    }
  });
  $.ajax({
    type: "POST",
    url: "scripts/shareWorld.php",
    data: {
      wold:editWorldId,
      adresses:JSON.stringify(uniqueTags)
    }
  });
  hideENW();
}
function showDelete(a,b){
  editWorldId = a;
  editWorldPass = b;
  $("#panelBack").show();
  $("#deletePanel").show();
}
function deleteWorld(){
  var valid;
  $.ajax({
    type: 'POST',
    url: "users/deleteWorld.php",
    data: {
      name:editWorldId,
      user:userID,
      pass:$("#DWPassword").val()
    },
    async:false,
    success: function(value){
      valid = value;
    }
  });
  if(valid == "ture"){
    $.ajax({
      type: 'POST',
      url: "AssetBundleBuilder/Assets/Theater/deleteWorld.php",
      data: {
        name: editWorldId,
        user:userID,
        pass:editWorldPass
      },
      async:false,
      success: function(value){
        console.log(value);
      }
    });
    $.ajax({
      type: 'POST',
      url: "worlds/deleteWorld.php",
      data: {
        name: editWorldId,
        user:userID,
        pass:editWorldPass
      },
      async:false
    });
  }
  location.reload();
}
$(function() {
  userID = $("#uid").text();
  hideENW();
});
