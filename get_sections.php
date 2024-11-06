<?php
session_start();
require 'config.php';

header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'C:\xampp\htdocs\Tugas_Projek_Web_2/error.log'); // Specify the path to your error log file

$response = [];

if (isset($_SESSION['username']) && $_SESSION['username'] !== 'Guest') {
    $username = $_SESSION['username'];
    $user_id_result = $conn->query("SELECT id FROM users WHERE username='$username'");

    if ($user_id_result && $user_id_result->num_rows > 0) {
        $user_id = $user_id_result->fetch_assoc()['id'];

        $sections_result = $conn->query("SELECT * FROM labels WHERE user_id='$user_id'");
        if ($sections_result) {
            $sections_array = [];

            while ($section = $sections_result->fetch_assoc()) {
                $sections_array[] = $section;
            }

            $response = $sections_array;
        } else {
            $response = ['error' => 'Error fetching sections'];
            error_log("Error fetching sections: " . $conn->error);
        }
    } else {
        $response = ['error' => 'Error fetching user ID'];
        error_log("Error fetching user ID: " . $conn->error);
    }
} else {
    $response = ['error' => 'User not authenticated'];
    error_log("User not authenticated");
}
echo json_encode($response);
?>