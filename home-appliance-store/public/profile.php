<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

/* UPDATE PROFILE */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['name']);
    $email = trim($_POST['email']);

    if ($name && $email) {
        $stmt = $conn->prepare("
            UPDATE users 
            SET name = :name, email = :email 
            WHERE user_id = :id
        ");
        $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':id' => $user_id
        ]);

        $success = "Profile updated successfully";
    }
}

/* FETCH USER DATA */
$stmt = $conn->prepare("
    SELECT name, email, role, created_at 
    FROM users 
    WHERE user_id = :id
");
$stmt->execute([':id' => $user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Profile</title>

<style>
body {
    font-family: Arial, sans-serif;
    background: #f4f4f4;
    padding: 20px;
}

.profile-box {
    max-width: 500px;
    margin: auto;
    background: #fff;
    padding: 25px;
    border-radius: 8px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
}

.profile-icon {
    width: 90px;
    height: 90px;
    border-radius: 50%;
    background: #0056b3;
    color: #fff;
    font-size: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 15px;
}

h2 {
    text-align: center;
    margin-bottom: 20px;
}

label {
    font-weight: bold;
    display: block;
    margin-top: 15px;
}

input {
    width: 100%;
    padding: 10px;
    margin-top: 5px;
    border-radius: 4px;
    border: 1px solid #ccc;
}

.readonly {
    background: #eee;
}

button {
    margin-top: 20px;
    width: 100%;
    padding: 12px;
    border: none;
    background: #0056b3;
    color: #fff;
    font-size: 16px;
    border-radius: 4px;
    cursor: pointer;
}

button:hover {
    background: #004494;
}

.success {
    background: #d4edda;
    color: #155724;
    padding: 10px;
    margin-bottom: 15px;
    border-radius: 4px;
    text-align: center;
}

.links {
    text-align: center;
    margin-top: 15px;
}
</style>
</head>

<body>

<div class="profile-box">

    <div class="profile-icon">
        👤
    </div>

    <h2>My Profile</h2>

    <?php if (!empty($success)): ?>
        <div class="success"><?= $success ?></div>
    <?php endif; ?>

    <form method="POST">
        <label>Name</label>
        <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>

        <label>Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

        <label>Role</label>
        <input type="text" class="readonly" value="<?= htmlspecialchars($user['role']) ?>" readonly>

        <label>Account Created</label>
        <input type="text" class="readonly" value="<?= htmlspecialchars($user['created_at']) ?>" readonly>

        <button type="submit">Update Profile</button>
    </form>

    <div class="links">
        <p><a href="index.php">Back to Dashboard</a></p>
        <p><a href="logout.php">Logout</a></p>
    </div>

</div>

</body>
</html>