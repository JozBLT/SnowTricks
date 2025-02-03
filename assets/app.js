import './styles/app.css';
import Swiper from 'swiper';
import { Navigation, Pagination } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';

const MAX_FILE_SIZE_MB = 2;
const MAX_FILE_SIZE_BYTES = MAX_FILE_SIZE_MB * 1024 * 1024;

document.addEventListener('DOMContentLoaded', () => {
    Swiper.use([Navigation, Pagination]);
    const form = document.querySelector('form[name="tricks"]');

    const loadMoreButton = document.getElementById('load-more');
    const tricksContainer = document.getElementById('tricks-container');
    const scrollToTopButton = document.getElementById('scroll-to-top');

    let offsetTricks = 15;

    if (loadMoreButton) {
        loadMoreButton.addEventListener('click', () => {
            fetch(`/load-more-tricks?offset=${offsetTricks}`)
                .then(response => response.json())
                .then(data => {
                    tricksContainer.innerHTML += data.html;
                    offsetTricks += 10;

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

    const loadMoreCommentsButton = document.getElementById('load-more-comments');
    const commentsContainer = document.getElementById('comments-container');

    if (loadMoreCommentsButton) {
        const tricksId = loadMoreCommentsButton.dataset.tricksId;

        loadMoreCommentsButton.addEventListener('click', () => {
            fetch(`/load-more-comments?tricksId=${tricksId}`)
                .then(response => response.json())
                .then(data => {
                    commentsContainer.querySelector('ul').insertAdjacentHTML('beforeend', data.html);
                    loadMoreCommentsButton.style.display = 'none';
                })
                .catch(error => console.error('Erreur lors du chargement des commentaires:', error));
        });
    }

    const tricksListAnchor = document.querySelector('#tricks-top-anchor');

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

    const isClassicView = document.getElementById('tricks-classic-view') !== null;

    if (isClassicView) {
        const modal = document.getElementById('imageModal');
        const modalImage = document.getElementById('modalImage');
        const closeModal = document.getElementById('closeModal');

        const openModal = (src) => {
            modalImage.src = src;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        };

        const closeModalHandler = () => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        };

        document.querySelectorAll('[data-modal-image]').forEach((img) => {
            img.addEventListener('click', () => openModal(img.src));
        });

        closeModal.addEventListener('click', closeModalHandler);

        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                closeModalHandler();
            }
        });
    }

    const initializeSwiper = (containerId) => {
        const container = document.getElementById(containerId);
        if (!container) return;

        new Swiper(`#${containerId}`, {
            slidesPerView: 1,
            spaceBetween: 10,
            keyboard: {
                enabled: true,
            },
            rewind: true,
            loop: false,
            loopAdditionalSlides: true,
            centerInsufficientSlides: true,
            navigation: true,
            grabCursor: true,
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            breakpoints: {
                640: { slidesPerView: 1 },
                768: { slidesPerView: 2 },
                1024: { slidesPerView: 3 },
            },
        });
    };
    initializeSwiper('carouselImages');
    initializeSwiper('carouselVideos');

    // Main image management (thumbnail)
    const thumbnailPreview = document.getElementById('thumbnail-preview');
    const thumbnailReplaceInput = document.getElementById('thumbnail-replace');
    const thumbnailDeleteBtn = document.getElementById('thumbnail-delete-btn');
    const thumbnailDeleteInput = document.getElementById('thumbnail-delete');
    const defaultThumbnailSrc = "/images/creative_snowboard_3.jpg";
    const updateDeleteButtonVisibility = () => {
        if (thumbnailPreview.src.includes(defaultThumbnailSrc)) {
            thumbnailDeleteBtn.classList.add('hidden');
            thumbnailDeleteInput.checked = false;
        } else {
            thumbnailDeleteBtn.classList.remove('hidden');
        }
    };

    if (thumbnailReplaceInput && thumbnailPreview) {
        thumbnailReplaceInput.addEventListener('change', (e) => {
            const file = e.target.files[0];

            if (file && file.size > MAX_FILE_SIZE_BYTES) {
                alert(`L'image est trop volumineuse. La taille maximale autorisée est de ${MAX_FILE_SIZE_MB} Mo.`);
                e.target.value = "";
                return;
            }

            if (file) {
                const reader = new FileReader();
                reader.onload = (event) => {
                    thumbnailPreview.src = event.target.result;
                    updateDeleteButtonVisibility();
                };
                reader.readAsDataURL(file);
            }
        });
    }

    if (thumbnailDeleteBtn && thumbnailDeleteInput && thumbnailPreview) {
        thumbnailDeleteBtn.addEventListener('click', (e) => {
            e.preventDefault();
            thumbnailPreview.src = defaultThumbnailSrc;
            thumbnailReplaceInput.value = '';
            updateDeleteButtonVisibility();
            thumbnailDeleteInput.checked = true;
        });
        updateDeleteButtonVisibility();
    }

    // Images slider management
    const addImageInput = document.querySelector('#add-image-input');
    const carouselImages = document.querySelector('#carouselImages .swiper-wrapper');

    if (addImageInput) {
        addImageInput.addEventListener('change', (e) => {
            const file = e.target.files[0];

            if (file && file.size > MAX_FILE_SIZE_BYTES) {
                alert(`L'image est trop volumineuse. La taille maximale autorisée est de ${MAX_FILE_SIZE_MB} Mo.`);
                e.target.value = '';
                return;
            }

            if (file) {
                const reader = new FileReader();
                reader.onload = (event) => {
                    const newSlide = document.createElement('div');
                    newSlide.className = 'swiper-slide flex flex-col items-center temp-slide';
                    newSlide.innerHTML = `
                        <div class="relative">
                            <img src="${event.target.result}" alt="Image ajoutée" class="rounded-lg object-cover temp-image mb-2">
                        </div>
                        <div class="flex justify-center space-x-2">
                            <label class="text-red-500 hover:text-red-600 cursor-pointer">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M9 3H15V4H20V6H4V4H9V3ZM5 8V19C5 20.1 5.9 21 7 21H17C18.1 21 19 20.1 19 19V8H5ZM8 10H10V18H8V10ZM14 10H16V18H14V10Z"></path>
                                </svg>
                            </label>
                        </div>
                    `;

                    carouselImages.appendChild(newSlide);
                    document.querySelector('#carouselImages').swiper.update();

                    const newInput = document.createElement('input');
                    newInput.type = 'file';
                    newInput.name = 'tricks[images][]';
                    newInput.files = e.target.files;
                    newInput.classList.add('hidden', 'temp-image-input');
                    form.appendChild(newInput);
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // Update existing images
    document.querySelectorAll('[id^="image-replace-"]').forEach((input) => {
        const imageId = input.id.replace('image-replace-', 'slider-image-');
        const imageElement = document.getElementById(imageId);

        if (input && imageElement) {
            input.addEventListener('change', (e) => {
                const file = e.target.files[0];

                if (file && file.size > MAX_FILE_SIZE_BYTES) {
                    alert(`L'image est trop volumineuse. La taille maximale autorisée est de ${MAX_FILE_SIZE_MB} Mo.`);
                    e.target.value = "";
                    return;
                }

                if (file) {
                    const reader = new FileReader();
                    reader.onload = (event) => {
                        imageElement.src = event.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            });
        }
    });

    // Delete added images
    if (carouselImages) {
        carouselImages.addEventListener('click', (e) => {
            const deleteIcon = e.target.closest('label');
            if (!deleteIcon) return;

            const parentSlide = deleteIcon.closest('.swiper-slide');
            if (parentSlide && parentSlide.classList.contains('temp-slide')) {
                parentSlide.remove();
                document.querySelector('#carouselImages').swiper.update();

                const tempInputs = form.querySelectorAll('.temp-image-input');
                if (tempInputs.length > 0) {
                    tempInputs[tempInputs.length - 1].remove();
                }
            }
        });
    }

    // Delete existing media
    document.querySelectorAll('input[type="checkbox"][data-overlay]').forEach((checkbox) => {
        checkbox.addEventListener('change', (e) => {
            const overlayId = e.target.getAttribute('data-overlay');
            const overlay = document.getElementById(overlayId);

            if (overlay) {
                if (e.target.checked) {
                    overlay.classList.remove('hidden');
                } else {
                    overlay.classList.add('hidden');
                }
            }
        });
    });

    // Vidéos slider management
    const cleanVideoLink = (link) => {
        if (link.includes('youtube.com')) {
            const urlParams = new URLSearchParams(new URL(link).search);
            return urlParams.get('v') ? `https://www.youtube.com/watch?v=${urlParams.get('v')}` : link;
        }

        if (link.includes('dailymotion.com')) {
            const matches = link.match(/\/video\/([^_]+)/);
            return matches && matches[1] ? `https://www.dailymotion.com/video/${matches[1]}` : link;
        }

        return link;
    };

    // Add new vidéo
    const openAddVideoModal = document.getElementById('openAddVideoModal');
    const addVideoModal = document.getElementById('addVideoModal');
    const cancelAddVideo = document.getElementById('cancelAddVideo');
    const confirmAddVideo = document.getElementById('confirmAddVideo');
    const videoLinkInput = document.getElementById('video-link');
    const carouselVideos = document.querySelector('#carouselVideos .swiper-wrapper');

    if (openAddVideoModal) {
        openAddVideoModal.addEventListener('click', (e) => {
            e.preventDefault();
            addVideoModal.classList.remove('hidden');
            addVideoModal.classList.add('flex');
        });
    }

    if (cancelAddVideo) {
        cancelAddVideo.addEventListener('click', (e) => {
            e.preventDefault();
            videoLinkInput.value = '';
            addVideoModal.classList.remove('flex');
            addVideoModal.classList.add('hidden');
        });
    }

    if (addVideoModal) {
        addVideoModal.addEventListener('click', (e) => {
            if (e.target === addVideoModal) {
                videoLinkInput.value = '';
                addVideoModal.classList.remove('flex');
                addVideoModal.classList.add('hidden');
            }
        });
    }

    if (confirmAddVideo) {
        confirmAddVideo.addEventListener('click', (e) => {
            e.preventDefault();
            let videoUrl = videoLinkInput.value.trim();

            if (!videoUrl) {
                alert('Veuillez entrer un lien vidéo valide.');
                return;
            }
            videoUrl = cleanVideoLink(videoUrl);
            let embedHtml = `<p>Vidéo non supportée</p>`;

            if (videoUrl.includes('youtube.com')) {
                const youtubeId = videoUrl.split('v=')[1];
                embedHtml = `<iframe class="rounded-lg w-64 h-48 mb-2"
                        src="https://www.youtube.com/embed/${youtubeId}"
                        allowfullscreen></iframe>`;
            } else if (videoUrl.includes('dailymotion.com')) {
                const dailymotionId = videoUrl.split('/').pop();
                embedHtml = `<iframe class="rounded-lg w-64 h-48 mb-2"
                        src="https://www.dailymotion.com/embed/video/${dailymotionId}"
                        allowfullscreen></iframe>`;
            }

            const newSlide = document.createElement('div');
            newSlide.className = 'swiper-slide flex flex-col items-center temp-slide';
            newSlide.innerHTML = `
                <div class="relative">
                    ${embedHtml}
                </div>
                <div class="flex justify-center space-x-2 mt-2">
                    <!-- Delete Button -->
                    <label class="text-red-500 hover:text-red-600 cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M9 3H15V4H20V6H4V4H9V3ZM5 8V19C5 20.1 5.9 21 7 21H17C18.1 21 19 20.1 19 19V8H5ZM8 10H10V18H8V10ZM14 10H16V18H14V10Z"></path>
                        </svg>
                    </label>
                </div>
            `;
            carouselVideos.appendChild(newSlide);
            document.querySelector('#carouselVideos').swiper.update();

            const newInput = document.createElement('input');
            newInput.type = 'hidden';
            newInput.name = 'tricks[videos][]';
            newInput.value = videoUrl;
            newInput.classList.add('hidden', 'temp-video-input');
            form.appendChild(newInput);

            videoLinkInput.value = '';
            addVideoModal.classList.add('hidden');
        });
    }

    // Edit vidéo
    const editVideoModal = document.getElementById('editVideoModal');
    const editVideoInput = document.getElementById('edit-video-link');
    const confirmEditVideo = document.getElementById('confirmEditVideo');
    const cancelEditVideo = document.getElementById('cancelEditVideo');
    let currentVideoId = null;

    document.querySelectorAll('.edit-video-icon').forEach((icon) => {
        icon.addEventListener('click', (e) => {
            e.preventDefault();
            currentVideoId = icon.dataset.videoId;
            editVideoInput.value = icon.dataset.videoLink;
            editVideoModal.classList.remove('hidden');
            editVideoModal.classList.add('flex');
        });
    });

    if (cancelEditVideo) {
        cancelEditVideo.addEventListener('click', (e) => {
            e.preventDefault();
            editVideoModal.classList.remove('flex');
            editVideoModal.classList.add('hidden');
        });
    }

    if (confirmEditVideo) {
        confirmEditVideo.addEventListener('click', (e) => {
            e.preventDefault();
            const newVideoLink = editVideoInput.value.trim();

            if (!newVideoLink) {
                alert('Veuillez entrer un lien vidéo valide.');
                return;
            }

            const cleanedLink = cleanVideoLink(newVideoLink);
            const videoIframe = document.getElementById(`video-iframe-${currentVideoId}`);
            const videoPlaceholder = document.getElementById(`video-placeholder-${currentVideoId}`);

            if (videoIframe) {
                if (cleanedLink.includes('youtube.com')) {
                    const youtubeId = cleanedLink.split('v=')[1];
                    videoIframe.src = `https://www.youtube.com/embed/${youtubeId}`;
                } else if (cleanedLink.includes('dailymotion.com')) {
                    const dailymotionId = cleanedLink.split('/').pop();
                    videoIframe.src = `https://www.dailymotion.com/embed/video/${dailymotionId}`;
                }
            } else if (videoPlaceholder) {
                videoPlaceholder.textContent = 'Vidéo non supportée';
            }

            const form = document.querySelector('form[name="tricks"]');
            let hiddenInput = form.querySelector(`input[name="existing_videos_replace[${currentVideoId}]"]`);

            if (!hiddenInput) {
                hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = `existing_videos_replace[${currentVideoId}]`;
                form.appendChild(hiddenInput);
            }
            hiddenInput.value = cleanedLink;
            editVideoModal.classList.remove('flex');
            editVideoModal.classList.add('hidden');
        });
    }

    // Delete added vidéos
    if (carouselVideos) {
        carouselVideos.addEventListener('click', (e) => {
            const deleteIcon = e.target.closest('label');
            if (!deleteIcon) return;

            const parentSlide = deleteIcon.closest('.swiper-slide');
            if (parentSlide && parentSlide.classList.contains('temp-slide')) {
                parentSlide.remove();
                document.querySelector('#carouselVideos').swiper.update();

                const videoInputs = form.querySelectorAll('.temp-video-input');
                if (videoInputs.length > 0) {
                    videoInputs[videoInputs.length -1].remove();
                }
            }
        });
    }
});
