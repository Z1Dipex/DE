<?php
session_start();
require_once __DIR__ . '/../helpers/db.php';

if (empty($_SESSION['user_id'])) {
    header('Location: auth.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: zayavki.php');
    exit;
}

$zayavka_id        = (int)($_POST['zayavka_id'] ?? 0);
$komentariy        = trim($_POST['komentariy'] ?? '');
$kolichestvo_zvezd = (int)($_POST['kolichestvo_zvezd'] ?? 0);

$errors = [];

if ($zayavka_id <= 0) {
    $errors[] = 'Неверная заявка';
}
if ($komentariy === '' || mb_strlen($komentariy) > 255) {
    $errors[] = 'Комментарий: 1–255 символов';
}
if ($kolichestvo_zvezd < 1 || $kolichestvo_zvezd > 5) {
    $errors[] = 'Оценка: от 1 до 5 звёзд';
}

// Проверяем, что заявка принадлежит пользователю, завершена и без отзыва
if (empty($errors)) {
    $stmt = $pdo->prepare("
        SELECT z.id_zayavki, z.statysi_id, z.otziv_id
        FROM zayavki z
        WHERE z.id_zayavki = ? AND z.polzovateli_id = ?
    ");
    $stmt->execute([$zayavka_id, $_SESSION['user_id']]);
    $z = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$z) {
        $errors[] = 'Заявка не найдена';
    } elseif ((int)$z['statysi_id'] !== 3) {
        $errors[] = 'Отзыв можно оставить только после завершения обучения';
    } elseif (!empty($z['otziv_id'])) {
        $errors[] = 'Отзыв уже оставлен';
    }
}

if (empty($errors)) {
    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare("
            INSERT INTO otzivi (komentariy, kolichestvo_zvezd) VALUES (?, ?)
        ");
        $stmt->execute([$komentariy, $kolichestvo_zvezd]);
        $otziv_id = $pdo->lastInsertId();

        $stmt = $pdo->prepare("
            UPDATE zayavki SET otziv_id = ? WHERE id_zayavki = ?
        ");
        $stmt->execute([$otziv_id, $zayavka_id]);

        $pdo->commit();
    } catch (Exception $e) {
        $pdo->rollBack();
        $errors[] = 'Ошибка при сохранении отзыва';
    }
}

if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
}

header('Location: zayavki.php');
exit;