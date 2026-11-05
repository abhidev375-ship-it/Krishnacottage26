import './bootstrap';
import { createIcons, icons } from 'lucide';

window.createIcons = () => createIcons({ icons });

document.addEventListener('DOMContentLoaded', () => {
    window.createIcons();
});
