import 'swiper/css';

async function initTripGallerySwipers() {
    const galleries = document.querySelectorAll('.trip-gallery');

    if (galleries.length === 0) {
        return;
    }

    const { Swiper, Navigation, Pagination } = await import('./swiper.js');

    galleries.forEach((wrapper) => {
        const el = wrapper.querySelector('.trip-gallery-swiper');

        if (!el || el.swiper) {
            return;
        }

        const slideCount = el.querySelectorAll('.swiper-slide').length;

        new Swiper(el, {
            modules: [Navigation, Pagination],
            loop: slideCount > 1,
            navigation: {
                nextEl: wrapper.querySelector('.trip-gallery-next'),
                prevEl: wrapper.querySelector('.trip-gallery-prev'),
            },
            pagination: {
                el: wrapper.querySelector('.trip-gallery-pagination'),
                clickable: true,
                bulletClass: 'trip-gallery-bullet',
                bulletActiveClass: 'trip-gallery-bullet-active',
            },
        });
    });
}

function initTripSectionNav() {
    const nav = document.querySelector('.trip-section-nav');

    if (!nav) {
        return;
    }

    nav._scrollSpyObserver?.disconnect();

    const links = [...nav.querySelectorAll('a[href^="#"]')];
    const sections = links
        .map((link) => document.querySelector(link.getAttribute('href')))
        .filter(Boolean);

    if (sections.length === 0) {
        return;
    }

    const setActive = (id) => {
        links.forEach((link) => {
            link.classList.toggle('border-primary', link.getAttribute('href') === `#${id}`);
            link.classList.toggle('text-primary', link.getAttribute('href') === `#${id}`);
        });
    };

    const observer = new IntersectionObserver(
        (entries) => {
            const visible = entries.find((entry) => entry.isIntersecting);

            if (visible) {
                setActive(visible.target.id);
            }
        },
        { rootMargin: '-96px 0px -70% 0px', threshold: 0 },
    );

    sections.forEach((section) => observer.observe(section));
    nav._scrollSpyObserver = observer;
}

initTripGallerySwipers();
initTripSectionNav();
document.addEventListener('livewire:navigated', () => {
    initTripGallerySwipers();
    initTripSectionNav();
});

document.addEventListener('click', async (event) => {
    const button = event.target.closest('[data-share]');

    if (!button) {
        return;
    }

    const shareData = { title: button.dataset.title, url: button.dataset.url };

    if (navigator.share) {
        navigator.share(shareData).catch(() => {});
        return;
    }

    try {
        await navigator.clipboard.writeText(shareData.url);
        const label = button.querySelector('[data-share-label]');
        const original = label.textContent;
        label.textContent = 'Copied!';
        setTimeout(() => { label.textContent = original; }, 2000);
    } catch {
        //
    }
});

document.addEventListener('click', (event) => {
    const summary = event.target.closest('details > summary');

    if (!summary || summary.parentElement._animating) {
        return;
    }

    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        return;
    }

    const details = summary.parentElement;
    const content = summary.nextElementSibling;

    if (!content) {
        return;
    }

    event.preventDefault();
    details._animating = true;

    const opening = !details.open;

    if (opening) {
        details.open = true;
    }

    const endHeight = opening ? content.getBoundingClientRect().height : 0;
    const startHeight = opening ? 0 : content.getBoundingClientRect().height;

    content.style.overflow = 'hidden';

    let animation;

    try {
        animation = content.animate(
            [{ height: `${startHeight}px` }, { height: `${endHeight}px` }],
            { duration: 250, easing: 'cubic-bezier(0.16, 1, 0.3, 1)' },
        );
    } catch {
        content.style.overflow = '';
        details._animating = false;

        if (!opening) {
            details.open = false;
        }

        return;
    }

    animation.onfinish = () => {
        content.style.height = '';
        content.style.overflow = '';
        details._animating = false;

        if (!opening) {
            details.open = false;
        }
    };
});
