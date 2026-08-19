import sort from '@alpinejs/sort';
Alpine.plugin(sort);

import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
