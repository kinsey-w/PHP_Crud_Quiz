<?php
require '../bdd.php';

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = htmlspecialchars(trim($_POST['username']));
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $error_message = 'Passwords do not match.';
    } else {
        $stmt = $connexion->prepare("SELECT id FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        if ($stmt->fetch()) {
            $error_message = 'Username already taken. Please choose another.';
        } else {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $connexion->prepare("INSERT INTO users (username, password, role) VALUES (:username, :password, 'user')");
            $stmt->execute(['username' => $username, 'password' => $hashed_password]);

            header('Location: login.php');
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Inscription</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <div class="container">
        <h1>Inscription</h1>

        <?php if ($error_message): ?>
            <p style="color: red;"><?= htmlspecialchars($error_message); ?></p>
        <?php endif; ?>

        <form method="POST" action="register.php">
            <label for="username">Nom d'utilisateur :</label>
            <input type="text" name="username" id="username" required>

            <label for="password">Mot de passe :</label>
            <input type="password" name="password" id="password" required>

            <label for="confirm_password">Confirmer le mot de passe :</label>
            <input type="password" name="confirm_password" id="confirm_password" required>

            <button type="submit" class="btn">S'inscrire</button>

            <p>Déjà un compte ? <a href="login.php">Connectez-vous ici</a>.</p>

        </form>

    </div>
</body>

</html>