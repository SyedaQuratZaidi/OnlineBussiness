
document.addEventListener('click', function(e) {
    const card = e.target.closest('.product-card[data-id]');
    if (!card) return;

    // Ignore clicks that are on interactive controls
    if (e.target.closest('a') || e.target.closest('button') || e.target.closest('form')) return;

    const id = card.getAttribute('data-id');
    if (id) {
        window.location.href = 'product-detail.php?id=' + encodeURIComponent(id);
    }
});

// Allow keyboard navigation: activate card with Enter key when focused
document.addEventListener('keydown', function(e) {
    if (e.key === 'Enter') {
        const active = document.activeElement;
        if (active && active.classList && active.classList.contains('product-card') && active.dataset.id) {
            window.location.href = 'product-detail.php?id=' + encodeURIComponent(active.dataset.id);
        }
    }
});
