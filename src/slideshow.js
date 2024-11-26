const slides = document.querySelectorAll('.achievement-slide');
let currentIndex = 0;

function changeSlide() {
    slides.forEach((slide, index) => {
        slide.classList.remove('active');
        if (index === currentIndex) {
            slide.classList.add('active');
        }
    });
}

function nextSlide() {
    currentIndex = (currentIndex + 1) % slides.length;
    changeSlide();
}

function prevSlide() {
    currentIndex = (currentIndex - 1 + slides.length) % slides.length;
    changeSlide();
}

export function startSlideshow() {
    changeSlide();
    setInterval(nextSlide, 10000);
}

document.querySelector('.next-slide').addEventListener('click', nextSlide);
document.querySelector('.prev-slide').addEventListener('click', prevSlide);