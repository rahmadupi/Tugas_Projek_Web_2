<?php
session_start();
require 'config.php';

header('Content-Type: application/json');
$response = ['success' => false, 'message' => ''];

if (isset($_SESSION['username']) && $_SESSION['username'] !== 'Guest') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = $_POST['id'];
        $name = $_POST['name'];

        if (!empty($id) && !empty($name)) {
            $stmt = $conn->prepare("UPDATE labels SET name = ? WHERE id = ?");
            if ($stmt === false) {
                $response['message'] = 'Error preparing statement.';
                error_log("Error preparing statement: " . $conn->error);
            } else {
                $stmt->bind_param("si", $name, $id);

                if ($stmt->execute()) {
                    $response['success'] = true;
                } else {
                    $response['message'] = 'Error updating section.';
                    error_log("Error updating section: " . $stmt->error);
                }

                $stmt->close();
            }
        } else {
            $response['message'] = 'Section ID and name cannot be empty.';
            error_log("Section ID and name cannot be empty.");
        }
    }
} else {
    $response['message'] = 'User not authenticated.';
    error_log("User not authenticated.");
}

echo json_encode($response);
?>