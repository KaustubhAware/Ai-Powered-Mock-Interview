document.getElementById("enableWebcam").addEventListener("click", function() {
    let video = document.getElementById("webcamVideo");
    let icon = document.getElementById("webcamIcon");

    navigator.mediaDevices.getUserMedia({ video: true, audio: false })
    .then(function(stream) {
        icon.style.display = "none";  // Hide icon
        video.style.display = "block"; // Show video
        video.srcObject = stream;
    })
    .catch(function(error) {
        alert("Error accessing webcam or microphone: " + error);
    });
});
