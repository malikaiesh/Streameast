// Toggle sidebar
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    sidebar.style.display = sidebar.style.display === 'none' ? 'block' : 'none';
}

// Theme toggle
function toggleTheme() {
    const body = document.body;
    const currentTheme = body.classList.contains('theme-light') ? 'light' : 'dark';
    const newTheme = currentTheme === 'light' ? 'dark' : 'light';
    
    body.classList.remove('theme-' + currentTheme);
    body.classList.add('theme-' + newTheme);
    
    localStorage.setItem('theme', newTheme);
}

// Load theme from localStorage
document.addEventListener('DOMContentLoaded', function() {
    const savedTheme = localStorage.getItem('theme') || 'dark';
    document.body.classList.remove('theme-light', 'theme-dark');
    document.body.classList.add('theme-' + savedTheme);
});

// Shorts horizontal scroll
function scrollShorts(direction) {
    const container = document.getElementById('shortsScroll');
    if (!container) return;
    
    const scrollAmount = 400;
    if (direction === 'left') {
        container.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
    } else {
        container.scrollBy({ left: scrollAmount, behavior: 'smooth' });
    }
}

// Search autocomplete
const searchInput = document.querySelector('.search-form input[name="s"]');
if (searchInput) {
    searchInput.addEventListener('input', function() {
        // Instant search can be implemented here
        console.log('Searching:', this.value);
    });
}
