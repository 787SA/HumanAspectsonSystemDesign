<?php
include 'db.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare and execute query
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['username'] = $username;
        $_SESSION['user_id'] = $user['id'];

        // Fetch user's role from user_roles table
        $stmt = $pdo->prepare("SELECT r.role_name FROM roles r
                               JOIN user_roles ur ON r.id = ur.role_id
                               WHERE ur.user_id = ?");
        $stmt->execute([$user['id']]);
        $role = $stmt->fetchColumn();
        $_SESSION['role'] = $role;

        // Redirect based on user role
        switch ($role) {
            case 'delivery_driver':
                header("Location: delivery_driver.php");
                break;
            case 'stock_manager':
                header("Location: stock_manager_dashboard.php");
                break;
            case 'inventory_staff':
                header("Location: inventory_staff_dashboard.php");
                break;
            case 'supplier_coordinator':
                header("Location: supplier_coordinator_dashboard.php");
                break;
            case 'manager':
                header("Location: manager_dashboard.php");
                break;
            case 'kitchen_inventory_staff':
                header("Location: kitchen_inventory_staff.php");
                break;
            case 'kitchen_manager':
                header("Location: kitchen_manager.php");
                break;
            default:
                header("Location: index.php");
                break;
        }
        exit();
    } else {
        echo "Invalid credentials";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f4f4f4;
        }
        .login-container {
            background-color: #fff;
            padding: 40px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
            text-align: center;
        }
        .login-container h2 {
            margin-bottom: 20px;
            color: #1E90FF;
        }
        .login-container input[type="text"],
        .login-container input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .login-container button {
            width: 100%;
            padding: 10px;
            background-color: #1E90FF;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .login-container button:hover {
            background-color: #1C86EE;
        }
        footer {
            background-color: #1E90FF;
            color: white;
            padding: 10px 20px;
            text-align: center;
            width: 100%;
            position: absolute;
            bottom: 0;
        }
    </style>
 
    <script src="https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
    <script>
        function googleTranslateElementInit() {
            new google.translate.TranslateElement({
                pageLanguage: 'en',
                includedLanguages: 'ar,zh,cs,da,nl,en,fi,fr,de,el,hi,hu,id,it,ja,ko,no,pl,pt,ru,es,sv,th,tr,vi',
                layout: google.translate.TranslateElement.InlineLayout.SIMPLE
            }, 'google_translate_element');
        }
    </script>
</head>
<body>
    <div class="login-container">
        <h2 data-translate>Sign In</h2>
        <form method="POST" action="sign-in.php">
            <input type="text" name="username" placeholder="Username" required data-translate>
            <input type="password" name="password" placeholder="Password" required data-translate>
            <button type="submit" data-translate>Sign In</button>
        </form>
    </div>
    <footer>
        <div id="google_translate_element"></div>
        <button onclick="googleTranslateElementInit()">Translate</button>
    </footer>
</body>
</html>


            