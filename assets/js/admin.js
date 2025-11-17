// Admin panel JavaScript

// Confirm delete actions
document.querySelectorAll('[onclick*="confirm"]').forEach(function(el) {
    el.addEventListener('click', function(e) {
        if (!confirm('Are you sure you want to delete this item?')) {
            e.preventDefault();
        }
    });
});

// Auto-resize textareas
document.querySelectorAll('textarea').forEach(function(textarea) {
    textarea.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = this.scrollHeight + 'px';
    });
});
