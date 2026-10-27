<?php
session_start();
require_once __DIR__ . '/../helpers/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login'] ?? '');
    $parol = trim($_POST['parol'] ?? '');

    if ($login === '' || $parol === '') {
        $_SESSION['error'] = 'Заполните все поля';
        header('Location: auth.php');
        exit;
    }

    $stmt = $pdo->prepare("SELECT * FROM polzovateli WHERE login = ?");
    $stmt->execute([$login]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && md5($parol) === $user['parol']) {
        $_SESSION['user_id']  = $user['id_polzovateli'];
        $_SESSION['fio']      = $user['fio'];
        $_SESSION['roli_id']  = $user['roli_id'];
        header('Location: index.php');
        exit;
    } else {
        $_SESSION['error'] = 'Неверный логин или пароль';
        header('Location: auth.php');
        exit;
    }
}

require_once __DIR__ . '/layout.php';
?>

<h2 class="page-title">Авторизация</h2>

<div class="auth-form">
    <?php if (!empty($_SESSION['error'])): ?>
        <p class="error"><?= htmlspecialchars($_SESSION['error']) ?></p>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <form method="post" action="auth.php">
        <label for="login">Логин</label>
        <input type="text" id="login" name="login" required>

        <label for="parol">Пароль</label>
        <input type="password" id="parol" name="parol" required>

        <button type="submit">Войти в аккаунт</button>
    </form>

    <p>Ещё не зарегистрированы? <a href="register.php">Регистрация</a></p>
</div>

</main>
    <footer>
        <div class="footer-inner">
            <div class="footer-contacts">
                <p><b>Контакты:</b></p>
                <p>Телефон: 8(800)555-35-35</p>
                <p>Email: info@korochki.est</p>
                <p>Адрес: г. Москва, ул. Программистов, д. 1</p>
            </div>
            <div class="footer-copy">
                &copy; <?= date('Y') ?> Корочки.есть. Все права защищены.
            </div>
        </div>
    </footer>
    <script src="../scripts/script.js"></script>
</body>
</html>