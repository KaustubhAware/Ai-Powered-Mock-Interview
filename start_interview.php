<?php
session_start();

$mockid = $_GET['mockid'] ?? null;
if (!$mockid) {
    die("Invalid Mock ID");
}

// Database Connection
$host = 'localhost';
$db   = 'mock_interview';
$user = 'root';
$pass = '';
$conn = new mysqli($host, $user, $pass, $db);

// Fetch Data
$result = $conn->query("SELECT * FROM interviews WHERE mockid='$mockid'");
$data = $result->fetch_assoc();

if ($data) {
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mock Interview</title>
    <link rel="stylesheet" href="css/start_interview.css">
    <script>
        let permissionsGranted = false;

        function checkPermissions() {
            navigator.mediaDevices.getUserMedia({ video: true, audio: true })
                .then(function(stream) {
                    permissionsGranted = true;
                    document.getElementById('startBtn').disabled = false;
                    alert("Webcam & Microphone enabled! You can now start the interview.");
                })
                .catch(function(error) {
                    permissionsGranted = false;
                    alert("Permission denied. Please enable your webcam & microphone to proceed!");
                });
        }

        function startInterview() {
            if (!permissionsGranted) {
                alert("Please enable your webcam & microphone before proceeding.");
                return false; // Prevent function execution
            }
            window.location.href = "interview.php?mockid=<?php echo $mockid;?>";
        }
    </script>

</head>
<body>
    <?php include('header.php'); ?>
    <div class="main-container">
        <!-- Left Section: Job Details & Info -->
        <div class="left-section">
            <h2 style="margin: 10px; padding-bottom: 10px;">Let's Get Started</h2>
            <div class="job-details">
                <p><strong>Job Role/Job Position:</strong> <?php echo htmlspecialchars($data['job_position']); ?></p>
                <p><strong>Job Description/Tech Stack:</strong> <?php echo htmlspecialchars($data['job_description']); ?></p>
                <p><strong>Years of Experience:</strong> <?php echo htmlspecialchars($data['job_experience']); ?></p>
            </div>

            <div class="info-box">
                <h3 style="margin-left: -10px;">💡 Information</h3>
                <p>Enable Video Web Cam and Microphone to start your AI Generated Mock Interview.<br> 
                   It has 5 questions which you can answer, and at the last, 
                   <span class="highlight">you will get the report on the basis of your answer.</span></p><br>
                <p><strong>NOTE:</strong><br> We never record your video. Webcam access can be disabled at any time if you want.</p>
            </div>
        </div>

        <!-- Right Section: Webcam -->
        <div class="right-section">
            <div class="webcam-container">
                <div class="webcam-box">
                    <img src="images/webcam.png" alt="Webcam Icon" class="webcam-icon" id="webcamIcon">
                    <video id="webcamVideo" autoplay playsinline></video>
                </div>
                <button id="enableWebcam" class="btn gray-btn" onclick="checkPermissions()">Enable Web Cam and Microphone</button>
                
            </div>

             <button id="startBtn" class="btn purple-btn" onclick="startInterview()" >Start Interview</button>
         </div>
    </div>

    <script src="start_interview.js"></script>
</body>
</html>

<?php
} else {
    echo "<p style='color:red; text-align:center;'>No data found for this Mock ID</p>";
}
$conn->close();
?>