// Mobile Navigation Toggle
const hamburger = document.getElementById('hamburger');
const navMenu = document.getElementById('navMenu');

if (hamburger && navMenu) {
    hamburger.addEventListener('click', (e) => {
        e.stopPropagation();
        navMenu.classList.toggle('active');
        hamburger.classList.toggle('active');
    });

    // Close menu when clicking on a link
    document.querySelectorAll('.nav-menu a').forEach(link => {
        link.addEventListener('click', () => {
            navMenu.classList.remove('active');
            hamburger.classList.remove('active');
        });
    });
    
    // Close menu when clicking outside
    document.addEventListener('click', (e) => {
        if (navMenu.classList.contains('active') && 
            !navMenu.contains(e.target) && 
            !hamburger.contains(e.target)) {
            navMenu.classList.remove('active');
            hamburger.classList.remove('active');
        }
    });
    
    // Close menu on window resize if it's larger than mobile
    window.addEventListener('resize', () => {
        if (window.innerWidth > 768) {
            navMenu.classList.remove('active');
            hamburger.classList.remove('active');
        }
    });
}

// Dropdown Menu Functionality
document.querySelectorAll('.nav-dropdown').forEach(dropdown => {
    const dropdownLink = dropdown.querySelector('a');
    const dropdownMenu = dropdown.querySelector('.dropdown-menu');
    
    // Toggle dropdown on click (for mobile)
    if (dropdownLink && dropdownMenu) {
        dropdownLink.addEventListener('click', (e) => {
            if (window.innerWidth <= 768) {
                e.preventDefault();
                dropdown.classList.toggle('active');
            }
        });
    }
    
    // Close dropdown when clicking outside
    document.addEventListener('click', (e) => {
        if (!dropdown.contains(e.target)) {
            dropdown.classList.remove('active');
        }
    });
});

// Smooth scrolling for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// Form validation enhancement
const contactForm = document.querySelector('.contact-form');
if (contactForm) {
    contactForm.addEventListener('submit', function(e) {
        const name = document.getElementById('name').value.trim();
        const email = document.getElementById('email').value.trim();
        const message = document.getElementById('message').value.trim();
        
        if (!name || !email || !message) {
            e.preventDefault();
            alert('Please fill in all required fields.');
            return false;
        }
        
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            e.preventDefault();
            alert('Please enter a valid email address.');
            return false;
        }
    });
}

// Scroll to top functionality (optional enhancement)
window.addEventListener('scroll', function() {
    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
    // You can add a scroll-to-top button here if needed
});

// Add animation on scroll (optional enhancement)
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver(function(entries) {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
        }
    });
}, observerOptions);

// Observe elements for animation
document.querySelectorAll('.feature-card, .service-card, .value-item').forEach(el => {
    el.style.opacity = '0';
    el.style.transform = 'translateY(20px)';
    el.style.transition = 'opacity 0.6s, transform 0.6s';
    observer.observe(el);
});

// Services Carousel
const servicesCarousel = document.getElementById('servicesCarousel');
const servicesPrev = document.getElementById('servicesPrev');
const servicesNext = document.getElementById('servicesNext');

if (servicesCarousel && servicesPrev && servicesNext) {
    const services = servicesCarousel.querySelectorAll('.service-preview-card');
    let currentIndex = 0;
    
    function getItemsPerView() {
        const width = window.innerWidth;
        if (width <= 375) {
            return 1;
        } else if (width <= 480) {
            return 1;
        } else if (width <= 768) {
            return 1;
        } else if (width <= 1024) {
            return 2;
        } else if (width <= 1200) {
            return 3;
        } else {
            return 4;
        }
    }
    
    function updateCarousel() {
        if (services.length === 0) return;
        
        const itemsPerView = getItemsPerView();
        const maxIndex = Math.max(0, services.length - itemsPerView);
        
        // If all items fit in one view, hide both buttons
        if (services.length <= itemsPerView) {
            servicesPrev.classList.add('hidden');
            servicesNext.classList.add('hidden');
            servicesPrev.disabled = true;
            servicesNext.disabled = true;
            return;
        }
        
        // Show/hide buttons based on position
        if (currentIndex === 0) {
            servicesPrev.classList.add('hidden');
            servicesPrev.disabled = true;
        } else {
            servicesPrev.classList.remove('hidden');
            servicesPrev.disabled = false;
        }
        
        if (currentIndex >= maxIndex) {
            servicesNext.classList.add('hidden');
            servicesNext.disabled = true;
        } else {
            servicesNext.classList.remove('hidden');
            servicesNext.disabled = false;
        }
        
        // Calculate scroll position
        const firstCard = services[0];
        if (firstCard && firstCard.offsetWidth > 0) {
            const cardWidth = firstCard.offsetWidth;
            const gap = window.innerWidth <= 768 ? 20 : 20; // 1.25rem gap
            const scrollAmount = currentIndex * (cardWidth + gap);
            
            servicesCarousel.style.transform = `translateX(-${scrollAmount}px)`;
        } else {
            // Retry if card width not ready
            setTimeout(updateCarousel, 50);
        }
    }
    
    servicesPrev.addEventListener('click', () => {
        if (currentIndex > 0) {
            currentIndex--;
            updateCarousel();
        }
    });
    
    servicesNext.addEventListener('click', () => {
        const itemsPerView = getItemsPerView();
        const maxIndex = Math.max(0, services.length - itemsPerView);
        if (currentIndex < maxIndex) {
            currentIndex++;
            updateCarousel();
        }
    });
    
    // Update on window resize
    let resizeTimer;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => {
            const newItemsPerView = getItemsPerView();
            const newMaxIndex = Math.max(0, services.length - newItemsPerView);
            if (currentIndex > newMaxIndex) {
                currentIndex = newMaxIndex;
            }
            updateCarousel();
        }, 250);
    });
    
    // Touch support for mobile devices
    let touchStartX = 0;
    let touchEndX = 0;
    
    servicesCarousel.addEventListener('touchstart', (e) => {
        touchStartX = e.changedTouches[0].screenX;
    }, { passive: true });
    
    servicesCarousel.addEventListener('touchend', (e) => {
        touchEndX = e.changedTouches[0].screenX;
        handleSwipe();
    }, { passive: true });
    
    function handleSwipe() {
        const swipeThreshold = 50;
        const diff = touchStartX - touchEndX;
        
        if (Math.abs(diff) > swipeThreshold) {
            if (diff > 0) {
                // Swipe left - next
                const itemsPerView = getItemsPerView();
                const maxIndex = Math.max(0, services.length - itemsPerView);
                if (currentIndex < maxIndex) {
                    currentIndex++;
                    updateCarousel();
                }
            } else {
                // Swipe right - previous
                if (currentIndex > 0) {
                    currentIndex--;
                    updateCarousel();
                }
            }
        }
    }
    
    // Initialize after a short delay to ensure elements are rendered
    function initCarousel() {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => {
                setTimeout(updateCarousel, 100);
            });
        } else {
            setTimeout(updateCarousel, 100);
        }
    }
    
    initCarousel();
    
    // Recalculate on load to ensure proper sizing
    window.addEventListener('load', () => {
        setTimeout(updateCarousel, 200);
    });
}

// Project Gallery Toggle Functionality
(function() {
    'use strict';
    
    function initProjectGalleries() {
        const detailButtons = document.querySelectorAll('.project-details-btn');
        
        detailButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const projectItem = this.closest('.project-item');
                if (!projectItem) return;
                
                const gallery = projectItem.querySelector('.project-gallery');
                if (!gallery) return;
                
                // Toggle active class
                const isActive = gallery.classList.contains('active');
                
                const icon = this.querySelector('i');
                const span = this.querySelector('span');
                
                if (isActive) {
                    // Collapse
                    gallery.classList.remove('active');
                    this.classList.remove('active');
                    if (span) span.textContent = 'More Details';
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-images');
                } else {
                    // Expand
                    gallery.classList.add('active');
                    this.classList.add('active');
                    if (span) span.textContent = 'Less Details';
                    icon.classList.remove('fa-images');
                    icon.classList.add('fa-times');
                    
                    // Lazy load images when gallery expands (if needed)
                    const images = gallery.querySelectorAll('img[loading="lazy"]');
                    images.forEach(img => {
                        // Images are already loaded via src attribute, but we can trigger a reload if needed
                        if (img.complete === false) {
                            img.addEventListener('load', function() {
                                this.style.opacity = '1';
                            });
                            img.style.opacity = '0.5';
                        }
                    });
                }
            });
        });
    }
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initProjectGalleries);
    } else {
        initProjectGalleries();
    }
})();

