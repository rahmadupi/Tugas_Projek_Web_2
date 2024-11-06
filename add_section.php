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
    $username = $_SESSION['username'];
    $user_id_result = $conn->query("SELECT id FROM users WHERE username='$username'");

    if ($user_id_result && $user_id_result->num_rows > 0) {
        $user_id = $user_id_result->fetch_assoc()['id'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'];

            if (!empty($name)) {
                $stmt = $conn->prepare("INSERT INTO labels (user_id, name) VALUES (?, ?)");
                if ($stmt === false) {
                    $response['message'] = 'Error preparing statement.';
                    error_log("Error preparing statement: " . $conn->error);
                } else {
                    $stmt->bind_param("is", $user_id, $name);

                    if ($stmt->execute()) {
                        $response['success'] = true;
                    } else {
                        $response['message'] = 'Error adding section.';
                        error_log("Error adding section: " . $stmt->error);
                    }

                    $stmt->close();
                }
            } else {
                $response['message'] = 'Section name cannot be empty.';
                error_log("Section name cannot be empty.");
            }
        }
    } else {
        $response['message'] = 'Error fetching user ID.';
        error_log("Error fetching user ID: " . $conn->error);
    }
} else {
    $response['message'] = 'User not authenticated.';
    error_log("User not authenticated.");
}

echo json_encode($response);
?>