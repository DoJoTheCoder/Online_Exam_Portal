let video = document.getElementById("video");
let canvas = document.getElementById('canvas');
let fullscreenModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('fullscreenModal'));
let permissionModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('permissionModal'));
let fullbtn = document.getElementById('fullbtn');
let permissionbtn = document.getElementById('permissionbtn');

let latitude = document.getElementById('geolocFeedbackLatitude'); 
let longitude = document.getElementById('geolocFeedbackLongitude'); 

let cameraPermission = true;
let geolocPermission = true;

// function is run after loading body
function initTest(timeLeft, testid) {
    checkTestPermission();
    invigilateUser(timeLeft, testid);
    countdown(timeLeft, testid);
}

// ******************** Fullscreen permission check ************************
document.onfullscreenchange = function() {checkTestPermission();};

// set full screen when button is pressed
fullbtn.onclick = function() {
    // console.log("fullbtn is pressed");     
    document.documentElement.requestFullscreen();
    
    // check webcam and geoloc access
    checkWebcamGeoloc();
};

// checks webcam and geolocation access and if test is in fullscreen mode
function checkTestPermission(){
    // console.log("this is document.fullscreenElement", document.fullscreenElement);
    if(document.fullscreenElement){
        // console.log("It is now full screen");
        fullscreenModal.hide();
        checkWebcamGeoloc();
    }else{
        // console.log("It is not full screen");
        permissionModal.hide();
        fullscreenModal.show();
    }
}

// checks permission for webcam and geoloc and opens modal if needed
// this function is called always at the end of checkTestPermission() 
function checkWebcamGeoloc(){
    navigator.permissions.query({name:'camera'}).then(function(permissionStatus) {
        if(permissionStatus.state == 'granted'){
            setWebcam();
            cameraPermission = true;
        }
        else{
            permissionModal.show();
            cameraPermission = false;
        }
    });

    navigator.permissions.query({name:'geolocation'}).then(function(permissionStatus) {
        if(permissionStatus.state =='granted'){
            setGeoloc();
            geolocPermission = true;
        }
        else{
            permissionModal.show();
            geolocPermission = false;
        }
    });

    if(cameraPermission && geolocPermission)
        permissionbtn.disabled = false;
    else
        permissionbtn.disabled = true;
}

// ******************** Webcam permission check ***************************
function setWebcam(){
    navigator.mediaDevices.getUserMedia({ video: true, audio: false })
    .then(function(stream){
        video.srcObject = stream;
    });
}

navigator.permissions.query({name:'camera'}).then(function(permissionStatus) {
    // console.log('camera permission status is ', permissionStatus.state);
    permissionStatus.onchange = function() {
        if(this.state =='granted'){
            setWebcam();
            cameraPermission = true;
        }
        else{
            checkTestPermission();
            cameraPermission = false;
        }

        if(cameraPermission && geolocPermission)
            permissionbtn.disabled = false;
        else
            permissionbtn.disabled = true;
    };
});

// ******************** geo location permission check ***************************
function setGeoloc(){
    if(navigator.geolocation){
        navigator.geolocation.getCurrentPosition(successGeoloc, failGeoloc);
    }
}
function successGeoloc(position){
    // console.log("postion: ", position.coords.latitude, ",", position.coords.longitude)
    latitude.innerHTML ="Latitude: " + position.coords.latitude;
    longitude.innerHTML ="Longitude: " + position.coords.longitude;
}

function failGeoloc(){
    latitude.innerHTML ="Latitude: UNKNOWN";
    longitude.innerHTML ="Longitude: UNKNOWN";
}

navigator.permissions.query({name:'geolocation'}).then(function(permissionStatus) {
    // console.log('initial geo location status:', permissionStatus.state);
    permissionStatus.onchange = function() {
        if(this.state =='granted'){
            setGeoloc();
            geolocPermission = true;
        }
        else{
            checkTestPermission();
            geolocPermission = false;
        }

        if(cameraPermission && geolocPermission)
            permissionbtn.disabled = false;
        else
            permissionbtn.disabled = true;
    };
});

// ******************** Timer and cookies ***************************
// saves selected option in cookies 
function saveOption(element){ 
    //uses cookie API for javascript from https://github.com/js-cookie/js-cookie/tree/latest#readme 
    Cookies.remove(element.name, {path: ''});   // remove old cookie
    Cookies.set(element.name, element.value, {expires: 1/12, path:''}); //sets cookie, expires in 2 hours
    console.log(element.name, ": ", element.value, " was added to cookie");
};

// sets the timer to integer minute
function countdown(minutes, testid) {
    var test_id = "test_" + testid;
    Cookies.remove(test_id, {path: ''});
    Cookies.set(test_id, minutes, {expires: 1/12 , path: ''}); //sets cookie, expires in 2 hours

    var seconds = 60;
    var mins = minutes;
    var offset = Math.floor(Math.random() *60);
    
    // changing color of div after 30 mins and 5 mins
    var elem = document.getElementById("timer");
    if(minutes<=30){
        elem.classList.replace('bg-success', 'bg-warning');
    }
    if(minutes<=5){
        elem.classList.replace('bg-warning', 'bg-danger');
    }

    // decreases 
    function tick(offset) {
        var counter = document.getElementById("timer");
        var current_minutes = mins-1
        seconds--;
        counter.innerHTML = current_minutes.toString() + ":" + (seconds < 10 ? "0" : "") + String(seconds);
        if(seconds == offset){
            console.log("capture function called at", offset);
            invigilateUser(current_minutes, testid);
        }

        if( seconds > 0 ) {
            setTimeout(tick, 1000, offset);
        }     
        else if(mins > 1) {
            setTimeout(function () { countdown(mins - 1, testid); }, 1000); 
        }

        if(seconds==0 && current_minutes==0){
            alert("Time up!");
            document.getElementById("sub").click();
        }
    }
    tick(offset);
} 

// ************************* Protoring *****************************
// send proctoring details to php proctor function
function invigilateUser(minute, testid){

    // takes video input and makes a data url
    canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
    let image_data_url = canvas.toDataURL('image/jpeg');

    navigator.permissions.query({name:'geolocation'}).then(function(permissionStatus) {
        // console.log('initial geo location status:', permissionStatus.state);
        if(permissionStatus.state == 'granted'){
            navigator.geolocation.getCurrentPosition(successFunction, failureFunction);
        }
        else{
            failureFunction();
        }
    });

    // function called if location position is obtained
    function successFunction(position){
        let latitude = position.coords.latitude;
        let longitude = position.coords.longitude;
        console.log("longitude: ", longitude, ", latitude: ", latitude);

        // http POST request to send to upload_protor.ps.php
        fetch("phpScript/upload_proctor.ps.php", {
            method : "POST",
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
              },
            body: "testid="+testid +"&minute="+minute +"&latitude="+latitude +"&longitude="+longitude+ "&dataUrl="+image_data_url
        }).then(
            response => response.text()
        ).then(
            html => console.log(html)
        );
    }

    // function called if failed to get position
    function failureFunction(){
        console.log("failed to get latitude and longitude.");

        // http POST request to send to upload_protor.ps.php
        fetch("phpScript/upload_proctor.ps.php", {
            method : "POST",
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
              },
            body: "testid="+testid +"&minute="+minute+"&dataUrl="+image_data_url
        }).then(
            response => response.text()
        ).then(
            html => console.log(html)
        );
    }
}
