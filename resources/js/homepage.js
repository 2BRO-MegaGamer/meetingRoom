import './bootstrap';
import 'animate.css';
import './homepage/script';
import { show_hide_pass } from "./homepage/script";
import Alpine from 'alpinejs';
window.Alpine = Alpine;
window.show_hide_pass = show_hide_pass;

Alpine.start();