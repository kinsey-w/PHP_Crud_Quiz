<?php
require '../auth.php';
require '../bdd.php';

if (!isAdmin()) {
    die("Accès refusé.");
}

$question_id = $_GET['id'];
$stmt = $connexion->prepare("SELECT * FROM questions WHERE id = :id");
$stmt->execute(['id' => $question_id]);
$question = $stmt->fetch();

$stmt = $connexion->prepare("SELECT * FROM choices WHERE question_id = :id");
$stmt->execute(['id' => $question_id]);
$choices = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $question_text = htmlspecialchars($_POST['question_text']);

    $stmt = $connexion->prepare("UPDATE questions SET question_text = :question_text WHERE id = :id");
    $stmt->execute(['question_text' => $question_text, 'id' => $question_id]);

    foreach ($choices as $index => $choice) {
        $choice_text = htmlspecialchars($_POST["choice_$index"]);
        $is_correct = ($_POST["correct"] == $index) ? 1 : 0;

        $stmt = $connexion->prepare("UPDATE choices SET choice_text = :choice_text, is_correct = :is_correct WHERE id = :id");
        $stmt->execute(['choice_text' => $choice_text, 'is_correct' => $is_correct, 'id' => $choice['id']]);
    }

    echo "<p class='info'>Question updated successfully!</p>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Modifier Question</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include '../header.php'; ?>

    <div class="container">
        <h1>Modifier Question</h1>
        <form method="POST">
            <label for="question_text">Question:</label>
            <textarea name="question_text" id="question_text" rows="3" required><?= htmlspecialchars($question['question_text']); ?></textarea>

            <?php foreach ($choices as $index => $choice): ?>
                <label for="choice_<?= $index ?>">Choix <?= $index + 1 ?>:</label>
                <input type="text" name="choice_<?= $index ?>" id="choice_<?= $index ?>" value="<?= htmlspecialchars($choice['choice_text']); ?>" required>
            <?php endforeach; ?>

            <label>Bonne réponse:</label>
            <select name="correct" required>
                <?php foreach ($choices as $index => $choice): ?>
                    <option value="<?= $index ?>" <?= $choice['is_correct'] ? 'selected' : '' ?>>Choix <?= $index + 1 ?></option>
                <?php endforeach; ?>
            </select>

            <button type="submit" class="btn">Mettre à jour la Question</button>
        </form>
    </div>
</body>
</html>
