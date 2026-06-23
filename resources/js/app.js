import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.data('siteNavigation', () => ({
    open: false,
    toggle() {
        this.open = !this.open;
    },
    close() {
        this.open = false;
    },
}));

Alpine.start();
