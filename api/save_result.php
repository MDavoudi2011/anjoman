<?php




$data_dir = __DIR__ . '/../data';

$results_file = $data_dir . '/results.json';


date_default_timezone_set('Asia/Tehran');




$json_input = file_get_contents('php://input');
$data = json_decode($json_input, true);


if (empty($data) || !isset($data['name']) || empty(trim($data['name']))) {
    header('Content-Type: application/json');
    http_response_code(400); 
    echo json_encode(['status' => 'error', 'message' => 'No data or name provided.']);
    exit;
}





$score = 0;
if (isset($data['answers']) && is_array($data['answers'])) {
    foreach ($data['answers'] as $answer) {
        
        if (isset($answer['correct']) && $answer['correct'] === true) {
            $score++;
        }
    }
}


$ip = 'Unknown';
if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
    $ip = $_SERVER['HTTP_CLIENT_IP'];
} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
} else {
    $ip = $_SERVER['REMOTE_ADDR'];
}

$ip_list = explode(',', $ip);
$ip = trim($ip_list[0]);




$timestamp = date('Y-m-d H:i:s');





$total_questions = $data['totalQuestions'] ?? count($data['answers']);

$new_entry = [
    'name'       => $data['name'],
    'score'      => $score, 
    'total'      => $total_questions,
    'answers'    => $data['answers'], 
    'cheated'    => $data['cheated'],
    'cheatCount' => $data['cheatCount'],
    'timestamp'  => $timestamp, 
    'ip'         => $ip         
];





if (!is_dir($data_dir)) {
    
    if (!mkdir($data_dir, 0755, true)) {
        
        header('Content-Type: application/json');
        http_response_code(500); 
        echo json_encode(['status' => 'error', 'message' => 'Failed to create data directory. Check permissions.']);
        exit;
    }
}


$all_results = [];
if (file_exists($results_file)) {
    $json_content = file_get_contents($results_file);
    $all_results = json_decode($json_content, true);
    
    if (!is_array($all_results)) {
        $all_results = [];
    }
}


$all_results[] = $new_entry;


$new_json_content = json_encode($all_results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);


file_put_contents($results_file, $new_json_content, LOCK_EX);



header('Content-Type: application/json');
echo json_encode(['status' => 'success', 'message' => 'Result saved successfully.']);

?>