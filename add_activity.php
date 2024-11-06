<?php
session_start();
require 'config.php';

header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'C:\xampp\htdocs\Tugas_Projek_Web_2/error.log');
$response = ['success' => false, 'message' => ''];

if (isset($_SESSION['username']) && $_SESSION['username'] !== 'Guest') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $user_id = $_POST['user_id'];
        echo $user_id;
        $name = $_POST['name'];
        $deadline = $_POST['deadline'];
        $notes = $_POST['notes'];
        $section_id = $_POST['section_id'];

        if (!empty($user_id) && !empty($name) && !empty($deadline) && !empty($section_id)) {
            $stmt = $conn->prepare("INSERT INTO activities (user_id, name, deadline, notes, label_id) VALUES (?, ?, ?, ?, ?)");
            if ($stmt === false) {
                $response['message'] = 'Error preparing statement.';
                error_log("Error preparing statement: " . $conn->error);
            } else {
                $stmt->bind_param("isssi", $user_id, $name, $deadline, $notes, $section_id);

                if ($stmt->execute()) {
                    $response['success'] = true;
                } else {
                    $response['message'] = 'Error adding activity.';
                    error_log("Error adding activity: " . $stmt->error);
                }

                $stmt->close();
            }
        } else {
            $response['message'] = 'All fields are required.';
            error_log("All fields are required.");
        }
    } else {
        $response['message'] = 'Invalid request method.';
        error_log("Invalid request method: " . $_SERVER['REQUEST_METHOD']);
    }
} else {
    $response['message'] = 'User not authenticated.';
    error_log("User not authenticated.");
}

echo json_encode($response);
?>