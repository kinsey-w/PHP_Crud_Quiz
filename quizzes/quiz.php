<?php 

if (!isset($_GET['slug']) || empty($_GET['slug'])) {
    die('<p>Quiz introuvable</p>');
}

require '../auth.php';
require_once '../bdd.php';

$getQuiz = $connexion->prepare(
    'SELECT id, title, description FROM quizzes WHERE slug = :slug LIMIT 1'
);
$getQuiz->execute(['slug' => htmlspecialchars($_GET['slug'])]);

if ($getQuiz->rowCount() == 1) {
    $quiz = $getQuiz->fetch();
} else {
    die('<p>Quiz introuvable</p>');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($quiz['title']) ?></title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include '../header.php'; ?>

<div class="container">
    <div class="title">
        <h1><?= htmlspecialchars($quiz['title']) ?></h1>
        <p><?= htmlspecialchars($quiz['description']) ?></p>
    </div>

    <?php
    $getQuestions = $connexion->prepare(
        'SELECT id, question_text FROM questions WHERE quiz_id = :quiz_id'
    );
    $getQuestions->execute(['quiz_id' => $quiz['id']]);

    $submitted = !empty($_POST);
    $score = 0;
    $totalQuestions = 0;

    echo '<form method="POST" action="?slug=' . htmlspecialchars($_GET['slug']) . '">';

    while ($question = $getQuestions->fetch()) {
        $totalQuestions++;
        echo '<p>', htmlspecialchars($question['question_text']), '</p>';

        $getChoices = $connexion->prepare(
            'SELECT id, choice_text, is_correct FROM choices WHERE question_id = :question_id'
        );
        $getChoices->execute(['question_id' => $question['id']]);
        
        $userAnswer = $_POST['question_' . $question['id']] ?? null;
        $correctChoiceId = null;
        $correctChoiceText = null;

        while ($choice = $getChoices->fetch()) {
            $choiceId = $choice['id'];
            $choiceText = htmlspecialchars($choice['choice_text']);
            $isCorrect = $choice['is_correct'];
            
            if ($isCorrect) {
                $correctChoiceId = $choiceId;
                $correctChoiceText = $choiceText;
            }

            echo '<label>';
            echo '<input type="radio" name="question_', $question['id'], '" value="', $choiceId, '"',
                 ($submitted && $userAnswer == $choiceId ? ' checked' : ''), '>';
            echo $choiceText;
            echo '</label>';
        }

        if ($submitted) {
            if ($userAnswer && $userAnswer == $correctChoiceId) {
                echo "<p style='color: green;'>Correct!</p>";
                $score++;
            } else {
                echo "<p style='color: red;'>Incorrect. La bonne réponse est : <strong>", htmlspecialchars_decode($correctChoiceText), "</strong></p>";
            }
        }

        echo '<hr>';
    }

    echo '<button type="submit" class="btn">Finir le quiz</button>';
    echo '</form>';

    if ($submitted) {
        echo "<p class='result'>Votre score final est : $score sur $totalQuestions</p>";
    
        $userId = $_SESSION['user_id'];
        $quizId = $quiz['id'];
        $dateTaken = date('Y-m-d H:i:s');
    
        $saveResult = $connexion->prepare("INSERT INTO results (user_id, quiz_id, score, date_taken) VALUES (:user_id, :quiz_id, :score, :date_taken)");
        $saveResult->execute([
            'user_id' => $userId,
            'quiz_id' => $quizId,
            'score' => $score,
            'date_taken' => $dateTaken
        ]);
    }
    
    ?>
</div>
</body>
</html>
