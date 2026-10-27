<?php
session_start();
require_once __DIR__ . '/../helpers/db.php';

if (empty($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: zayavki.php');
    exit;
}

$zayavka_id = (int)($_POST['zayavka_id'] ?? 0);
$komentariy = trim($_POST['komentariy'] ?? '');
$kolichestvo_zvezd = (int)($_POST['kolichestvo_zvezd'] ?? 0);

$errors = [];
if ($komentariy === '' || $kolichestvo_zvezd < 1 || $kolichestvo_zvezd > 5) {
    $errors[] = 'Заполните все поля отзыва корректно';
}

if (empty($errors)) {
    $stmt = $pdo->prepare("SELECT id_zayavki, statysi_id, otziv_id FROM zayavki WHERE id_zayavki = ? AND polzovateli_id = ?");
    $stmt->execute([$zayavka_id, $_SESSION['user_id']]);
    $z = $stmt->fetch();
    
    if (!$z || $z['statysi_id'] != 3 || !empty($z['otziv_id'])) {
        $errors[] = 'Оставить отзыв можно только после завершения практики, и только один раз';
    }
}

if (empty($errors)) {
    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare("INSERT INTO otzivi (komentariy, kolichestvo_zvezd) VALUES (?, ?)");
        $stmt->execute([$komentariy, $kolichestvo_zvezd]);
        $otziv_id = $pdo->lastInsertId();
        
        $stmt = $pdo->prepare("UPDATE zayavki SET otziv_id = ? WHERE id_zayavki = ?");
        $stmt->execute([$otziv_id, $zayavka_id]);
        $pdo->commit();
    } catch (Exception $e) {
        $pdo->rollBack();
        $errors[] = 'Ошибка сохранения отзыва';
    }
}

if (!empty($errors)) $_SESSION['errors'] = $errors;
header('Location: zayavki.php');
exit;