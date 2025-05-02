<?php
session_start();
$userId = $_SESSION['userId'];
header('Content-Type: application/json');
$config=include('config.php');

$host = 'localhost';
$db   = 'mock_interview';
$user = 'root';
$pass = '';
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die(json_encode(["error" => "Database connection failed"]));
}

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(["error" => "Invalid input"]);
    exit;
}

$jobPosition = $data['jobPosition'];
$jobDesc = $data['jobDesc'];
$jobExperience = $data['jobExperience'];
$createdBy = $userId;
// $createdAt = date('Y-m-d H:i:s');

// ✅ Generate unique mock ID
$mockid = uniqid();

// ✅ Gemini API Request
$apiKey = $config['GEMINI_API_KEY'];
$url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=$apiKey";

$prompt = "Job Position: $jobPosition, Job Description: $jobDesc, Years of Experience: $jobExperience, Depends on this information please give me 5 interview questions with answers in JSON format, Give questions and answered as field in JSON";

$requestBody = json_encode([
    "contents" => [["parts" => [["text" => $prompt]]]]
]);

$options = [
    'http' => [
        'header' => "Content-Type: application/json",
        'method' => 'POST',
        'content' => $requestBody
    ]
];

$context = stream_context_create($options);
$response = file_get_contents($url, false, $context);
$responseData = json_decode($response, true);

if (!$responseData || isset($responseData['error'])) {
    echo json_encode(["error" => $responseData['error']['message'] ?? "API Error"]);
    exit;
}

// ✅ Remove ```json and ```
$jsonMockResponse = str_replace(["```json", "```"], "", json_encode($responseData));

// ✅ Save to Database
$stmt = $conn->prepare("INSERT INTO interviews (mockid, jsonmockresponse, job_position, job_description, job_experience, created_by, created_at) VALUES (?, ?, ?, ?, ?, ?, now())");
$stmt->bind_param("ssssss", $mockid, $jsonMockResponse, $jobPosition, $jobDesc, $jobExperience, $createdBy);

if ($stmt->execute()) {
    echo json_encode(["mockid" => $mockid]);
} else {
    echo json_encode(["error" => "Failed to save data"]);
}

$stmt->close();
$conn->close();
?>
