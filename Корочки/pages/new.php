<?php
session_start();
require_once __DIR__ . '/../helpers/db.php';

if (empty($_SESSION['user_id']) || ($_SESSION['roli_id'] ?? 0) == 2) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kursi_id    = (int)($_POST['kursi_id'] ?? 0);
    $data_nach   = trim($_POST['data_nach'] ?? '');
    $oplati_id   = (int)($_POST['oplati_id'] ?? 0);
    $kommentariy = trim($_POST['kommentariy'] ?? '');

    $errors = [];

    if ($kursi_id <= 0 || $oplati_id <= 0 || $data_nach === '') {
        $errors[] = 'Заполните все поля';
    } else {
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data_nach) || !strtotime($data_nach)) {
            $errors[] = 'Выберите корректную дату';
        }
        if (mb_strlen($kommentariy) > 500) {
            $errors[] = 'Комментарий слишком длинный (макс. 500 символов)';
        }
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("
            INSERT INTO zayavki (kursi_id, data_nach, kommentariy, oplati_id, statysi_id, polzovateli_id)
            VALUES (?, ?, ?, ?, 1, ?)
        ");
        $stmt->execute([$kursi_id, $data_nach, $kommentariy, $oplati_id, $_SESSION['user_id']]);
        header('Location: zayavki.php');
        exit;
    } else {
        $_SESSION['errors'] = $errors;
        $_SESSION['old']    = compact('kursi_id', 'data_nach', 'oplati_id', 'kommentariy');
        header('Location: new.php');
        exit;
    }
}

$kursi  = $pdo->query("SELECT id_kursi, nazvanie_kursa FROM kursi")->fetchAll(PDO::FETCH_ASSOC);
$oplati = $pdo->query("SELECT id_oplati, sposob_oplati FROM oplati")->fetchAll(PDO::FETCH_ASSOC);

require_once __DIR__ . '/layout.php';

$errors = $_SESSION['errors'] ?? [];
$old    = $_SESSION['old'] ?? [];
unset($_SESSION['errors'], $_SESSION['old']);
?>

<h2 class="page-title">Добавление заявки</h2>

<div class="auth-form">
    <?php if (!empty($errors)): ?>
        <ul class="error">
            <?php foreach ($errors as $e): ?>
                <li><?= htmlspecialchars($e) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form method="post" action="new.php">
        <label for="kursi_id">Наименование курса</label>
        <select id="kursi_id" name="kursi_id" required>
            <option value="">— выберите курс —</option>
            <?php foreach ($kursi as $k): ?>
                <option value="<?= $k['id_kursi'] ?>" <?= (($old['kursi_id'] ?? 0) == $k['id_kursi']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($k['nazvanie_kursa']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="oplati_id">Способ оплаты</label>
        <select id="oplati_id" name="oplati_id" required>
            <option value="">— выберите способ —</option>
            <?php foreach ($oplati as $o): ?>
                <option value="<?= $o['id_oplati'] ?>" <?= (($old['oplati_id'] ?? 0) == $o['id_oplati']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($o['sposob_oplati']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="data_nach">Дата начала обучения</label>
        <input type="date" id="data_nach" name="data_nach" required min="<?= date('Y-m-d') ?>" value="<?= htmlspecialchars($old['data_nach'] ?? '') ?>">

        <label for="kommentariy">Комментарий (необязательно)</label>
        <textarea id="kommentariy" name="kommentariy" rows="4" maxlength="500"><?= htmlspecialchars($old['kommentariy'] ?? '') ?></textarea>

        <button type="submit">Отправить заявку</button>
    </form>

    <p>Заявка уже создана? <a href="zayavki.php">Посмотреть</a></p>
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