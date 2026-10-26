        document.addEventListener('DOMContentLoaded', () => {
            const slider = document.querySelector('[data-blog-slider]');
            if (!slider) return;

            const slides = [...slider.querySelectorAll('[data-blog-slide]')];
            if (slides.length < 2) return;
            const dots = [...slider.querySelectorAll('[data-blog-slider-dot]')];
            const count = slider.querySelector('[data-blog-slider-count]');
            const toggle = slider.querySelector('[data-blog-slider-toggle]');
            const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
            let activeIndex = 0;
            let paused = false;
            let timer;
            let touchStartX = null;

            const showSlide = (index) => {
                activeIndex = (index + slides.length) % slides.length;
                slides.forEach((slide, slideIndex) => { slide.hidden = slideIndex !== activeIndex; });
                dots.forEach((dot, dotIndex) => { dot.setAttribute('aria-current', String(dotIndex === activeIndex)); });
                count.textContent = `${String(activeIndex + 1).padStart(2, '0')} / ${String(slides.length).padStart(2, '0')}`;
            };

            const stop = () => { window.clearInterval(timer); };
            const start = () => {
                stop();
                toggle.hidden = prefersReducedMotion.matches;
                if (!paused && !prefersReducedMotion.matches && !document.hidden && !slider.matches(':hover, :focus-within') && slides.length > 1) {
                    timer = window.setInterval(() => showSlide(activeIndex + 1), 7000);
                }
            };

            toggle.addEventListener('click', () => {
                paused = !paused;
                toggle.setAttribute('aria-pressed', String(paused));
                toggle.setAttribute('aria-label', paused ? 'شروع پخش خودکار اسلایدها' : 'توقف پخش خودکار اسلایدها');
                start();
            });
            slider.querySelector('[data-blog-slider-prev]').addEventListener('click', () => { showSlide(activeIndex - 1); start(); });
            slider.querySelector('[data-blog-slider-next]').addEventListener('click', () => { showSlide(activeIndex + 1); start(); });
            dots.forEach((dot, index) => dot.addEventListener('click', () => { showSlide(index); start(); }));
            slider.addEventListener('mouseenter', stop);
            slider.addEventListener('mouseleave', start);
            slider.addEventListener('focusin', stop);
            slider.addEventListener('focusout', (event) => { if (!slider.contains(event.relatedTarget)) start(); });
            slider.addEventListener('keydown', (event) => {
                if (event.key === 'ArrowLeft' || event.key === 'ArrowRight') {
                    event.preventDefault();
                    showSlide(activeIndex + (event.key === 'ArrowLeft' ? 1 : -1));
                    stop();
                }
            });
            slider.addEventListener('touchstart', (event) => { touchStartX = event.changedTouches[0].screenX; }, { passive: true });
            slider.addEventListener('touchend', (event) => {
                if (touchStartX === null) return;
                const distance = event.changedTouches[0].screenX - touchStartX;
                if (Math.abs(distance) > 50) showSlide(activeIndex + (distance > 0 ? 1 : -1));
                touchStartX = null;
            }, { passive: true });
            document.addEventListener('visibilitychange', start);
            prefersReducedMotion.addEventListener('change', start);
            start();
        });
