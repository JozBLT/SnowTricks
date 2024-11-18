import './styles/app.css';

document.addEventListener('DOMContentLoaded', () => {
    const loadMoreButton = document.getElementById('load-more');
    const tricksContainer = document.getElementById('tricks-container');
    const scrollToTopButton = document.getElementById('scroll-to-top');

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
            document.querySelector('#tricks-list').scrollIntoView({ behavior: 'smooth' });
        });
    }
});
