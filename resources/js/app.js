import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

// Custom Alpine components
Alpine.data('dropdown', () => ({
    open: false,
    toggle() {
        this.open = !this.open;
    },
    close() {
        this.open = false;
    }
}));

// Image preview component
Alpine.data('imagePreview', () => ({
    imageUrl: null,
    fileChosen(event) {
        if (event.target.files.length === 0) return;
        
        const file = event.target.files[0];
        const reader = new FileReader();
        
        reader.onload = e => {
            this.imageUrl = e.target.result;
        };
        
        reader.readAsDataURL(file);
    },
    clearImage() {
        this.imageUrl = null;
    }
}));

// Notification system
Alpine.data('notifications', () => ({
    notifications: [],
    add(message, type = 'info') {
        const id = Date.now();
        this.notifications.push({ id, message, type });
        
        setTimeout(() => {
            this.remove(id);
        }, 5000);
    },
    remove(id) {
        this.notifications = this.notifications.filter(n => n.id !== id);
    }
}));

// Currency formatter
window.formatCurrency = (amount, currency = 'TRY') => {
    return new Intl.NumberFormat('tr-TR', {
        style: 'currency',
        currency: currency,
    }).format(amount);
};

// Confirm dialog
window.confirmAction = (message = 'Bu işlemi gerçekleştirmek istediğinizden emin misiniz?') => {
    return confirm(message);
};

// AJAX Setup
document.addEventListener('DOMContentLoaded', () => {
    // CSRF token for AJAX requests
    const token = document.querySelector('meta[name="csrf-token"]');
    if (token) {
        window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
    }
    
    // Handle dynamic content loading
    document.addEventListener('click', (e) => {
        // Handle delete confirmations
        if (e.target.matches('[data-confirm]')) {
            const message = e.target.getAttribute('data-confirm');
            if (!confirmAction(message)) {
                e.preventDefault();
            }
        }
        
        // Handle AJAX links
        if (e.target.matches('[data-ajax]')) {
            e.preventDefault();
            const url = e.target.getAttribute('href') || e.target.getAttribute('data-url');
            const method = e.target.getAttribute('data-method') || 'GET';
            
            axios({
                method: method,
                url: url,
            }).then(response => {
                // Handle response
                if (response.data.redirect) {
                    window.location.href = response.data.redirect;
                }
                if (response.data.message) {
                    // Show notification
                    Alpine.find('[x-data]').__x.$data.add(response.data.message, 'success');
                }
            }).catch(error => {
                console.error('AJAX error:', error);
                // Show error notification
                Alpine.find('[x-data]').__x.$data.add('Bir hata oluştu', 'error');
            });
        }
    });
});

Alpine.start();
