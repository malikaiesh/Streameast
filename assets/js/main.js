// Toggle sidebar
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    sidebar.style.display = sidebar.style.display === 'none' ? 'block' : 'none';
}

// Search autocomplete
const searchInput = document.querySelector('.search-form input[name="s"]');
if (searchInput) {
    searchInput.addEventListener('input', function() {
        // Instant search can be implemented here
        console.log('Searching:', this.value);
    });
}
