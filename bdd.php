<?php

try {
    $connexion = new PDO('mysql:host=localhost;dbname=...', '...', '...'); 
} catch (PDOException $e) {
    die($e->getMessage());
}
