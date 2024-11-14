let currentIndex = 0;

export function scrollTestimonials(direction) {
    const testimonials = document.querySelector('.testimonials');
    const testimonialCount = document.querySelectorAll('.testimonial').length;

    let visibleTestimonials;
    if (window.innerWidth <= 480) {
        visibleTestimonials = 1;
    } else if (window.innerWidth <= 768) {
        visibleTestimonials = 2;
    } else {
        visibleTestimonials = 3;
    }

    const maxIndex = Math.max(0, testimonialCount - visibleTestimonials);

    currentIndex += direction;

    if (currentIndex < 0) {
        currentIndex = maxIndex;
    } else if (currentIndex > maxIndex) {
        currentIndex = 0;
    }

    const offset = -currentIndex * 100 / visibleTestimonials;
    testimonials.style.transform = `translateX(${offset}%)`;
}