// Client-side JavaScript for Malawi Tourism Blog

document.addEventListener('DOMContentLoaded', function() {
    // Confirm delete actions
    const deleteForms = document.querySelectorAll('.delete-form');
    
    deleteForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!confirm('Are you sure you want to delete this post?')) {
                e.preventDefault();
            }
        });
    });
    
    // Add fade-in animation to elements
    const animateElements = document.querySelectorAll('.post-card, .full-post, .form-section');
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = 1;
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, { threshold: 0.1 });
    
    animateElements.forEach(el => {
        el.style.opacity = 0;
        el.style.transform = 'translateY(20px)';
        el.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
        observer.observe(el);
    });
    
    // Enhance textareas with auto-resize
    const textareas = document.querySelectorAll('textarea');
    
    textareas.forEach(textarea => {
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });
        
        // Trigger input event to set initial height
        textarea.dispatchEvent(new Event('input'));
    });
});