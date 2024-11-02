<?php
require '../auth.php';
require '../bdd.php';

if (!isAdmin()) {
    die("Accès refusé.");
}

$quiz_id = $_GET['id'];
$stmt = $connexion->prepare("SELECT * FROM quizzes WHERE id = :id");
$stmt->execute(['id' => $quiz_id]);
$quiz = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = htmlspecialchars($_POST['title']);
    $description = htmlspecialchars($_POST['description']);

    $stmt = $connexion->prepare("UPDATE quizzes SET title = :title, description = :description WHERE id = :id");
    $stmt->execute(['title' => $title, 'description' => $description, 'id' => $quiz_id]);

    echo "<p class='info'>Quiz mis à jour avec succès !</p>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Modifier Quiz</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include '../header.php'; ?>

    <div class="container">
        <h1>Modifier Quiz</h1>
        <form method="POST">
            <label for="title">Titre du quiz:</label>
            <input type="text" name="title" id="title" value="<?= htmlspecialchars($quiz['title']); ?>" required>

            <label for="description">Description:</label>
            <textarea name="description" id="description" rows="4"><?= htmlspecialchars($quiz['description']); ?></textarea>

            <button type="submit" class="btn">Mettre à jour</button>
        </form>
    </div>
</body>
</html>
