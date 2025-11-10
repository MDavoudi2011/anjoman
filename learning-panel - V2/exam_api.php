<?php
session_start();
define('APP_ACCESS', true);
require_once 'config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user']['username'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$user = Config::getUser($_SESSION['user']['username']);
$userId = $user['id'];
$action = $_GET['action'] ?? $_POST['action'] ?? '';

$pdo = Config::db();

switch ($action) {
    case 'get_exam':
        $stmt = $pdo->prepare("
            SELECT e.*, eq.id as qid, eq.question, eq.type, eq.options 
            FROM exams e 
            LEFT JOIN exam_questions eq ON e.id = eq.exam_id 
            WHERE e.group_name = ? AND e.is_active = 1
            ORDER BY eq.id
        ");
        $stmt->execute([$user['user_group']]);
        $rows = $stmt->fetchAll();

        if (empty($rows)) {
            echo json_encode(['success' => false, 'message' => 'آزمونی فعال نیست']);
            exit;
        }

        $exam = [
            'id' => $rows[0]['id'],
            'title' => $rows[0]['title'],
            'description' => $rows[0]['description'],
            'duration' => $rows[0]['duration'],
            'questions' => []
        ];

        foreach ($rows as $row) {
            if ($row['qid']) {
                $exam['questions'][] = [
                    'id' => $row['qid'],
                    'question' => $row['question'],
                    'type' => $row['type'],
                    'options' => $row['options'] ? json_decode($row['options'], true) : null
                ];
            }
        }

        echo json_encode(['success' => true, 'exam' => $exam]);
        break;

    case 'start':
        $examId = $_POST['exam_id'] ?? 0;
        $stmt = $pdo->prepare("INSERT INTO exam_submissions (user_id, exam_id, answers, status) VALUES (?, ?, ?, 'in_progress') ON DUPLICATE KEY UPDATE started_at = NOW(), status = 'in_progress'");
        $stmt->execute([$userId, $examId, json_encode([])]);
        echo json_encode(['success' => true]);
        break;

    case 'save':
        $examId = $_POST['exam_id'] ?? 0;
        $answers = $_POST['answers'] ?? [];
        $stmt = $pdo->prepare("UPDATE exam_submissions SET answers = ? WHERE user_id = ? AND exam_id = ? AND status = 'in_progress'");
        $stmt->execute([json_encode($answers), $userId, $examId]);
        echo json_encode(['success' => true]);
        break;

    case 'submit':
        $examId = $_POST['exam_id'] ?? 0;
        $stmt = $pdo->prepare("UPDATE exam_submissions SET status = 'submitted', submitted_at = NOW() WHERE user_id = ? AND exam_id = ? AND status = 'in_progress'");
        $stmt->execute([$userId, $examId]);
        echo json_encode(['success' => true]);
        break;

    case 'get_answers':
        $examId = $_GET['exam_id'] ?? 0;
        $stmt = $pdo->prepare("SELECT answers, status FROM exam_submissions WHERE user_id = ? AND exam_id = ?");
        $stmt->execute([$userId, $examId]);
        $row = $stmt->fetch();
        echo json_encode($row ? ['answers' => json_decode($row['answers'], true), 'status' => $row['status']] : ['answers' => [], 'status' => null]);
        break;
}