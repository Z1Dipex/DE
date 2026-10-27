<?php
session_start();
require_once __DIR__ . '/../helpers/db.php';

if (empty($_SESSION['user_id']) || ($_SESSION['roli_id'] ?? 0) != 2) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $zayavka_id = (int)($_POST['zayavka_id'] ?? 0);
    $statysi_id = (int)($_POST['statysi_id'] ?? 0);
    if ($zayavka_id > 0 && $statysi_id >= 1 && $statysi_id <= 4) {
        $stmt = $pdo->prepare("UPDATE zayavki SET statysi_id = ? WHERE id_zayavki = ?");
        $stmt->execute([$statysi_id, $zayavka_id]);
        $_SESSION['toast'] = ['text' => 'Статус заявки №' . $zayavka_id . ' успешно изменен', 'type' => 'success'];
    }
    header('Location: admin.php');
    exit;
}

$statusFilter = (int)($_GET['status'] ?? 0);
$orgFilter = trim($_GET['org'] ?? '');
$tipFilter = (int)($_GET['tip'] ?? 0);

$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 3;

$statusi = $pdo->query("SELECT * FROM statusi")->fetchAll(PDO::FETCH_ASSOC);
$orgs = $pdo->query("SELECT inn, nazvanie_organizacii FROM organizacii ORDER BY nazvanie_organizacii")->fetchAll(PDO::FETCH_ASSOC);
$tips = $pdo->query("SELECT id_tip, nazvanie_tipa FROM tipi_praktiki")->fetchAll(PDO::FETCH_ASSOC);

$where = [];
$params = [];
if ($statusFilter > 0) { $where[] = "z.statysi_id = ?"; $params[] = $statusFilter; }
if ($orgFilter !== '') { $where[] = "z.inn = ?"; $params[] = $orgFilter; }
if ($tipFilter > 0) { $where[] = "z.tip_id = ?"; $params[] = $tipFilter; }

$whereSql = $where ? " WHERE " . implode(" AND ", $where) : "";

$countStmt = $pdo->prepare("SELECT COUNT(*) FROM zayavki z" . $whereSql);
$countStmt->execute($params);
$total = (int)$countStmt->fetchColumn();
$totalPages = max(1, (int)ceil($total / $perPage));
if ($page > $totalPages) $page = $totalPages;
$offset = ($page - 1) * $perPage;

// ОБРАТИ ВНИМАНИЕ: используем period_nachalo и period_konec
$sql = "
    SELECT z.id_zayavki, z.period_nachalo, z.period_konec, z.dolzhnost, z.hodataistvo,
           o.nazvanie_organizacii, t.nazvanie_tipa, s.status_name, s.id_statusi,
           p.fio, p.telefon, p.pochta,
           z.otziv_id, ot.komentariy, ot.kolichestvo_zvezd
    FROM zayavki z
    LEFT JOIN organizacii o ON z.inn = o.inn
    LEFT JOIN tipi_praktiki t ON z.tip_id = t.id_tip
    LEFT JOIN statusi s ON z.statysi_id = s.id_statusi
    LEFT JOIN polzovateli p ON z.polzovateli_id = p.id_polzovateli
    LEFT JOIN otzivi ot ON z.otziv_id = ot.id_otziv
    {$whereSql}
    ORDER BY z.id_zayavki DESC
    LIMIT {$perPage} OFFSET {$offset}
";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$zayavki = $stmt->fetchAll(PDO::FETCH_ASSOC);

$toast = $_SESSION['toast'] ?? null;
unset($_SESSION['toast']);

require_once __DIR__ . '/layout.php';
?>

<h2 class="page-title">Панель администратора</h2>

<?php if ($toast): ?>
    <div class="toast show <?= $toast['type'] ?>"><?= htmlspecialchars($toast['text']) ?></div>
<?php endif; ?>

<form method="get" action="admin.php" class="filter-form">
    <div class="filter-row">
        <label for="status">Статус:</label>
        <select id="status" name="status" onchange="this.form.submit()">
            <option value="0">Все статусы</option>
            <?php foreach ($statusi as $s): ?>
                <option value="<?= $s['id_statusi'] ?>" <?= ($statusFilter == $s['id_statusi']) ? 'selected' : '' ?>><?= htmlspecialchars($s['status_name']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="filter-row">
        <label for="org">Организация:</label>
        <select id="org" name="org" onchange="this.form.submit()">
            <option value="">Все организации</option>
            <?php foreach ($orgs as $o): ?>
                <option value="<?= $o['inn'] ?>" <?= ($orgFilter == $o['inn']) ? 'selected' : '' ?>><?= htmlspecialchars($o['nazvanie_organizacii']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="filter-row">
        <label for="tip">Вид практики:</label>
        <select id="tip" name="tip" onchange="this.form.submit()">
            <option value="0">Все виды</option>
            <?php foreach ($tips as $t): ?>
                <option value="<?= $t['id_tip'] ?>" <?= ($tipFilter == $t['id_tip']) ? 'selected' : '' ?>><?= htmlspecialchars($t['nazvanie_tipa']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <?php if ($statusFilter || $orgFilter || $tipFilter): ?>
        <a href="admin.php" class="reset-filter">Сбросить</a>
    <?php endif; ?>
</form>

<?php if (empty($zayavki)): ?>
    <p>Заявок не найдено.</p>
<?php else: ?>
    <div class="zayavki-list">
        <?php foreach ($zayavki as $z): ?>
            <div class="zayavka-card">
                <h3>Заявка №<?= $z['id_zayavki'] ?></h3>
                <p><b>Студент:</b> <?= htmlspecialchars($z['fio']) ?></p>
                <p><b>Телефон:</b> <?= htmlspecialchars($z['telefon']) ?></p>
                <p><b>Организация:</b> <?= htmlspecialchars($z['nazvanie_organizacii']) ?></p>
                <p><b>Вид практики:</b> <?= htmlspecialchars($z['nazvanie_tipa']) ?></p>
                <p><b>Период:</b> 
                    <?= date('d.m.Y', strtotime($z['period_nachalo'])) ?> — 
                    <?= $z['period_konec'] ? date('d.m.Y', strtotime($z['period_konec'])) : '—' ?>
                </p>
                <p><b>Должность:</b> <?= htmlspecialchars($z['dolzhnost']) ?></p>
                <p><b>Ходатайство:</b> <a href="../<?= htmlspecialchars(ltrim($z['hodataistvo'], '/')) ?>" target="_blank">Открыть файл</a></p>
                
                <?php
                $statusClass = 'badge-unknown';
                if ($z['id_statusi'] == 1) $statusClass = 'badge-new';
                elseif ($z['id_statusi'] == 2) $statusClass = 'badge-progress';
                elseif ($z['id_statusi'] == 3) $statusClass = 'badge-done';
                ?>
                <p><b>Статус:</b> <span class="badge <?= $statusClass ?>"><?= htmlspecialchars($z['status_name']) ?></span></p>

                <?php if (!empty($z['otziv_id'])): ?>
                    <div class="otziv-view">
                        <p><b>Отзыв:</b> <?= htmlspecialchars($z['komentariy']) ?></p>
                        <p><b>Оценка:</b> <?= str_repeat('★', (int)$z['kolichestvo_zvezd']) ?><?= str_repeat('☆', 5 - (int)$z['kolichestvo_zvezd']) ?></p>
                    </div>
                <?php endif; ?>

                <form method="post" action="admin.php" class="status-form">
                    <input type="hidden" name="zayavka_id" value="<?= $z['id_zayavki'] ?>">
                    <label>Изменить статус
                        <select name="statysi_id" required>
                            <?php foreach ($statusi as $s): ?>
                                <option value="<?= $s['id_statusi'] ?>" <?= ($z['id_statusi'] == $s['id_statusi']) ? 'selected' : '' ?>><?= htmlspecialchars($s['status_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                    <button type="submit">Сохранить</button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php
            $queryParams = $_GET; unset($queryParams['page']);
            $baseUrl = 'admin.php?' . http_build_query($queryParams);
            $sep = empty($queryParams) ? '' : '&';
            ?>
            <?php if ($page > 1): ?><a href="<?= $baseUrl . $sep ?>page=<?= $page - 1 ?>">« Назад</a>
            <?php else: ?><span class="disabled">« Назад</span><?php endif; ?>
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <?php if ($i == $page): ?><span class="active"><?= $i ?></span>
                <?php else: ?><a href="<?= $baseUrl . $sep ?>page=<?= $i ?>"><?= $i ?></a><?php endif; ?>
            <?php endfor; ?>
            <?php if ($page < $totalPages): ?><a href="<?= $baseUrl . $sep ?>page=<?= $page + 1 ?>">Вперёд »</a>
            <?php else: ?><span class="disabled">Вперёд »</span><?php endif; ?>
        </div>
    <?php endif; ?>
<?php endif; ?>

</main>
<footer>
    <div class="footer-inner">
        <div class="footer-contacts">
            <p><b>Контакты:</b></p><p>Телефон: 8(800)555-35-35</p><p>Email: info@dogovory-praktiki.ru</p>
        </div>
        <div class="footer-copy">&copy; <?= date('Y') ?> Договоры практики</div>
    </div>
</footer>
<script src="../scripts/script.js"></script>
</body>
</html>