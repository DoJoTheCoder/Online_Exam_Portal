// webcam permission
let video = document.getElementById("video");
let cameraBtn = document.getElementById("cameraStart");
let cameraAccessMsg = document.getElementById("webcamStatus");
let cameraPermission = false;

cameraBtn.addEventListener('click', function() {
    navigator.mediaDevices.getUserMedia({ video: true, audio: false })
    .then(function(stream){
        video.srcObject = stream;
        cameraAccessMsg.innerHTML = '<i class="bi-check-circle text-success">&nbsp;Webcam access enabled</i>';
        cameraPermission = true;
        startTestBtnEnable();
    });
});

navigator.permissions.query({name:'camera'}).then(function(permissionStatus) {
    // console.log('camera permission status is ', permissionStatus.state);
    permissionStatus.onchange = function() {
        if(this.state =='granted'){
            cameraAccessMsg.innerHTML = '<i class="bi-check-circle text-success">&nbsp;Webcam access enabled</i>';
            cameraPermission = true;
        }
        else{
            cameraAccessMsg.innerHTML = '<i class="bi-exclamation-circle text-danger">&nbsp;Webcam access disabled</i>';
            cameraPermission = false;
        }
        startTestBtnEnable();
    };
});

// Geo location Permission 
let geolocStart = document.getElementById("geolocStart");
let geolocAccessMsg = document.getElementById("geolocStatus");
let latitude = document.getElementById("geolocFeedbackLatitude");
let longitude = document.getElementById("geolocFeedbackLongitude");
let geolocPermission = false;

geolocStart.addEventListener('click', function() {
    if(navigator.geolocation){
        navigator.geolocation.getCurrentPosition(showPosition, showError);
        navigator.permissions.query({name:'geolocation'}).then(function(permissionStatus) {
            if(permissionStatus.state =='granted'){
            geolocAccessMsg.innerHTML = '<i class="bi-check-circle text-success">&nbsp;Geo-location access enabled</i>';
            geolocPermission = true;
        }
        else{
            geolocAccessMsg.innerHTML = '<i class="bi-exclamation-circle text-danger">&nbsp;Geo-location access disabled</i>';
            geolocPermission = false;
        }
        });
        startTestBtnEnable();
    }
});

function showPosition(position) {
    latitude.innerHTML = "Latitude: " + position.coords.latitude;
    longitude.innerHTML = "Longitude: " + position.coords.longitude;
}

function showError() {
    latitude.innerHTML = "Latitude: Unknown";
    longitude.innerHTML = "Longitude: Unknown";
}

navigator.permissions.query({name:'geolocation'}).then(function(permissionStatus) {
    // console.log('initial geo location status:', permissionStatus.state);
    permissionStatus.onchange = function() {
        if(this.state =='granted'){
            geolocAccessMsg.innerHTML = '<i class="bi-check-circle text-success">&nbsp;Geo-location access enabled</i>';
            geolocPermission = true;
        }
        else{
            geolocAccessMsg.innerHTML = '<i class="bi-exclamation-circle text-danger">&nbsp;Geo-location access disabled</i>';
            geolocPermission = false;
        }
        startTestBtnEnable();
    };
});

// agreement permission and start test button
let agreementCheckbox = document.getElementById('agreementCheckbox');
let startTestBtn = document.getElementById("startTestBtn");

agreementCheckbox.onchange = function(){startTestBtnEnable()};

function startTestBtnEnable(){
    // console.log('agreement checkbox status:', agreementCheckbox.checked);
    // console.log('camera permission status:', cameraPermission);
    // console.log('geo-location permission status:', geolocPermission);
    if(cameraPermission && agreementCheckbox.checked && geolocPermission){
        startTestBtn.disabled = false;
    }
    else{
        startTestBtn.disabled = true;
    }
}                        