<?php
require '../auth.php';
require '../bdd.php'; 

$stmt = $connexion->query("SELECT * FROM quizzes");
$quizzes = $stmt->fetchAll();

$userId = $_SESSION['user_id']; 

$stmt = $connexion->query("SELECT * FROM quizzes");
$quizzes = $stmt->fetchAll();

$stmt = $connexion->prepare("SELECT quiz_id, score, date_taken FROM results WHERE user_id = :user_id");
$stmt->execute(['user_id' => $userId]);
$results = $stmt->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Quiz disponibles</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <?php include '../header.php'; ?>

    <div class="container">
        <h1>Quiz disponibles</h1>

        <?php if (count($quizzes) > 0): ?>
            <div class="quiz-list">
                <?php foreach ($quizzes as $quiz): ?>
                    <div class="quiz-item">
                        <h3><?= htmlspecialchars($quiz['title']); ?></h3>
                        <p><?= htmlspecialchars($quiz['description']); ?></p>
                        <a href="quiz.php?slug=<?= htmlspecialchars($quiz['slug']); ?>" class="btn">Faire le quiz</a>

                        <?php if (isset($results[$quiz['id']])): ?>
                            <div class="resultat-precedent">
                            <h4>Résultats précédents :</h4>
                            <ul>
                                <?php foreach ($results[$quiz['id']] as $result): ?>
                                    <li>
                                        Score : <?= htmlspecialchars($result['score']); ?> / Date : <?= htmlspecialchars($result['date_taken']); ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>Aucun quiz disponible pour le moment.</p>
        <?php endif; ?>
    </div>
</body>

</html>