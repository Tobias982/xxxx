<?php
session_start();
require 'db.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header("Location: dashboard.php");
        exit;
    } else {
        $message = "Špatné jméno nebo heslo.";
    }
}
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Přihlášení</title>
    <link rel="stylesheet" href="style/style.css">
</head>
<body>
    <main>
        <section>
            <div class="form">
                <div class="form-header">
                    <img src="images/apexlogo.png" alt="LogoApex">
                    <h2>Přihlášení</h2>
                    <?php if($message): ?>
                        <p style="color: #f25a5a; text-align:center;"><?php echo $message; ?></p>
                    <?php endif; ?>
                </div>
                <form action="login.php" method="POST">
                    <div class="form-main">
                        <div class="form-main-inputs">
                            <label>Uživatelské jméno</label>
                            <input type="text" name="username" required>
                        </div>
                        <div class="form-main-inputs">
                            <label>Heslo</label>
                            <input type="password" name="password" required>
                        </div>
                    </div>
                    <div class="form-buttons">
                        <button type="submit" style="margin-top: 20px;">Přihlásit se</button>
                    </div>       
                </form>
                <div class="form-footer">
                    <a href="index.php">
                        <span>Nemáte účet?</span>
                        Registrovat se
                    </a>
                </div> 
            </div>
        </section>
    </main>
</body>
</html>