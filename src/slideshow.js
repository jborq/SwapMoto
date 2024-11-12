const images = [
    './public/images/warsaw.jpg',
    './public/images/lodz.jpg',
    './public/images/gdansk.jpg',
    './public/images/cracow.jpg',
];

let currentIndex = 0;

function changeBackground() {
    const container = document.querySelector('.heroHeader');
    container.style.backgroundImage = `url(${images[currentIndex]})`;
    currentIndex = (currentIndex + 1) % images.length;
}

export function startSlideshow() {
    changeBackground();
    setInterval(changeBackground, 5000);
}