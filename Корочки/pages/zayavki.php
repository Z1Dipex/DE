<?php
session_start();
require_once __DIR__ . '/../helpers/db.php';

if (empty($_SESSION['user_id'])) {
    header('Location: auth.php');
    exit;
}

// Админа разворачиваем на его панель
if (($_SESSION['roli_id'] ?? 0) == 2) {
    header('Location: admin.php');
    exit;
}

// Параметры фильтра
$statusFilter = (int)($_GET['status'] ?? 0);
$kursFilter   = (int)($_GET['kurs'] ?? 0);
$oplataFilter = (int)($_GET['oplata'] ?? 0);

// Пагинация
$perPage = 3;
$page = max(1, (int)($_GET['page'] ?? 1));

// Справочники
$statusi = $pdo->query("SELECT id_statusi, nazvanie_status FROM statusi")->fetchAll(PDO::FETCH_ASSOC);
$kursi   = $pdo->query("SELECT id_kursi, nazvanie_kursa FROM kursi")->fetchAll(PDO::FETCH_ASSOC);
$oplati  = $pdo->query("SELECT id_oplati, sposob_oplati FROM oplati")->fetchAll(PDO::FETCH_ASSOC);

// Условия
$where  = ["z.polzovateli_id = ?"];
$params = [$_SESSION['user_id']];

if ($statusFilter > 0) { $where[] = "z.statysi_id = ?"; $params[] = $statusFilter; }
if ($kursFilter   > 0) { $where[] = "z.kursi_id = ?";   $params[] = $kursFilter; }
if ($oplataFilter > 0) { $where[] = "z.oplati_id = ?";  $params[] = $oplataFilter; }
$whereSql = " WHERE " . implode(" AND ", $where);

// Общее количество для пагинации
$countStmt = $pdo->prepare("SELECT COUNT(*) FROM zayavki z" . $whereSql);
$countStmt->execute($params);
$total = (int)$countStmt->fetchColumn();
$totalPages = max(1, (int)ceil($total / $perPage));
if ($page > $totalPages) $page = $totalPages;
$offset = ($page - 1) * $perPage;

// Основной запрос (самые новые — первыми)
$sql = "
    SELECT z.id_zayavki, z.data_nach,
           k.nazvanie_kursa, o.sposob_oplati, s.nazvanie_status,
           p.fio, z.otziv_id, s.id_statusi,
           ot.komentariy, ot.kolichestvo_zvezd
    FROM zayavki z
    LEFT JOIN kursi        k  ON z.kursi_id        = k.id_kursi
    LEFT JOIN oplati       o  ON z.oplati_id       = o.id_oplati
    LEFT JOIN statusi      s  ON z.statysi_id      = s.id_statusi
    LEFT JOIN polzovateli  p  ON z.polzovateli_id  = p.id_polzovateli
    LEFT JOIN otzivi       ot ON z.otziv_id        = ot.id_otziv
    {$whereSql}
    ORDER BY z.id_zayavki DESC
    LIMIT {$perPage} OFFSET {$offset}
";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$zayavki = $stmt->fetchAll(PDO::FETCH_ASSOC);

$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['errors']);

require_once __DIR__ . '/layout.php';
?>

<h2 class="page-title">Просмотр заявок</h2>

<?php if (!empty($errors)): ?>
    <ul class="error">
        <?php foreach ($errors as $e): ?>
            <li><?= htmlspecialchars($e) ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<p><a href="new.php">+ Создать новую заявку</a></p>

<form method="get" action="zayavki.php" class="filter-form">
    <div class="filter-row">
        <label for="status">Статус:</label>
        <select id="status" name="status" onchange="this.form.submit()">
            <option value="0">Все статусы</option>
            <?php foreach ($statusi as $s): ?>
                <option value="<?= $s['id_statusi'] ?>" <?= ($statusFilter == $s['id_statusi']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($s['nazvanie_status']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="filter-row">
        <label for="kurs">Курс:</label>
        <select id="kurs" name="kurs" onchange="this.form.submit()">
            <option value="0">Все курсы</option>
            <?php foreach ($kursi as $k): ?>
                <option value="<?= $k['id_kursi'] ?>" <?= ($kursFilter == $k['id_kursi']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($k['nazvanie_kursa']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="filter-row">
        <label for="oplata">Оплата:</label>
        <select id="oplata" name="oplata" onchange="this.form.submit()">
            <option value="0">Все способы</option>
            <?php foreach ($oplati as $o): ?>
                <option value="<?= $o['id_oplati'] ?>" <?= ($oplataFilter == $o['id_oplati']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($o['sposob_oplati']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <?php if ($statusFilter || $kursFilter || $oplataFilter): ?>
        <a href="zayavki.php" class="reset-filter">Сбросить</a>
    <?php endif; ?>
</form>

<?php if (empty($zayavki)): ?>
    <p>Заявок пока нет.</p>
<?php else: ?>
    <div class="zayavki-list">
        <?php foreach ($zayavki as $z): ?>
            <div class="zayavka-card">
                <h3>Заявка №<?= $z['id_zayavki'] ?></h3>

                <p><b>Наименование курса:</b> <?= htmlspecialchars($z['nazvanie_kursa']) ?></p>
                <?php
                $statusClass = 'badge-unknown';
                if ($z['id_statusi'] == 1) $statusClass = 'badge-new';
                elseif ($z['id_statusi'] == 2) $statusClass = 'badge-progress';
                elseif ($z['id_statusi'] == 3) $statusClass = 'badge-done';
                ?>
                <p><b>Статус:</b> <span class="badge <?= $statusClass ?>"><?= htmlspecialchars($z['nazvanie_status']) ?></span></p>
                <p><b>Способ оплаты:</b> <?= htmlspecialchars($z['sposob_oplati']) ?></p>
                <?php
                $dateDisplay = $z['data_nach'];
                if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $dateDisplay, $m)) {
                    $dateDisplay = "$m[3].$m[2].$m[1]";
                }
                ?>
                <p><b>Дата начала обучения:</b> <?= htmlspecialchars($dateDisplay) ?></p>

                <?php if (!empty($z['otziv_id'])): ?>
                    <div class="otziv-view">
                        <p><b>Отзыв:</b> <?= htmlspecialchars($z['komentariy']) ?></p>
                        <p><b>Оценка:</b> <?= str_repeat('★', (int)$z['kolichestvo_zvezd']) ?><?= str_repeat('☆', 5 - (int)$z['kolichestvo_zvezd']) ?></p>
                    </div>
                <?php endif; ?>

                <?php if ($z['id_statusi'] == 3 && empty($z['otziv_id'])): ?>
                    <form method="post" action="otziv.php" class="otziv-form">
                        <input type="hidden" name="zayavka_id" value="<?= $z['id_zayavki'] ?>">
                        <label>Оставить отзыв <textarea name="komentariy" maxlength="255"></textarea></label>
                        <label>Оставить звёзды
                            <select name="kolichestvo_zvezd">
                                <option value="">—</option>
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <option value="<?= $i ?>"><?= $i ?></option>
                                <?php endfor; ?>
                            </select>
                        </label>
                        <button type="submit">Сохранить</button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php
            $queryParams = $_GET;
            unset($queryParams['page']);
            $baseUrl = 'zayavki.php?' . http_build_query($queryParams);
            $sep = empty($queryParams) ? '' : '&';
            ?>
            <?php if ($page > 1): ?>
                <a href="<?= $baseUrl . $sep ?>page=<?= $page - 1 ?>">« Назад</a>
            <?php else: ?>
                <span class="disabled">« Назад</span>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <?php if ($i == $page): ?>
                    <span class="active"><?= $i ?></span>
                <?php else: ?>
                    <a href="<?= $baseUrl . $sep ?>page=<?= $i ?>"><?= $i ?></a>
                <?php endif; ?>
            <?php endfor; ?>

            <?php if ($page < $totalPages): ?>
                <a href="<?= $baseUrl . $sep ?>page=<?= $page + 1 ?>">Вперёд »</a>
            <?php else: ?>
                <span class="disabled">Вперёд »</span>
            <?php endif; ?>
        </div>
    <?php endif; ?>
<?php endif; ?>

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