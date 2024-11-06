<?php
session_start();
require 'config.php';

header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'C:\xampp\htdocs\Tugas_Projek_Web_2/error.log'); // Specify the path to your error log file

$response = ['success' => false, 'message' => ''];

if (isset($_SESSION['username']) && $_SESSION['username'] !== 'Guest') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = $_POST['id'];

        if (!empty($id)) {
            $stmt = $conn->prepare("DELETE FROM activities WHERE id = ?");
            if ($stmt === false) {
                $response['message'] = 'Error preparing statement.';
                error_log("Error preparing statement: " . $conn->error);
            } else {
                $stmt->bind_param("i", $id);

                if ($stmt->execute()) {
                    $response['success'] = true;
                } else {
                    $response['message'] = 'Error deleting activity.';
                    error_log("Error deleting activity: " . $stmt->error);
                }

                $stmt->close();
            }
        } else {
            $response['message'] = 'Activity ID cannot be empty.';
            error_log("Activity ID cannot be empty.");
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