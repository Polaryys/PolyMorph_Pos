import './bootstrap';

import Alpine from 'alpinejs';
import { ventasCarrito } from './ventas';

window.Alpine = Alpine;

Alpine.data('ventasCarrito', ventasCarrito);

Alpine.start();
