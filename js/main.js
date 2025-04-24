// Main JavaScript file

document.addEventListener('DOMContentLoaded', function() {
    // Mobile menu toggle
    const mobileMenuButton = document.querySelector('.navbar-toggler');
    const mobileMenu = document.querySelector('.navbar-collapse');
    
    if (mobileMenuButton && mobileMenu) {
        mobileMenuButton.addEventListener('click', function() {
            mobileMenu.classList.toggle('show');
        });
    }

    // Quantity input validation
    const quantityInputs = document.querySelectorAll('input[type="number"]');
    quantityInputs.forEach(input => {
        input.addEventListener('change', function() {
            if (this.value < 1) {
                this.value = 1;
            }
            if (this.max && this.value > this.max) {
                this.value = this.max;
            }
        });
    });

    // Form validation
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('border-red-500');
                } else {
                    field.classList.remove('border-red-500');
                }
            });

            if (!isValid) {
                e.preventDefault();
                showToast('Please fill in all required fields');
            }
        });
    });

    // Toast notification function
    function showToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
        toast.textContent = message;
        document.body.appendChild(toast);

        setTimeout(() => {
            toast.remove();
        }, 3000);
    }

    // Add to cart animation
    const addToCartButtons = document.querySelectorAll('[name="add_to_cart"]');
    addToCartButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            const productCard = this.closest('.product-card');
            if (productCard) {
                productCard.classList.add('fade-in');
            }
        });
    });

    // Price range slider
    const priceRange = document.querySelector('.price-range');
    if (priceRange) {
        noUiSlider.create(priceRange, {
            start: [0, 9999],
            connect: true,
            range: {
                'min': 0,
                'max': 9999
            }
        });

        priceRange.noUiSlider.on('update', function(values) {
            const minPrice = document.querySelector('[name="min_price"]');
            const maxPrice = document.querySelector('[name="max_price"]');
            if (minPrice && maxPrice) {
                minPrice.value = Math.round(values[0]);
                maxPrice.value = Math.round(values[1]);
            }
        });
    }

    // Lazy loading images
    const lazyImages = document.querySelectorAll('img[data-src]');
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.removeAttribute('data-src');
                    observer.unobserve(img);
                }
            });
        });

        lazyImages.forEach(img => imageObserver.observe(img));
    } else {
        // Fallback for browsers that don't support IntersectionObserver
        lazyImages.forEach(img => {
            img.src = img.dataset.src;
            img.removeAttribute('data-src');
        });
    }

    // Smooth scrolling
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });
    });
}); 