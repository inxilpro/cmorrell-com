import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';

// This needs to be set before Alpine.start()
window.Alpine = Alpine;

Alpine.plugin(collapse);
Alpine.start();
