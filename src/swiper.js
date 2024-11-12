let currentIndex = 0;

export function scrollTestimonials(direction) {
    const testimonials = document.querySelector('.testimonials');
    const testimonialCount = document.querySelectorAll('.testimonial').length;
    const visibleTestimonials = 3;
    const maxIndex = Math.ceil(testimonialCount / visibleTestimonials) - 1;

    currentIndex += direction;

    if (currentIndex < 0) {
        currentIndex = maxIndex;
    } else if (currentIndex > maxIndex) {
        currentIndex = 0;
    }

    const offset = -currentIndex * 100 / visibleTestimonials;
    testimonials.style.transform = `translateX(${offset}%)`;
}