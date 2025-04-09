import './bootstrap';
import './evaluacion';
import { notificationBell } from './notifications';

import Alpine from 'alpinejs';

window.Alpine = Alpine;
window.notificationBell = notificationBell;
Alpine.start();
