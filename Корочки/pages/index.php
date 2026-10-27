<?php
session_start();
require_once __DIR__ . '/../helpers/db.php';

$stmt = $pdo->query("SELECT id_kursi, nazvanie_kursa FROM kursi");
$kursi = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once __DIR__ . '/layout.php';
?>

<h2 class="page-title">Слайдер</h2>

<div class="slider">
    <button class="slider-arrow" id="prevSlide">&#8592;</button>
    <img src="/DE/Korochki/media/image01.webp" alt="Слайд" id="sliderImage">
    <button class="slider-arrow" id="nextSlide">&#8594;</button>
</div>

<div class="dots">
    <span class="dot active" data-index="0"></span>
    <span class="dot" data-index="1"></span>
    <span class="dot" data-index="2"></span>
    <span class="dot" data-index="3"></span>
</div>

<section class="about">
    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
    <p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt.</p>
</section>

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