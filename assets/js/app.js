import Alpine from 'alpinejs';
import 'animate.css';
import Swiper from 'swiper';
import { Navigation, Pagination } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';

import.meta.glob([
    '../images/**',
    '../fonts/**',
]);

window.Alpine = Alpine;

Alpine.start();


function init() {
    // Intersection Observer for scroll animations (replacing WOW.js)
    const wowElements = document.querySelectorAll('.wow');
    const observerOptions = {
        root: null,
        rootMargin: '0px 0px -10% 0px', // 少し手前で発火させる
        threshold: 0.05
    };

    const wowObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const target = entry.target;
                
                target.style.visibility = 'visible';
                
                const delay = target.getAttribute('data-wow-delay');
                if (delay) {
                    target.style.animationDelay = delay;
                }
                
                const duration = target.getAttribute('data-wow-duration');
                if (duration) {
                    target.style.animationDuration = duration;
                }

                const iteration = target.getAttribute('data-wow-iteration');
                if (iteration) {
                    target.style.animationIterationCount = iteration;
                }

                // animate.cssのアニメーションクラスを有効にするためにクラスを追加
                target.classList.add('animate__animated');
                
                observer.unobserve(target);
            }
        });
    }, observerOptions);

    wowElements.forEach(element => {
        if (element.style.visibility !== 'visible') {
            element.style.visibility = 'hidden';
        }
        wowObserver.observe(element);
    });

    const CLASSNAME = "-visible";
    const TIMEOUT = 1500;
    const targets = document.querySelectorAll('.title');

    setTimeout(() => {
        for (let index = 0; index < targets.length; index += 1) {
            const element = targets[index];
            element.classList.add(CLASSNAME);
        }
    }, TIMEOUT);

    // Works
    const swiper = new Swiper(".swiper", {
        modules: [Navigation, Pagination],
        slidesPerView: 3,
        pagination: {
            el: ".swiper-pagination"
        },
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev"
        }
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
} else {
    init();
}


