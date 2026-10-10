import { ChevronLeft, ChevronRight } from 'lucide';
import { onDomReady } from './dom-ready';
import { renderIcons } from './icon-renderer';

const iconSet = { ChevronLeft, ChevronRight };

const initializeCourseCarousel = () => {
    const carousel = document.querySelector('[data-course-carousel]');

    if (!carousel) return;

    const slides = [...carousel.querySelectorAll('[data-carousel-slide]')];
    const dots = [...carousel.querySelectorAll('[data-carousel-dot]')];
    const previousButton = carousel.querySelector('[data-carousel-previous]');
    const nextButton = carousel.querySelector('[data-carousel-next]');
    let activeIndex = slides.findIndex((slide) => !slide.hidden);
    let autoplayTimer;
    let touchStartX = null;
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

    if (slides.length === 0) return;

    renderIcons(iconSet);

    if (activeIndex < 0) {
        activeIndex = 0;
    }

    const showSlide = (index) => {
        activeIndex = (index + slides.length) % slides.length;

        slides.forEach((slide, slideIndex) => {
            const isActive = slideIndex === activeIndex;

            slide.hidden = !isActive;
            slide.classList.toggle('hidden', !isActive);
            slide.setAttribute('aria-hidden', String(!isActive));
        });

        dots.forEach((dot, dotIndex) => {
            const isActive = dotIndex === activeIndex;

            dot.setAttribute('aria-current', isActive ? 'true' : 'false');
            dot.dataset.active = String(isActive);
        });
    };

    const stopAutoplay = () => {
        window.clearInterval(autoplayTimer);
        autoplayTimer = undefined;
    };

    const startAutoplay = () => {
        stopAutoplay();

        if (prefersReducedMotion.matches || document.hidden || slides.length < 2) return;

        autoplayTimer = window.setInterval(() => showSlide(activeIndex + 1), 6500);
    };

    previousButton?.addEventListener('click', () => {
        showSlide(activeIndex - 1);
        startAutoplay();
    });

    nextButton?.addEventListener('click', () => {
        showSlide(activeIndex + 1);
        startAutoplay();
    });

    carousel.addEventListener('click', (event) => {
        const dot = event.target.closest('[data-carousel-dot]');

        if (!dot || !carousel.contains(dot)) return;

        const dotIndex = dots.indexOf(dot);

        if (dotIndex < 0) return;

        showSlide(dotIndex);
        startAutoplay();
    });

    carousel.addEventListener('keydown', (event) => {
        if (event.key === 'ArrowLeft') {
            event.preventDefault();
            showSlide(activeIndex - 1);
            startAutoplay();
        }

        if (event.key === 'ArrowRight') {
            event.preventDefault();
            showSlide(activeIndex + 1);
            startAutoplay();
        }
    });

    carousel.addEventListener('pointerdown', (event) => {
        if (event.pointerType !== 'touch') return;

        touchStartX = event.clientX;
    });

    carousel.addEventListener('pointerup', (event) => {
        if (event.pointerType !== 'touch' || touchStartX === null) return;

        const distance = event.clientX - touchStartX;
        touchStartX = null;

        if (Math.abs(distance) < 45) return;

        showSlide(activeIndex + (distance < 0 ? 1 : -1));
        startAutoplay();
    });

    carousel.addEventListener('pointercancel', () => {
        touchStartX = null;
    });

    document.addEventListener('visibilitychange', () => {
        if (document.hidden) stopAutoplay();
        else startAutoplay();
    });

    prefersReducedMotion.addEventListener('change', startAutoplay);

    showSlide(activeIndex);
    startAutoplay();
};

onDomReady(initializeCourseCarousel);
