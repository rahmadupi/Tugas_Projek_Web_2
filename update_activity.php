<?php
session_start();
require 'config.php';

header('Content-Type: application/json');
$response = ['success' => false, 'message' => ''];

if (isset($_SESSION['username']) && $_SESSION['username'] !== 'Guest') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $deadline = $_POST['deadline'];
        $notes = $_POST['notes'];
        $section_id = $_POST['section_id'];

        if (!empty($id) && !empty($name) && !empty($deadline) && !empty($section_id)) {
            $stmt = $conn->prepare("UPDATE activities SET name = ?, deadline = ?, notes = ?, label_id = ? WHERE id = ?");
            if ($stmt === false) {
                $response['message'] = 'Error preparing statement.';
                error_log("Error preparing statement: " . $conn->error);
            } else {
                $stmt->bind_param("sssii", $name, $deadline, $notes, $section_id, $id);

                if ($stmt->execute()) {
                    $response['success'] = true;
                } else {
                    $response['message'] = 'Error updating activity.';
                    error_log("Error updating activity: " . $stmt->error);
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