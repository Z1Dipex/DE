<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
require_once __DIR__ . '/../helpers/db.php';

if (empty($_SESSION['user_id']) || ($_SESSION['roli_id'] ?? 0) == 2) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inn = trim($_POST['inn'] ?? '');
    $tip_id = (int)($_POST['tip_id'] ?? 0);
    $period_nachalo = trim($_POST['period_nachalo'] ?? '');
    $period_konec = trim($_POST['period_konec'] ?? '');
    $dolzhnost = trim($_POST['dolzhnost'] ?? '');
    
    $errors = [];
    if ($inn === '' || $tip_id <= 0 || $period_nachalo === '' || $period_konec === '' || $dolzhnost === '') {
        $errors[] = 'Заполните все поля, включая организацию и даты';
    } elseif (strtotime($period_nachalo) > strtotime($period_konec)) {
        $errors[] = 'Дата окончания не может быть раньше даты начала';
    }
    
    // Обработка файла
    $filePath = '';
    if (isset($_FILES['hodataistvo']) && $_FILES['hodataistvo']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['hodataistvo'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        if (!in_array($ext, ['pdf', 'jpg', 'jpeg']) || $file['size'] > 10 * 1024 * 1024) {
            $errors[] = 'Ходатайство: только PDF/JPG, до 10 МБ';
        } else {
            $uploadDir = __DIR__ . '/../uploads/hodataystva/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
            
            if (!is_writable($uploadDir)) {
                $errors[] = 'Ошибка: нет прав на запись в папку uploads.';
            } else {
                $fileName = uniqid('hod_') . '.' . $ext;
                if (move_uploaded_file($file['tmp_name'], $uploadDir . $fileName)) {
                    $filePath = '/uploads/hodataystva/' . $fileName;
                } else {
                    $errors[] = 'Не удалось переместить файл.';
                }
            }
        }
    } else {
        $errors[] = 'Загрузите ходатайство.';
    }

    if (empty($errors)) {
        // ОБРАТИ ВНИМАНИЕ: используем period_nachalo и period_konec
        $stmt = $pdo->prepare("INSERT INTO zayavki (inn, period_nachalo, period_konec, tip_id, dolzhnost, statysi_id, hodataistvo, polzovateli_id) VALUES (?, ?, ?, ?, ?, 1, ?, ?)");
        $stmt->execute([$inn, $period_nachalo, $period_konec, $tip_id, $dolzhnost, $filePath, $_SESSION['user_id']]);
        
        $_SESSION['toast'] = ['text' => 'Заявка успешно создана!', 'type' => 'success'];
        header('Location: zayavki.php');
        exit;
    } else {
        $_SESSION['errors'] = $errors;
        $_SESSION['old'] = compact('inn', 'tip_id', 'period_nachalo', 'period_konec', 'dolzhnost');
        header('Location: new.php');
        exit;
    }
}

$orgs = $pdo->query("SELECT inn, nazvanie_organizacii FROM organizacii ORDER BY nazvanie_organizacii")->fetchAll(PDO::FETCH_ASSOC);
$tips = $pdo->query("SELECT id_tip, nazvanie_tipa FROM tipi_praktiki")->fetchAll(PDO::FETCH_ASSOC);

$orgsForJs = [];
foreach ($orgs as $o) {
    $orgsForJs[] = ['inn' => $o['inn'], 'name' => $o['nazvanie_organizacii']];
}

require_once __DIR__ . '/layout.php';
$errors = $_SESSION['errors'] ?? [];
$old = $_SESSION['old'] ?? [];
unset($_SESSION['errors'], $_SESSION['old']);
?>

<h2 class="page-title">Создание заявки</h2>
<div class="auth-form">
    <?php if (!empty($errors)): ?>
        <ul class="error">
            <?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form method="post" action="new.php" enctype="multipart/form-data">
        <div class="autocomplete">
            <label for="org_search">Наименование организации (поиск по ИНН)</label>
            <input type="hidden" id="inn" name="inn" value="<?= htmlspecialchars($old['inn'] ?? '') ?>">
            <input type="text" id="org_search" placeholder="Введите ИНН или название..." autocomplete="off" required 
                   value="<?php 
                        if (!empty($old['inn'])) {
                            foreach ($orgs as $o) {
                                if ($o['inn'] == $old['inn']) echo htmlspecialchars($o['nazvanie_organizacii'] . ' (ИНН: ' . $o['inn'] . ')');
                            }
                        }
                   ?>">
            <div id="autocomplete-list" class="autocomplete-items"></div>
        </div>

        <label for="tip_id">Вид практики</label>
        <select id="tip_id" name="tip_id" required>
            <option value="">— выберите вид —</option>
            <?php foreach ($tips as $t): ?>
                <option value="<?= $t['id_tip'] ?>" <?= (($old['tip_id'] ?? 0) == $t['id_tip']) ? 'selected' : '' ?>><?= htmlspecialchars($t['nazvanie_tipa']) ?></option>
            <?php endforeach; ?>
        </select>

        <label for="period_nachalo">Период практики (дата начала)</label>
        <input type="date" id="period_nachalo" name="period_nachalo" required value="<?= htmlspecialchars($old['period_nachalo'] ?? '') ?>">

        <label for="period_konec">Период практики (дата окончания)</label>
        <input type="date" id="period_konec" name="period_konec" required value="<?= htmlspecialchars($old['period_konec'] ?? '') ?>">

        <label for="dolzhnost">Должность</label>
        <input type="text" id="dolzhnost" name="dolzhnost" required value="<?= htmlspecialchars($old['dolzhnost'] ?? '') ?>">

        <label for="hodataistvo">Ходатайство от организации (PDF/JPG, до 10 МБ)</label>
        <input type="file" id="hodataistvo" name="hodataistvo" accept=".pdf,.jpg,.jpeg" required>

        <button type="submit">Отправить</button>
    </form>
</div>

</main>
<footer>
    <div class="footer-inner">
        <div class="footer-contacts">
            <p><b>Контакты:</b></p><p>Телефон: 8(800)555-35-35</p><p>Email: info@dogovory-praktiki.ru</p>
        </div>
        <div class="footer-copy">&copy; <?= date('Y') ?> Договоры практики</div>
    </div>
</footer>

<script>
    window.organizationsData = <?= json_encode($orgsForJs, JSON_UNESCAPED_UNICODE) ?>;
</script>
<script src="../scripts/script.js"></script>
</body>
</html>