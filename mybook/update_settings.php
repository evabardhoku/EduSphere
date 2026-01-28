<?php
session_start();
require_once 'classes/settings.php'; // Adjust the path if needed

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $settings_class = new Settings();
    $user_id = $_SESSION['mybook_userid'];

    // Get POST data
    $data = [
        'first_name' => $_POST['first_name'] ?? '',
        'last_name' => $_POST['last_name'] ?? '',
        'gender' => $_POST['gender'] ?? '',
        'email' => $_POST['email'] ?? '',
        'password' => $_POST['password'] ?? '',
        'password2' => $_POST['password2'] ?? '',
        'about' => $_POST['about'] ?? ''
    ];

    // Save settings
    $settings_class->save_settings($data, $user_id);

    // Redirect or show a success message
    header('Location: profile.php?section=settings&id=' . urlencode($user_id));
    exit;
}
?>
