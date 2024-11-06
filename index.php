<?php
session_start();
require 'config.php';

$username = 'Guest';

if (!isset($_SESSION['username'])) {
    if (isset($_COOKIE['username']) && $username !== 'Guest') {
        $_SESSION['username'] = $_COOKIE['username'];
        $user_id = $conn->query("SELECT id FROM users WHERE username='$username'")->fetch_assoc()['id'];
    } else {
        $_SESSION['username'] = 'Guest';
    }
}
else if (isset($_SESSION['username']) && $_SESSION['username'] !== 'Guest') {
    $username = $_SESSION['username'];
    setcookie('username', $username, time() + (3600), "/");
    $user_id = $conn->query("SELECT id FROM users WHERE username='$username'")->fetch_assoc()['id'];

    // if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //     $name = $_POST['name'];
    //     $deadline = $_POST['deadline'];
    //     $notes = $_POST['notes'];
    //     $sql = "INSERT INTO activities (user_id, name, deadline, notes) VALUES ('$user_id', '$name', '$deadline', '$notes')";
    //     $conn->query($sql);
    // }

    // $search = isset($_GET['search']) ? $_GET['search'] : '';
    // $activities = $conn->query("SELECT * FROM activities WHERE user_id='$user_id' AND name LIKE '%$search%'");
} else {
    $activities = [];
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>TO-DO</title>
    <link rel="stylesheet" href="styles.css" />
  </head>
  <body>
    <header>
        <div class="logo">TO-DO | <?php echo htmlspecialchars($username); ?></div>
        
        <!-- <form class="search-form" method="GET" action="index.php">
            <input type="text" name="-form" placeholder="Search activities...">
            <button type="submit" class="search-btn"><</button>
        </form> -->
        <div class="buttons">
            <button class="add-btn">Add Activity</button>
            <button id="arrange_button" class="arrange-btn">Arrange</button>
            <?php if ($username === 'Guest'): ?>
                <a href="login.php" class="add-btn">Login</a>
                <a href="register.php" class="add-btn">Register</a>
            <?php else: ?>
                <a href="logout.php" class="add-btn">Logout</a>
            <?php endif; ?>
        </div>
    </header>
    <main id="section_container"></main>

    <div id="activity_modal" class="modal hidden">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <h2>Activity</h2>
            <div
            style="display: flex; justify-content: center; align-items: center"
            >
                <div class="modal-content-input">
                    <form id="activityForm" method="POST" action="index.php">
                        <div>
                            <label for="activity_input">Name:</label>
                            <input
                            type="text"
                            id="activity_input"
                            placeholder="Enter activity name"
                            required
                            />
                        </div>
                        <div>
                            <label for="deadlineInput">Deadline:</label>
                            <input
                            type="date"
                            id="deadlineInput"
                            placeholder="Enter deadline date"
                            required
                            />
                        </div>
                        <div>
                            <label for="notesInput">Notes:</label>
                            <textarea id="notesInput" placeholder="Enter notes" d></textarea>
                        </div>
                        <div>
                            <label for="sectionSelect">Select Section:</label>
                            <select id="sectionSelect"></select>
                        </div>
                        <button type="submit" id="save_activity_button" class="save-btn">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <div id="edit_activity_modal" class="modal hidden">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <h2>Edit Activity</h2>
            <div style="display: flex; justify-content: center; align-items: center">
                <div class="modal-content-input">
                    <form id="editActivityForm" method="POST" action="index.php">
                        <input type="hidden" id="edit_activity_id" />
                        <div>
                            <label for="edit_activity_input">Name:</label>
                            <input type="text" id="edit_activity_input" placeholder="Enter activity name" required />
                        </div>
                        <div>
                            <label for="edit_deadlineInput">Deadline:</label>
                            <input type="date" id="edit_deadlineInput" placeholder="Enter deadline date" required />
                        </div>
                        <div>
                            <label for="edit_notesInput">Notes:</label>
                            <textarea id="edit_notesInput" placeholder="Enter notes"></textarea>
                        </div>
                        <div>
                            <label for="edit_sectionSelect">Select Section:</label>
                            <select id="edit_sectionSelect"></select>
                        </div>
                        <button id="edit_save_activity_button" class="save-btn">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <div id="arrange_container" class="hidden">
      <button id="add_section_btn" class="add-btn">Add Section</button>
    </div>
    
    <input type="hidden" id="user_id" value="<?php echo isset($user_id) ? $user_id : ''; ?>">
    <input type="hidden" id="username" value="<?php echo htmlspecialchars($username); ?>">
    <script src="script.js"></script>
  </body>
</html>