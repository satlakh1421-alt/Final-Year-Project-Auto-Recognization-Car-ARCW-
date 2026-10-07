<!DOCTYPE html> <!-- define document type as HTML5 -->
<html> <!-- start of HTML document -->
<head> <!-- head section (metadata, title, etc.) -->
<title>Automatic Plate Camera</title> <!-- title shown on browser tab -->
</head> <!-- end of head -->

<body> <!-- start of body (visible content) -->

<h2>Car Plate Camera</h2> <!-- heading text displayed on page -->

<video id="video" width="500" autoplay></video> <!-- video element to display live camera stream -->
<br><br> <!-- line breaks for spacing -->

<button onclick="capture()">Scan Plate</button> <!-- button that triggers capture() function when clicked -->

<canvas id="canvas" width="500" height="350" style="display:none;"></canvas> <!-- hidden canvas used to capture image from video -->

<form method="POST" action="anpr_upload.php" id="form"> <!-- form to send captured image to server -->
<input type="hidden" name="image_data" id="image_data"> <!-- hidden input to store captured image (base64 format) -->
</form> <!-- end of form -->

<script> <!-- start of JavaScript -->

const video = document.getElementById('video'); // get reference to video element

navigator.mediaDevices.getUserMedia({video:true}) // request access to user's camera
.then(stream => { // if permission is granted
    video.srcObject = stream; // assign camera stream to video element to display live feed
});

function capture(){ // function to capture image from video

    const canvas = document.getElementById('canvas'); // get canvas element
    const context = canvas.getContext('2d'); // get 2D drawing context of canvas

    context.drawImage(video,0,0,500,350); // draw current video frame onto canvas (capture image)

    const data = canvas.toDataURL('image/jpeg'); // convert canvas image to base64 encoded JPEG format

    document.getElementById('image_data').value = data; // store captured image data inside hidden input field

    document.getElementById('form').submit(); // automatically submit form to send image to server (anpr_upload.php)

}

</script> <!-- end of JavaScript -->

</body> <!-- end of body -->
</html> <!-- end of HTML document -->