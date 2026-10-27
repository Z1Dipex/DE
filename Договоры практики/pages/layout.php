<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Договоры практики</title>
    <link rel="stylesheet" href="../styles/style.css">
</head>
<body>
    <header>
        <div class="header-inner">
            <div class="logo">
                <img src="../media/logo.png" alt="Логотип" width="110" height="110" onerror="this.src='../media/image02.jpg'">
                <span class="site-title">Договоры практики</span>
            </div>
            <button class="burger" id="burgerBtn" aria-label="Меню">
                <span></span><span></span><span></span>
            </button>
            <div class="header-right" id="mobileMenu">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <span class="user-info"><?= htmlspecialchars($_SESSION['fio']) ?></span>
                    <?php if (($_SESSION['roli_id'] ?? 0) == 2): ?>
                        <a href="admin.php">Панель администратора</a>
                    <?php else: ?>
                        <a href="zayavki.php">Мои заявки</a>
                        <a href="new.php">Создать заявку</a>
                    <?php endif; ?>
                    <a href="logout.php" class="logout-btn">Выйти</a>
                <?php else: ?>
                    <a href="auth.php">Вход</a>
                    <a href="register.php">Регистрация</a>
                <?php endif; ?>
            </div>
        </div>
        <div class="search-box">
            <input type="text" placeholder="Поиск...">
        </div>
        <hr>
        <nav>
            <a href="index.php">Главная</a>
            <?php if (isset($_SESSION['user_id']) && ($_SESSION['roli_id'] ?? 0) != 2): ?>
                <a href="zayavki.php">Мои заявки</a>
            <?php endif; ?>
        </nav>
    </header>
    <main>