<?php
session_start();
require 'db.php';

// Ochrana stránky - jen pro přihlášené
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$msg = "";
$msg_color = "#00ff00"; // Zelená pro úspěch

// ZPRACOVÁNÍ FORMULÁŘE
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. Změna Emailu
    if (isset($_POST['email']) && !empty($_POST['email'])) {
        $email = trim($_POST['email']);
        $stmt = $pdo->prepare("UPDATE users SET email = ? WHERE id = ?");
        $stmt->execute([$email, $user_id]);
        $msg = "Email aktualizován.";
    }

    // 2. Změna Hesla
    if (!empty($_POST['new_password'])) {
        if (strlen($_POST['new_password']) >= 6) {
            $hash = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->execute([$hash, $user_id]);
            $msg = "Heslo bylo změněno.";
        } else {
            $msg = "Heslo musí mít min 6 znaků.";
            $msg_color = "#f25a5a";
        }
    }

    // 3. Upload Avataru
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['avatar']['name'];
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        
        if (in_array(strtolower($ext), $allowed)) {
            // Unikátní název souboru
            $new_name = "user_" . $user_id . "_" . time() . "." . $ext;
            $target = "images/" . $new_name;
            
            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $target)) {
                $stmt = $pdo->prepare("UPDATE users SET avatar = ? WHERE id = ?");
                $stmt->execute([$new_name, $user_id]);
                $msg = "Avatar nahrán.";
            } else {
                $msg = "Chyba při nahrávání souboru.";
                $msg_color = "#f25a5a";
            }
        } else {
            $msg = "Povoleny jsou jen obrázky (JPG, PNG).";
            $msg_color = "#f25a5a";
        }
    }
}

// Načtení dat uživatele
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$currentUser = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="style/style.css">
    <style>
        .avatar-img { width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 2px solid #932929; display: block; margin: 0 auto 15px auto;}
        .section-title { color: #f25a5a; margin: 20px 0 10px 0; border-bottom: 1px solid #434355; padding-bottom: 5px; }
        .logout-btn { background-color: #444 !important; margin-top: 15px; }
        .logout-btn:hover { background-color: #222 !important; }
    </style>
</head>
<body>
    <main>
        <section>
            <div class="form">
                <div class="form-header">
                    <h2>Můj Profil: <?php echo htmlspecialchars($currentUser['username']); ?></h2>
                    
                    <?php 
                        // Pokud avatar neexistuje, použijeme logo nebo default
                        $avatar = $currentUser['avatar'] ? $currentUser['avatar'] : 'apexlogo.png';
                        // Pokud soubor fyzicky neexistuje, dej logo
                        if (!file_exists("images/" . $avatar)) { $avatar = 'apexlogo.png'; }
                    ?>
                    <img src="images/<?php echo $avatar; ?>" alt="Avatar" class="avatar-img">

                    <?php if($msg): ?>
                        <p style="color: <?php echo $msg_color; ?>; text-align:center;"><?php echo $msg; ?></p>
                    <?php endif; ?>
                </div>

                <form action="dashboard.php" method="POST" enctype="multipart/form-data">
                    
                    <h3 class="section-title">Osobní údaje</h3>
                    <div class="form-main-inputs">
                        <label>Email</label>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($currentUser['email'] ?? ''); ?>" placeholder="tvuj@email.cz">
                    </div>

                    <div class="form-main-inputs">
                        <label>Změnit Avatara</label>
                        <input type="file" name="avatar" style="color: white; padding: 10px 0;">
                    </div>

                    <h3 class="section-title">Bezpečnost</h3>
                    <div class="form-main-inputs">
                        <label>Nové heslo (nevyplňujte, pokud nechcete měnit)</label>
                        <input type="password" name="new_password" placeholder="Nové heslo">
                    </div>

                    <div class="form-buttons">
                        <button type="submit" style="margin-top: 20px;">Uložit změny</button>
                    </div>
                </form>

                <div class="form-buttons">
                    <a href="logout.php" style="width:100%; text-decoration:none;">
                        <button type="button" class="logout-btn">Odhlásit se</button>
                    </a>
                </div>
            </div>
        </section>
    </main>
</body>
</html>