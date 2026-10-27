<?php
session_start();
require_once __DIR__ . '/../helpers/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login    = trim($_POST['login'] ?? '');
    $parol    = trim($_POST['parol'] ?? '');
    $parol2   = trim($_POST['parol2'] ?? '');
    $fio      = trim($_POST['fio'] ?? '');
    $telefon  = trim($_POST['telefon'] ?? '');
    $pochta   = trim($_POST['pochta'] ?? '');

    $errors = [];

    if ($login === '' || $parol === '' || $parol2 === '' || $fio === '' || $telefon === '' || $pochta === '') {
        $errors[] = 'Заполните все поля';
    } else {
        if (!preg_match('/^[A-Za-z0-9]{6,}$/', $login)) {
            $errors[] = 'Логин: латиница и цифры, не менее 6 символов';
        }
        if (mb_strlen($parol) < 8) {
            $errors[] = 'Пароль: минимум 8 символов';
        }
        if ($parol !== $parol2) {
            $errors[] = 'Пароли не совпадают';
        }
        if (!preg_match('/^[А-Яа-яЁёA-Za-z\s\-]+$/u', $fio)) {
            $errors[] = 'ФИО должно содержать только буквы';
        }
        if (!filter_var($pochta, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Введите корректный адрес электронной почты';
        }
        if (!preg_match('/^8\(\d{3}\)\d{3}-\d{2}-\d{2}$/', $telefon)) {
            $errors[] = 'Введите корректный номер телефона';
        }
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id_polzovateli FROM polzovateli WHERE login = ?");
        $stmt->execute([$login]);
        if ($stmt->fetch()) {
            $errors[] = 'Пользователь с таким логином уже существует';
        }
    }

    if (empty($errors)) {
        $hash = md5($parol);
        $stmt = $pdo->prepare("
            INSERT INTO polzovateli (login, parol, fio, telefon, pochta, roli_id)
            VALUES (?, ?, ?, ?, ?, 1)
        ");
        $stmt->execute([$login, $hash, $fio, $telefon, $pochta]);

        $_SESSION['user_id'] = $pdo->lastInsertId();
        $_SESSION['fio']     = $fio;
        $_SESSION['roli_id'] = 1;

        header('Location: index.php');
        exit;
    } else {
        $_SESSION['errors'] = $errors;
        $_SESSION['old']    = compact('login', 'fio', 'telefon', 'pochta');
        header('Location: register.php');
        exit;
    }
}

require_once __DIR__ . '/layout.php';

$errors = $_SESSION['errors'] ?? [];
$old    = $_SESSION['old'] ?? [];
unset($_SESSION['errors'], $_SESSION['old']);
?>

<h2 class="page-title">Регистрация</h2>

<div class="auth-form">
    <?php if (!empty($errors)): ?>
        <ul class="error">
            <?php foreach ($errors as $e): ?>
                <li><?= htmlspecialchars($e) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form method="post" action="register.php">
        <label for="login">Логин</label>
        <input type="text" id="login" name="login" required value="<?= htmlspecialchars($old['login'] ?? '') ?>">

        <label for="parol">Пароль</label>
        <input type="password" id="parol" name="parol" required>

        <label for="parol2">Подтверждение пароля</label>
        <input type="password" id="parol2" name="parol2" required>

        <label for="fio">ФИО</label>
        <input type="text" id="fio" name="fio" required value="<?= htmlspecialchars($old['fio'] ?? '') ?>">

        <label for="pochta">Адрес эл. почты</label>
        <input type="email" id="pochta" name="pochta" required value="<?= htmlspecialchars($old['pochta'] ?? '') ?>">

        <label for="telefon">Телефон</label>
        <input type="text" id="telefon" name="telefon" required placeholder="8(XXX)XXX-XX-XX" maxlength="16" value="<?= htmlspecialchars($old['telefon'] ?? '') ?>">

        <button type="submit">Зарегистрироваться</button>
    </form>

    <p>Уже есть аккаунт? <a href="auth.php">Войти</a></p>
</div>

</main>
<footer>
    <div class="footer-inner">
        <div class="footer-contacts">
            <p><b>Контакты:</b></p>
            <p>Телефон: 8(800)555-35-35</p>
            <p>Email: info@korochki.est</p>
        </div>
        <div class="footer-copy">&copy; <?= date('Y') ?> Корочки.есть</div>
    </div>
</footer>
<script src="../scripts/script.js"></script>
</body>
</html>