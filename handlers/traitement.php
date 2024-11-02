<?php 
session_start();

if (!isset($_POST['token']) || $_POST['token'] != $_SESSION['csrf_quiz_add']) {
    die('<p>Invalid CSRF token</p>');
}

unset($_SESSION['csrf_quiz_add']);

if (isset($_POST['title']) && !empty($_POST['title'])) {
    $title = htmlspecialchars($_POST['title']);
} else {
    echo "<p>The title is required</p>";
}

if (isset($_POST['description']) && !empty($_POST['description'])) {
    $description = htmlspecialchars($_POST['description']);
} else {
    echo "<p>The description is required</p>";
}

if (isset($_POST['slug']) && !empty($_POST['slug'])) {
    $slug = htmlspecialchars($_POST['slug']);
} else {
    echo "<p>The slug is required</p>";
}

if (isset($title) && isset($description) && isset($slug)) {
    echo "<p>Saving quiz...</p>";
    
    require_once 'bdd.php';

    $saveQuiz = $connexion->prepare(
        'INSERT INTO quizzes (title, description, slug) VALUES (:title, :description, :slug)'
    );
    
    $saveQuiz->execute([
        'title' => $title,
        'description' => $description,
        'slug' => $slug
    ]);

    if ($saveQuiz->rowCount() > 0) {
        echo "<p>Quiz successfully saved</p>";
    } else {
        echo "<p>An error occurred while saving</p>";
    }
}