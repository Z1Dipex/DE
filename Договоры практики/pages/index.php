<?php
session_start();
require_once __DIR__ . '/../helpers/db.php';
require_once __DIR__ . '/layout.php';
?>

<h2 class="page-title">Добро пожаловать!</h2>

<div class="slider">
    <button class="slider-arrow" id="prevSlide">&#8592;</button>
    <img src="../media/image01.webp" alt="Слайд" id="sliderImage">
    <button class="slider-arrow" id="nextSlide">&#8594;</button>
</div>

<div class="dots">
    <span class="dot active" data-index="0"></span>
    <span class="dot" data-index="1"></span>
    <span class="dot" data-index="2"></span>
    <span class="dot" data-index="3"></span>
</div>

<section class="about">
    <h3>О портале</h3>
    <p>Портал «Договоры практики» представляет собой информационную систему для подачи заявок на заключение договоров о практической подготовке с организациями-партнёрами техникума.</p>
    <p>Войдя в систему, вы можете составить заявку, указав организацию, вид практики, желаемый период и должность, а также загрузить ходатайство от организации.</p>
</section>

</main>
<footer>
    <div class="footer-inner">
        <div class="footer-contacts">
            <p><b>Контакты:</b></p>
            <p>Телефон: 8(800)555-35-35</p>
            <p>Email: info@dogovory-praktiki.ru</p>
            <p>Адрес: г. Москва, ул. Студенческая, д. 1</p>
        </div>
        <div class="footer-copy">&copy; <?= date('Y') ?> Договоры практики. Все права защищены.</div>
    </div>
</footer>
<script src="../scripts/script.js"></script>
</body>
</html>