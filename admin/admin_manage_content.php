<?php
require '../auth.php';
require '../bdd.php';

if (!isAdmin()) {
    die("Accès refusé.");
}

if (isset($_GET['delete_quiz']) && !empty($_GET['delete_quiz'])) {
    $quiz_slug = $_GET['delete_quiz'];
    $stmt = $connexion->prepare("DELETE FROM quizzes WHERE slug = :slug");
    $stmt->execute(['slug' => $quiz_slug]);
    echo "<script>console.log('Quiz supprimé avec succès !');</script>";
}

if (isset($_GET['delete_question']) && !empty($_GET['delete_question'])) {
    $question_id = intval($_GET['delete_question']);
    $stmt = $connexion->prepare("DELETE FROM questions WHERE id = :id");
    $stmt->execute(['id' => $question_id]);
    echo "<script>console.log('Question supprimée avec succès !');</script>";
}

$quizzes = $connexion->query("SELECT * FROM quizzes")->fetchAll();

$selectedQuiz = null;
$questions = [];
if (isset($_GET['edit_quiz']) && !empty($_GET['edit_quiz'])) {
    $quiz_slug = $_GET['edit_quiz'];
    $stmt = $connexion->prepare("SELECT * FROM quizzes WHERE slug = :slug");
    $stmt->execute(['slug' => $quiz_slug]);
    $selectedQuiz = $stmt->fetch();

    if ($selectedQuiz) {
        $stmt = $connexion->prepare("SELECT * FROM questions WHERE quiz_id = :quiz_id");
        $stmt->execute(['quiz_id' => $selectedQuiz['id']]);
        $questions = $stmt->fetchAll();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Gérer les quiz et les questions</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include '../header.php'; ?>

    <div class="container">
        <h1>Gérer les quiz et les questions</h1>
        
        <h2>Tous les quiz</h2>
        <?php if (count($quizzes) > 0): ?>
            <ul>
                <?php foreach ($quizzes as $quiz): ?>
                    <li>
                        <strong><?= htmlspecialchars($quiz['title']); ?></strong> 
                        <a href="admin_manage_content.php?edit_quiz=<?= $quiz['slug']; ?>" class="btn">Modifier</a>
                        <a href="admin_manage_content.php?delete_quiz=<?= $quiz['slug']; ?>" class="btn">Supprimer</a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No quizzes available.</p>
        <?php endif; ?>

        <?php if ($selectedQuiz): ?>
            <section>
            <h2>Modifier Quiz: <?= htmlspecialchars($selectedQuiz['title']); ?></h2>
            <form method="POST" action="edit_quiz.php?id=<?= $selectedQuiz['id']; ?>">
                <label for="title">Titre du quiz:</label>
                <input type="text" name="title" id="title" value="<?= htmlspecialchars($selectedQuiz['title']); ?>" required>

                <label for="description">Description:</label>
                <textarea name="description" id="description" rows="4"><?= htmlspecialchars($selectedQuiz['description']); ?></textarea>

                <button type="submit" class="btn">Mettre à jour le quiz</button>
            </form>
            </section>

            <section>
            <h3>Questions</h3>
            <?php if (count($questions) > 0): ?>
                <ul>
                    <?php foreach ($questions as $question): ?>
                        <li>
                            <strong><?= htmlspecialchars($question['question_text']); ?></strong>
                            <a href="edit_question.php?id=<?= $question['id']; ?>" class="btn">Modifier</a>
                            <a href="admin_manage_content.php?edit_quiz=<?= $selectedQuiz['id']; ?>&delete_question=<?= $question['id']; ?>" class="btn">Supprimer</a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>Aucune question disponible pour ce quiz.</p>
            <?php endif; ?>
            </section>
        <?php endif; ?>
    </div>
</body>
</html>
