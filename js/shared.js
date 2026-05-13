// Shared constants
const TIRE_SIZES = [40, 80, 120, 200, 300, 600];

// Real-time clock
function updateClock() {
    const el = document.getElementById('headerClock');
    if (el) {
        el.textContent = new Date().toLocaleDateString('th-TH', {
            year: 'numeric', month: 'short', day: 'numeric',
            hour: '2-digit', minute: '2-digit', second: '2-digit'
        });
    }
}
document.addEventListener('DOMContentLoaded', function() {
    updateClock();
    setInterval(updateClock, 1000);
});
