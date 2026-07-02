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

        // タイトル（Sho Tsukamoto）のアニメーション完了後にサブタイトルとスクロールを表示
        const subtitle = document.querySelector('.hero-subtitle');
        const scrollIndicator = document.querySelector('.scroll-indicator');
        
        setTimeout(() => {
            if (subtitle) {
                subtitle.classList.remove('opacity-0', 'translate-y-4');
                subtitle.classList.add('opacity-100', 'translate-y-0');
            }
            if (scrollIndicator) {
                scrollIndicator.classList.remove('opacity-0');
                scrollIndicator.classList.add('opacity-100');
            }
        }, 1400);
    }, TIMEOUT);

    // Works (グリッドレイアウト使用中)

}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
} else {
    init();
}


