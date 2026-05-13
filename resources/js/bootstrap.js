import axios from 'axios';
import jQuery from 'jquery';

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// jQuery is required by Yajra DataTables on the client
window.$ = window.jQuery = jQuery;

// Initialise DataTables (used by the YajraDataTable component)
import dataTablesFn from 'datatables.net-bs5';
dataTablesFn(window, window.$);
