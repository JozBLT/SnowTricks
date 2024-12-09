import './styles/app.css';
import Swiper from 'swiper';
import { Navigation } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/navigation';

document.addEventListener('DOMContentLoaded', () => {
    Swiper.use([Navigation]);
    const loadMoreButton = document.getElementById('load-more');
    const tricksContainer = document.getElementById('tricks-container');
    const scrollToTopButton = document.getElementById('scroll-to-top');
    const tricksListAnchor = document.querySelector('#tricks-top-anchor');

    let offset = 15;

    if (loadMoreButton) {
        loadMoreButton.addEventListener('click', () => {
            fetch(`/load-more-tricks?offset=${offset}`)
                .then(response => response.json())
                .then(data => {
                    tricksContainer.innerHTML += data.html;
                    offset += 10;

                    if (!data.hasMore) {
                        loadMoreButton.style.display = 'none';
                    }

                    if (scrollToTopButton && scrollToTopButton.classList.contains('hidden')) {
                        scrollToTopButton.classList.remove('hidden');
                    }
                })
                .catch(error => console.error('Erreur lors du chargement des tricks:', error));
        });
    }

    if (scrollToTopButton) {
        scrollToTopButton.addEventListener('click', (e) => {
            e.preventDefault();
            tricksListAnchor.scrollIntoView({ behavior: 'smooth' });
        });
    }

    window.addEventListener('scroll', () => {
        if (scrollToTopButton && tricksListAnchor) {
            const anchorPosition = tricksListAnchor.getBoundingClientRect().top;

            if (anchorPosition >= 0) {
                scrollToTopButton.classList.add('hidden');
            } else {
                scrollToTopButton.classList.remove('hidden');
            }
        }
    });

    const initializeSwiper = (containerId) => {
        const container = document.getElementById(containerId);
        if (!container) return;

        const slidesCount = container.querySelectorAll('.swiper-slide').length;

        new Swiper(`#${containerId}`, {
            slidesPerView: slidesCount < 3 ? slidesCount : 3,
            spaceBetween: 10,
            loop: slidesCount >= 3,
            navigation: {
                nextEl: `#${containerId} .swiper-button-next`,
                prevEl: `#${containerId} .swiper-button-prev`,
            },
            on: {
                init: function () {
                    console.log(`Swiper initialisé pour ${containerId}`);
                    const nextButton = container.querySelector('.swiper-button-next');
                    const prevButton = container.querySelector('.swiper-button-prev');
                    if (slidesCount < 3) {
                        nextButton.style.display = 'none';
                        prevButton.style.display = 'none';
                    }
                },
            },
            breakpoints: {
                640: { slidesPerView: 1 },
                768: { slidesPerView: Math.min(2, slidesCount) },
                1024: { slidesPerView: Math.min(3, slidesCount) },
            },
        });
    };

    // Initialisation des carrousels
    initializeSwiper('carouselImages');
    initializeSwiper('carouselVideos');
});
