<?php
session_start(); 
require 'auth.php'; 
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Accueil Quiz</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <?php include 'header.php'; ?>
    <div class="container">
        <div class="home-title">
            <h1>Bienvenue sur la plateforme de quiz!</h1>
            <p>Mettez-vous au défi, apprenez de nouvelles choses et amusez-vous !</p>
        </div>

        <a href="quizzes/quiz_list.php" class="btn">Voir les quiz</a>

        <?php if (isset($_SESSION['user_id']) && isAdmin()): ?>
            <div class="admin-links">
                <a href="admin/admin_add_quiz.php" class="btn">Créer un quiz</a>
                <a href="admin/admin_manage_content.php" class="btn">Gérer les quiz</a>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>