
<?php
include("db.php");
$config = include("config.php");

$data = json_decode(file_get_contents("php://input"), true);
$question = trim($data['question']);
$correctAnswer = trim($data['correctAnswer']);
$userAnswer = trim($data['answer']);
$mockId = trim($data['mockId']);

if (empty($question) || empty($correctAnswer) || empty($userAnswer) || empty($mockId)) {
    echo json_encode(["error" => "Invalid input data"]);
    exit;
}
$sql="SELECT created_by FROM interviews WHERE mockid='$mockId'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $createdBy = $result->fetch_assoc()['created_by'];
} else {
    echo json_encode(["error" => "Mock ID not found"]);
    exit;
}

$apiKey = $config['GEMINI_API_KEY']; 
$url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=$apiKey";

// Construct the prompt
$prompt = "Question: $question, User Answer: $userAnswer, 
    Depends on question and user answer for given interview question. 
     Please provide a rating out of 10 based on relevance, completeness, and clarity,if feedback irrelevant give rating as 0. Additionally, give concise feedback (3-5 lines) on areas of improvement, if any.
    in just 3 to 5 lines to improve it in JSON format, Give rating and feedback as field in JSON .";

$requestBody = json_encode(["contents" => [["parts" => [["text" => $prompt]]]]]);

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

$tempResponse = str_replace(["```json", "```"], "", json_encode($responseData));

$data = json_decode($tempResponse, true);

// Extract text field which contains another JSON string
$geminiFeedback = json_decode($data['candidates'][0]['content']['parts'][0]['text'], true);

// Extract rating & feedback with defaults
$rating = $geminiFeedback['rating'] ?? 'No rating';
$feedbackRaw = $geminiFeedback['feedback'] ?? 'No feedback';
$feedback=is_array($feedbackRaw) ? implode(" ",$feedbackRaw) : $feedbackRaw;

// ✅ Use Prepared Statement for Security
$sql = "INSERT INTO userAnswer (mockIdRef, question,correctAns, userAns, feedback, rating, userEmail,createdAt)
        VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssss", $mockId, $question, $correctAnswer,$userAnswer, $feedback, $rating, $createdBy);

if ($stmt->execute()) {
    echo json_encode(["message" => "Answer stored successfully"]);
} else {
    echo json_encode(["error" => "Database error: " . $conn->error]);
}

$stmt->close();
$conn->close();
?>
