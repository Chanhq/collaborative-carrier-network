const APP_BASE_URL = 'http://localhost:3000';
export default {
    isAlreadyOnPath: (path) => {
        return window.location.href === APP_BASE_URL + path;
    },
    redirectTo(path) {
        window.location.href = APP_BASE_URL + path;
    },
    getPath() {
        return window.location.pathname;
    }
}