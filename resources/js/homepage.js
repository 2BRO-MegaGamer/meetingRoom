import 'animate.css';
import Resumable from 'resumablejs';
import jQuery from "jquery";
import './homepage/script';
import { show_hide_pass } from "./homepage/script";
import { show_notification } from "./notification_Toasts";
import Alpine from 'alpinejs';
window.Alpine = Alpine;
window.show_hide_pass = show_hide_pass;
window.$ = jQuery;
window.show_notification = show_notification;
window.Resumable = Resumable;
Alpine.start();