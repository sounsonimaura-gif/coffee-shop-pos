import axios from 'axios';

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// jQuery and DataTables are loaded as classic <script> tags in app.blade.php
// before the Vite bundle, because DataTables' UMD source contains bare
// `window = ...` assignments that crash when bundled into a strict-mode ESM
// build. They expose window.jQuery and window.$ which the YajraDataTable
// component consumes.
