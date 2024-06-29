import 'bootstrap';
import axios from 'axios';
import {Toast} from 'bootstrap';

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.Toast = Toast;
