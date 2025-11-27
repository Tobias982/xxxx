<?php
session_start();
require 'db.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($username) || empty($password)) {
        $message = "Vyplňte všechna pole.";
    } elseif ($password !== $confirm_password) {
        $message = "Hesla se neshodují.";
    } elseif (strlen($password) < 6) {
        $message = "Heslo musí mít alespoň 6 znaků.";
    } else {
        // Kontrola existence uživatele
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->rowCount() > 0) {
            $message = "Uživatelské jméno je již obsazené.";
        } else {
            // Vytvoření uživatele
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            
            if ($stmt->execute([$username, $hash])) {
                header("Location: login.php");
                exit;
            } else {
                $message = "Chyba při registraci.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrace</title>
    <link rel="stylesheet" href="style/style.css">
</head>
<body>
    <main>
        <section>
            <div class="form">
                <div class="form-header">
                    <img src="images/apexlogo.png" alt="LogoApex">
                    <h2>Vytvořit účet</h2>
                    <?php if($message): ?>
                        <p style="color: #f25a5a; text-align:center; margin-bottom:10px;"><?php echo $message; ?></p>
                    <?php endif; ?>
                </div>
                <form action="index.php" method="POST">
                    <div class="form-main">
                        <div class="form-main-inputs">
                            <label>Uživatelské jméno</label>
                            <input type="text" name="username" placeholder="Username" required>
                        </div>
                        <div class="form-main-inputs">
                            <label>Heslo</label>
                            <input type="password" name="password" placeholder="Min 6 znaků" required>
                        </div>
                        <div class="form-main-inputs">
                            <label>Potvrzení hesla</label>
                            <input type="password" name="confirm_password" placeholder="Zadejte heslo znovu" required>
                        </div>
                    </div>
                    
                    <div class="form-control">
                        <input type="checkbox" name="checkbox" id="terms" required>
                        <label for="terms">Souhlasím s <a href="#">podmínkami</a></label>
                    </div>

                    <div class="form-buttons">
                        <button type="submit">Vytvořit účet</button>
                    </div>       
                </form>
                <div class="form-footer">
                    <a href="login.php">
                        <span>Již máte účet?</span>
                        Přihlásit se
                    </a>
                </div> 
            </div>
        </section>
    </main>
</body>
</html>