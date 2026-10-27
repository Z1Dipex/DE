document.addEventListener('DOMContentLoaded', function () {
    // --- Слайдер ---
    const sliderImage = document.getElementById('sliderImage');
    if (sliderImage) {
        const images = ['../media/image01.webp', '../media/image03.png', '../media/image04.jpg', '../media/image05.jpg'];
        let currentIndex = 0;
        const dots = document.querySelectorAll('.dot');
        
        function showSlide(index) {
            if (index < 0) index = images.length - 1;
            if (index >= images.length) index = 0;
            currentIndex = index;
            sliderImage.src = images[currentIndex];
            dots.forEach((dot, i) => dot.classList.toggle('active', i === currentIndex));
        }

        let autoTimer = setInterval(() => showSlide(currentIndex + 1), 3000);
        
        function resetTimer() {
            clearInterval(autoTimer);
            autoTimer = setInterval(() => showSlide(currentIndex + 1), 3000);
        }

        document.getElementById('prevSlide')?.addEventListener('click', () => { showSlide(currentIndex - 1); resetTimer(); });
        document.getElementById('nextSlide')?.addEventListener('click', () => { showSlide(currentIndex + 1); resetTimer(); });
        dots.forEach((dot, i) => dot.addEventListener('click', () => { showSlide(i); resetTimer(); }));
        
        showSlide(0);
    }

    // --- Бургер-меню ---
    const burger = document.getElementById('burgerBtn');
    const menu = document.getElementById('mobileMenu');
    if (burger && menu) {
        burger.addEventListener('click', function () {
            menu.classList.toggle('active');
            burger.classList.toggle('active');
        });
    }

    // --- Маска телефона ---
    const phoneInput = document.getElementById('telefon');
    if (phoneInput) {
        phoneInput.addEventListener('input', function (e) {
            let digits = this.value.replace(/\D/g, '');
            if (digits.length === 0) { this.value = ''; return; }
            if (digits[0] === '7') digits = '8' + digits.slice(1);
            else if (digits[0] !== '8') digits = '8' + digits;
            digits = digits.slice(0, 11);
            let formatted = '8';
            if (digits.length > 1) formatted += '(' + digits.substring(1, 4);
            if (digits.length >= 5) formatted += ')' + digits.substring(4, 7);
            if (digits.length >= 8) formatted += '-' + digits.substring(7, 9);
            if (digits.length >= 10) formatted += '-' + digits.substring(9, 11);
            if (e.inputType.includes('delete') && formatted === '8(') { this.value = '8'; return; }
            this.value = formatted;
        });
        phoneInput.addEventListener('keydown', function (e) {
            const allowed = ['Backspace', 'Delete', 'Tab', 'Enter', 'Escape', 'ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown', 'Home', 'End'];
            if (allowed.includes(e.key) || e.ctrlKey || e.metaKey) return;
            if (!/^\d$/.test(e.key)) e.preventDefault();
        });
        phoneInput.addEventListener('focus', function() { if (this.value === '') this.value = '8'; });
    }

    // --- УМНЫЙ ПОИСК ПО ИНН (АВТОКОМПЛИТ) ---
    const orgSearch = document.getElementById('org_search');
    const innHidden = document.getElementById('inn');
    const autocompleteList = document.getElementById('autocomplete-list');
    
    // Проверяем, есть ли элементы на странице и переданы ли данные из PHP
    if (orgSearch && innHidden && autocompleteList && window.organizationsData) {
        const orgs = window.organizationsData;

        // Функция отрисовки списка
        function renderList(query = '') {
            autocompleteList.innerHTML = ''; // Очищаем
            const q = query.toLowerCase().trim();
            
            // Если запрос пустой, показываем всё. Иначе фильтруем по ИНН или названию
            const filtered = orgs.filter(org => 
                q === '' || org.inn.includes(q) || org.name.toLowerCase().includes(q)
            );

            if (filtered.length === 0) {
                autocompleteList.style.display = 'none';
                return;
            }

            filtered.forEach(org => {
                const div = document.createElement('div');
                div.innerHTML = `<strong>${org.name}</strong><br><small>ИНН: ${org.inn}</small>`;
                
                // Действие при клике на элемент списка
                div.addEventListener('click', function() {
                    orgSearch.value = org.name + ' (ИНН: ' + org.inn + ')';
                    innHidden.value = org.inn; // Записываем ИНН в скрытое поле
                    autocompleteList.style.display = 'none'; // Скрываем список
                });
                
                autocompleteList.appendChild(div);
            });
            
            autocompleteList.style.display = 'block';
        }

        // 1. При клике на поле (фокус) — показываем весь список
        orgSearch.addEventListener('focus', function() {
            // Если поле уже заполнено (например, при ошибке формы), очищаем его для удобства поиска
            if (innHidden.value !== '') {
                this.value = '';
                innHidden.value = '';
            }
            renderList('');
        });

        // 2. При вводе текста — фильтруем
        orgSearch.addEventListener('input', function() {
            innHidden.value = ''; // Сбрасываем скрытый ИНН, так как текст изменился
            renderList(this.value);
        });

        // 3. Клик вне списка — скрываем его
        document.addEventListener('click', function(e) {
            if (e.target !== orgSearch && !autocompleteList.contains(e.target)) {
                autocompleteList.style.display = 'none';
            }
        });

        // 4. Валидация перед отправкой: не даем отправить, если не выбран ИНН
        orgSearch.closest('form').addEventListener('submit', function(e) {
            if (innHidden.value === '') {
                e.preventDefault(); // Отменяем отправку
                alert('Пожалуйста, выберите организацию из выпадающего списка.');
                orgSearch.focus();
            }
        });
    }

    // --- Поиск по карточкам (клиентский) ---
    const searchInput = document.querySelector('.search-box input');
    const cards = document.querySelectorAll('.zayavka-card');
    if (searchInput && cards.length) {
        searchInput.addEventListener('input', function() {
            const q = this.value.toLowerCase();
            cards.forEach(card => {
                card.style.display = card.textContent.toLowerCase().includes(q) ? '' : 'none';
            });
        });
    }
});


    // --- Тосты (всплывающие сообщения) ---
    const toasts = document.querySelectorAll('.toast');
    if (toasts.length > 0) {
        toasts.forEach(toast => {
            // Через 3 секунды плавно скрываем
            setTimeout(() => {
                toast.classList.remove('show');
                // Удаляем из DOM через 0.4 сек (после завершения анимации)
                setTimeout(() => toast.remove(), 400);
            }, 3000);
        });
    }