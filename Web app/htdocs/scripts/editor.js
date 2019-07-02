if (WEBGL.isWebGLAvailable() === false) {
  document.body.appendChild(WEBGL.getWebGLErrorMessage());
}

var camera, scene, renderer, control, orbit;
var models, activeModel, world, items;
var watching = 0;

init();
render();

function init() {
  models = [];
  setupRenderer();
  setupCamera();
  scene = new THREE.Scene();
  scene.background = new THREE.Color(0xE0E0E0);
  addGrid();
  buildRoom();
  addSceneLight();
  addOrbitControls();
  addKeyboardControls();

  control = new THREE.TransformControls(camera, renderer.domElement);
  control.addEventListener('change', render);
  control.addEventListener('dragging-changed', function(event) {
    orbit.enabled = !event.value;
  });

  window.addEventListener('resize', onWindowResize, false);
}
function render() {
  renderer.render(scene, camera);
}
function buildRoom() {
  var geometry = new THREE.PlaneGeometry(100, 100, 32);
  var material = new THREE.MeshBasicMaterial({
    color: 0xffffff,
    side: THREE.DoubleSide
  });
  var plane = new THREE.Mesh(geometry, material);
  scene.add(plane);
  var loader = new THREE.FBXLoader();
  loader.load( 'models/portalFrame.fbx', function ( object ) {
    object.position["z"] = -500;
    scene.add( object );
  }, undefined, function ( e ) {
    console.error( e );
  } );
  scene.children.splice(1,1);
}
function loadFBX(file, name, type) {
  var loader = new THREE.FBXLoader();
  loader.load(file, function(object) {
    var newObj = {
      name: name,
      object: object,
      script: 0,
      url: "",
      type: type
    }
    models.push(newObj);
    var objNum = models.length - 1;
    var node = document.createElement("div");
    var textnode = document.createTextNode("Item #" + (objNum + 1) + " : " + name);
    node.appendChild(textnode);
    node.classList.add("altPanelButton");
    document.getElementById("panel1").appendChild(node);
    node.setAttribute("onclick","setModel(" + objNum + ");");
    setModel(objNum);
    updateActive();
  }, undefined, function(e) {
    console.error(e);
  });
}
function downloadFBX(file,name,posX,posY,posZ,rotX,rotY,rotZ,scaleX,scaleY,scaleZ,script,url,type) {
  var loader = new THREE.FBXLoader();
  loader.load(file, function(object) {
    var newObj = {
      name: name,
      object: object,
      script: script,
      url: url,
      type: type
    }
    models.push(newObj);
    var objNum = models.length - 1
    var node = document.createElement("div");
    var textnode = document.createTextNode("Item #" + (objNum + 1) + " : " + name);
    node.appendChild(textnode);
    document.getElementById("panel1").appendChild(node);
    node.classList.add("altPanelButton");
    node.setAttribute("onclick","setModel(" + objNum + ");");
    setModel(objNum);
    console.log(file);
    object["position"]["x"] = posX * 100;
    object["position"]["y"] = posY * 100;
    object["position"]["z"] = (posZ - 5) * 100;
    object["rotateX"](rotX);
    object["rotateY"](rotY);
    object["rotateZ"](rotZ);
    object["scale"]["x"] = scaleX;
    object["scale"]["y"] = scaleY;
    object["scale"]["z"] = scaleZ;
    updateActive();
    render();
  }, undefined, function(e) {
    console.error(e);
  });
}
function incrementModels(){
  activeModel = activeModel + 1;
  if(!(activeModel < models.length)){
    activemodel = models.length - 1;
  }
  setModel(activeModel);

}
function decrementModels(){
  activeModel = activeModel - 1;
  if(activeModel < 0){
    activeModel = 0;
  }
  setModel(activeModel);
}
function setModel(modelNum){
  activeModel = modelNum;
  scene.add(models[modelNum]["object"]);
  control.attach(models[modelNum]["object"]);
  scene.add(control);
  watching = modelNum;
  updateActive()
}
function setupRenderer() {
  renderer = new THREE.WebGLRenderer();
  renderer.setPixelRatio(window.devicePixelRatio);
  renderer.setSize($("#editorCanvas").width(), $("#editorCanvas").height());
  renderer.shadowMap.enabled = true;
  document.getElementById("editorCanvas").appendChild(renderer.domElement);
}
function setupCamera() {
  camera = new THREE.PerspectiveCamera(50, window.innerWidth / window.innerHeight, 1, 3000);
  camera.position.set(750, 500, 750);
  camera.lookAt(0, 200, 0);
}
function addGrid() {
  var grid = new THREE.GridHelper(1000, 20, 0x000000, 0x000000);
  grid.material.opacity = 0.2;
  grid.material.transparent = true;
  scene.add(grid);
}
function addSceneLight() {
  scene.add(new THREE.AmbientLight(0xf0f0f0));
  var light = new THREE.SpotLight(0xffffff, 1.5);
  light.position.set(0, 1500, 200);
  light.castShadow = true;
  light.shadow = new THREE.LightShadow(new THREE.PerspectiveCamera(70, 1, 200, 2000));
  light.shadow.bias = -0.000222;
  light.shadow.mapSize.width = 1024;
  light.shadow.mapSize.height = 1024;
  scene.add(light);
}
function addOrbitControls() {
  orbit = new THREE.OrbitControls(camera, renderer.domElement);
  orbit.update();
  orbit.addEventListener('change', render);
}
function addKeyboardControls() {
  window.addEventListener('keydown', function(event) {
    switch (event.keyCode) {
      case 17: // Ctrl
        control.setTranslationSnap(100);
        control.setRotationSnap(THREE.Math.degToRad(15));
        break;
      case 87: // W
        control.setMode("translate");
        break;
      case 69: // E
        control.setMode("scale");
        break;
      case 82: // R
        control.setMode("rotate");
        break;
      case 187:
      case 107: // +, =, num+
        control.setSize(control.size + 0.1);
        break;
      case 189:
      case 109: // -, _, num-
        control.setSize(Math.max(control.size - 0.1, 0.1));
        break;
      case 88: // X
        control.showX = !control.showX;
        break;
      case 89: // Y
        control.showY = !control.showY;
        break;
      case 90: // Z
        control.showZ = !control.showZ;
        break;
      case 32: // Spacebar
        control.enabled = !control.enabled;
        break;
      case 219: // [
        decrementModels();
        break;
      case 221: // ]
        incrementModels();
        break;
    }
  });
  window.addEventListener('keyup', function(event) {
    switch (event.keyCode) {
      case 17: // Ctrl
        control.setTranslationSnap(null);
        control.setRotationSnap(null);
        break;
    }
  });
}
function onWindowResize() {
  camera.aspect = window.innerWidth / window.innerHeight;
  camera.updateProjectionMatrix();
  renderer.setSize($("#editorCanvas").width(), $("#editorCanvas").height());
  render();
}
function checkChild(){
  if($("#action_selector").val() == 1){
    $("#vidURLLabel").show();
    $("#ivdURLValue").show();
  }else{
    $("#vidURLLabel").hide();
    $("#ivdURLValue").hide();
    $("#ivdURLValue").val("");
  }
}
function updateActive(){
  if(models.length > 0){
    $("#xCord").val(models[activeModel]["object"]["position"]["x"]/100);
    $("#yCord").val(models[activeModel]["object"]["position"]["y"]/100);
    $("#zCord").val(models[activeModel]["object"]["position"]["z"]/100);
    $("#xScale").val(models[activeModel]["object"]["scale"]["x"]);
    $("#yScale").val(models[activeModel]["object"]["scale"]["y"]);
    $("#zScale").val(models[activeModel]["object"]["scale"]["z"]);
    $("#xRot").val(models[activeModel]["object"]["rotation"]["_x"]);
    $("#yRot").val(models[activeModel]["object"]["rotation"]["_y"]);
    $("#zRot").val(models[activeModel]["object"]["rotation"]["_z"]);
    $("#action_selector").val(models[activeModel]["script"]);
    $("#ivdURLValue").val(models[activeModel]["url"]);
  }
}
function updateObject(){
  models[activeModel]["object"]["position"]["x"] = $("#xCord").val() * 100;
  models[activeModel]["object"]["position"]["y"] = $("#yCord").val() * 100;
  models[activeModel]["object"]["position"]["z"] = $("#zCord").val() * 100;
  models[activeModel]["object"]["scale"]["x"] = $("#xScale").val();
  models[activeModel]["object"]["scale"]["y"] = $("#yScale").val();
  models[activeModel]["object"]["scale"]["z"] = $("#zScale").val();
  models[activeModel]["object"]["rotateX"]($("#xRot").val() - models[activeModel]["object"]["rotation"]["_x"]);
  models[activeModel]["object"]["rotateY"]($("#yRot").val() - models[activeModel]["object"]["rotation"]["_y"]);
  models[activeModel]["object"]["rotateZ"]($("#zRot").val() - models[activeModel]["object"]["rotation"]["_z"]);
  models[activeModel]["script"] = $("#action_selector").val();
  models[activeModel]["url"] = $("#ivdURLValue").val();
  render();
}
function readWorldName() {
  return $("#worldID").val();
}
function readWorldPass() {
  return $("#worldPass").val();
}
function generateSave(){
  world["objects"] = [];
  $.each(models,function(index, value){
    var object = {
      model:value["name"],
      posX:value["object"]["position"]["x"]/100,
      posY:value["object"]["position"]["y"]/100,
      posZ:(value["object"]["position"]["z"]/100) + 5,
      rotX:value["object"]["rotation"]["_x"],
      rotY:value["object"]["rotation"]["_y"],
      rotZ:value["object"]["rotation"]["_z"],
      scaleX:value["object"]["scale"]["x"],
      scaleY:value["object"]["scale"]["y"],
      scaleZ:value["object"]["scale"]["z"],
      action:value["script"],
      url:value["url"],
      type:value["type"]
    }
    world["objects"].push(object);
  })
  console.log(JSON.stringify(world));
  $.ajax({
    type: 'POST',
    url: "worlds/updateWorld.php",
    data: {
      "json" : JSON.stringify(world),
      "wold":readWorldName(),
      "pass":readWorldPass()
    },
    async:false
  });
}
function deleteActive(){
  scene.children.forEach(function(element,index){
    if(element["uuid"] == models[activeModel]["object"]["uuid"]){
      scene.children.splice(index,1);
    }
  });
  models.splice(activeModel,1);
  items.splice(activeModel,1);
  $("#panel1").html("");

  $.each(items, function(index, value){
    $("#panel1").append("<div class=\"altPanelButton\" onclick=\"setModel(" + index + ");\">Item #" + (index + 1) + " : " + value["model"] + "</div>");
  });

  setModel(0);
  render();
}
function downloadSave(){
  $.ajax({
    type: "POST",
    url: "worlds/getWorld.php",
    data: {
      wold:readWorldName(),
      pass:readWorldPass()
    },
    async: false,
    success: function(value){
      world = JSON.parse(value);
      items = world["objects"];
    }
  });
  $.each(items, function(index, value){
    if(value["type"] == 0){
      downloadFBX("models/fbx/" + value['model'], value['model'],value["posX"],value["posY"],value["posZ"],value["rotX"],value["rotY"],value["rotZ"],value["scaleX"],value["scaleY"],value["scaleZ"],value["action"],value["url"],value["type"]);
    }else{
      downloadFBX("AssetBundleBuilder/Assets/Theater/" + readWorldName() + "/fbx/" + value['model'], value['model'],value["posX"],value["posY"],value["posZ"],value["rotX"],value["rotY"],value["rotZ"],value["scaleX"],value["scaleY"],value["scaleZ"],value["action"],value["url"],value["type"]);
    }
  });
}
function selectPanel(i){
  $(".panelButton").removeClass("selectedButton");
  $(".controlPanel").hide();
  if($("#tab" + i).length){
    $("#tab" + i).addClass("selectedButton");
    $("#panel" + i).show();
  }
}

$(document).ready(function() {
  $.get("/models/fbx", function(data) {
    $("#asset").append(data);
  });
  $("#action_selector").change(checkChild);
  downloadSave();
  $(".controlPanel").hide();
  $("#tab0").addClass("selectedButton");
  $("#panel0").show();

})
