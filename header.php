   <header>
       <nav class="navbar">
           <a href="/index.php" class="nav-link">Accueil</a>
           <a href="/quizzes/quiz_list.php" class="nav-link">Voir les quiz</a>

           <?php if (isset($_SESSION['user_id'])): ?>
               <?php if (isAdmin()): ?>
                   <a href="/admin/admin_add_quiz.php" class="nav-link">Ajouter un quiz/question</a>
                   <a href="/admin/admin_manage_content.php" class="nav-link">Gérer le contenu</a>
               <?php endif; ?>
               <a href="/auth/logout.php" class="nav-link">Déconnexion</a>
           <?php else: ?>
               <a href="/auth/login.php" class="nav-link">Connexion</a>
               <a href="/auth/register.php" class="nav-link">Inscription</a>
           <?php endif; ?>
       </nav>
   </header>