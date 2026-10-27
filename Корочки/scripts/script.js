// ---------- Слайдер на главной ----------
document.addEventListener('DOMContentLoaded', function () {
    const sliderImage = document.getElementById('sliderImage');
    if (!sliderImage) return; // на других страницах слайдера нет

    const images = [
        '/DE/Korochki/media/image01.webp',
        '/DE/Korochki/media/image03.png',
        '/DE/Korochki/media/image04.jpg',
        '/DE/Korochki/media/image05.jpg'
    ];

    let currentIndex = 0;
    const dots = document.querySelectorAll('.dot');
    const prevBtn = document.getElementById('prevSlide');
    const nextBtn = document.getElementById('nextSlide');

    function showSlide(index) {
        if (index < 0) index = images.length - 1;
        if (index >= images.length) index = 0;

        currentIndex = index;
        sliderImage.src = images[currentIndex];

        dots.forEach((dot, i) => {
            dot.classList.toggle('active', i === currentIndex);
        });
    }

    // Автопрокрутка каждые 5 секунд
    let autoTimer = setInterval(() => {
        showSlide(currentIndex + 1);
    }, 5000);

    function resetTimer() {
        clearInterval(autoTimer);
        autoTimer = setInterval(() => {
            showSlide(currentIndex + 1);
        }, 5000);
    }

    if (prevBtn) {
        prevBtn.addEventListener('click', () => {
            showSlide(currentIndex - 1);
            resetTimer();
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            showSlide(currentIndex + 1);
            resetTimer();
        });
    }

    dots.forEach((dot, i) => {
        dot.addEventListener('click', () => {
            showSlide(i);
            resetTimer();
        });
    });

    showSlide(0);
});

// ---------- Маска телефона ----------
document.addEventListener('DOMContentLoaded', function () {
    const phoneInput = document.getElementById('telefon');
    if (!phoneInput) return;

    phoneInput.addEventListener('input', function () {
        let digits = this.value.replace(/\D/g, '');

        // Приводим к формату 8XXXXXXXXXX
        if (digits.startsWith('7')) {
            digits = '8' + digits.slice(1);
        }
        if (!digits.startsWith('8')) {
            digits = '8' + digits;
        }
        digits = digits.slice(0, 11);

        let formatted = '8';
        if (digits.length > 1) {
            formatted += '(' + digits.slice(1, 4);
        }
        if (digits.length >= 5) {
            formatted += ')' + digits.slice(4, 7);
        }
        if (digits.length >= 8) {
            formatted += '-' + digits.slice(7, 9);
        }
        if (digits.length >= 10) {
            formatted += '-' + digits.slice(9, 11);
        }

        this.value = formatted;
    });
});

// ---------- Поиск по заявкам (фильтр на клиенте) ----------
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.querySelector('.search-box input');
    const cards = document.querySelectorAll('.zayavka-card');
    if (!searchInput || !cards.length) return;

    searchInput.addEventListener('input', function () {
        const query = this.value.toLowerCase().trim();

        cards.forEach(card => {
            const text = card.textContent.toLowerCase();
            card.style.display = text.includes(query) ? '' : 'none';
        });
    });
});



// ---------- Бургер-меню ----------
document.addEventListener('DOMContentLoaded', function () {
    const burger = document.getElementById('burgerBtn');
    const menu = document.getElementById('mobileMenu');
    
    if (burger && menu) {
        burger.addEventListener('click', function () {
            menu.classList.toggle('active');
            burger.classList.toggle('active');
        });
    }
});