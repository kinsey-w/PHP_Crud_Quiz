<?php
require '../auth.php';
require '../bdd.php';

if (!isAdmin()) {
    die("Accès refusé.");
}

if (isset($_POST['create_quiz'])) {
    $title = htmlspecialchars($_POST['title']);
    $description = htmlspecialchars($_POST['description']);
    $slug = htmlspecialchars($_POST['slug']);

    $stmt = $connexion->prepare(
        'INSERT INTO quizzes (title, description, slug) VALUES (:title, :description, :slug)'
    );
    $stmt->execute(['title' => $title, 'description' => $description, 'slug' => $slug]);

    echo "<p>Quiz '{$title}' créé avec succès !</p>";
}

$quizzes = $connexion->query("SELECT * FROM quizzes")->fetchAll();

if (isset($_POST['add_question']) && !empty($_POST['quiz_id'])) {
    $quiz_id = $_POST['quiz_id'];
    $question_text = htmlspecialchars($_POST['question_text']);
    $question_type = $_POST['question_type']; 

    $stmt = $connexion->prepare(
        'INSERT INTO questions (quiz_id, question_text) VALUES (:quiz_id, :question_text)'
    );
    $stmt->execute(['quiz_id' => $quiz_id, 'question_text' => $question_text]);

    $question_id = $connexion->lastInsertId();

    if ($question_type == 'multiple') {
        for ($i = 1; $i <= 4; $i++) {
            $choice_text = htmlspecialchars($_POST["choice_$i"]);
            $is_correct = isset($_POST["correct"]) && $_POST["correct"] == $i ? 1 : 0;

            $stmt = $connexion->prepare(
                'INSERT INTO choices (question_id, choice_text, is_correct) VALUES (:question_id, :choice_text, :is_correct)'
            );
            $stmt->execute(['question_id' => $question_id, 'choice_text' => $choice_text, 'is_correct' => $is_correct]);
        }
    } elseif ($question_type == 'true_false') {
        $true_is_correct = $_POST['correct'] == 'true' ? 1 : 0;
        $false_is_correct = $_POST['correct'] == 'false' ? 1 : 0;

        $stmt = $connexion->prepare(
            'INSERT INTO choices (question_id, choice_text, is_correct) VALUES (:question_id, "True", :is_correct)'
        );
        $stmt->execute(['question_id' => $question_id, 'is_correct' => $true_is_correct]);

        $stmt = $connexion->prepare(
            'INSERT INTO choices (question_id, choice_text, is_correct) VALUES (:question_id, "False", :is_correct)'
        );
        $stmt->execute(['question_id' => $question_id, 'is_correct' => $false_is_correct]);
    }

    echo "<p>Question ajoutée au quiz avec succès !</p>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Créer quiz / questions</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include '../header.php'; ?>

    <div class="container">
        <h1>Créer un quiz / questions</h1>

        <section>
            <h2>Créer un nouveau quiz:</h2>
            <form method="POST">
                <label for="title">Titre du quiz:</label>
                <input type="text" name="title" id="title" required>

                <label for="description">Description:</label>
                <textarea name="description" id="description" rows="4"></textarea>

                <label for="slug">Slug (identifiant unique):</label>
                <input type="text" name="slug" id="slug" required>

                <button type="submit" name="create_quiz" class="btn">Créer le quiz</button>
            </form>
        </section>

        <section>
            <h2>Ajouter une question à un quiz existant:</h2>
            <form method="POST">
                <label for="quiz_id">Choisir un quiz:</label>
                <select name="quiz_id" id="quiz_id" required>
                    <option value="">-- Choisir un quiz --</option>
                    <?php foreach ($quizzes as $quiz): ?>
                        <option value="<?= $quiz['id']; ?>"><?= htmlspecialchars($quiz['title']); ?></option>
                    <?php endforeach; ?>
                </select>

                <label for="question_text">Question:</label>
                <textarea name="question_text" id="question_text" rows="3" required></textarea>

                <label for="question_type">Type de question:</label>
                <select name="question_type" id="question_type" onchange="toggleAnswerFields()" required>
                    <option value="multiple">Choix Multiple</option>
                    <option value="true_false">Vrai ou Faux</option>
                </select>

                <div id="multiple_choice_fields" style="display: block;">
                    <?php for ($i = 1; $i <= 4; $i++): ?>
                        <label for="choice_<?= $i ?>">Choix <?= $i ?>:</label>
                        <input type="text" name="choice_<?= $i ?>" id="choice_<?= $i ?>" required>
                    <?php endfor; ?>

                    <label>Bonne réponse:</label>
                    <select name="correct" required>
                        <option value="1">Choix 1</option>
                        <option value="2">Choix 2</option>
                        <option value="3">Choix 3</option>
                        <option value="4">Choix 4</option>
                    </select>
                </div>

                <div id="true_false_fields" style="display: none;">
                    <label>Bonne réponse:</label>
                    <select name="correct" required>
                        <option value="true">Vrai</option>
                        <option value="false">Faux</option>
                    </select>
                </div>

                <button type="submit" name="add_question" class="btn">Ajouter Question</button>
            </form>
        </section>
    </div>
<script src="../script.js"></script>
</body>
</html>

