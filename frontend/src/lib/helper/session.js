export default {
    persistTokenClientSide: (token) => {
        localStorage.setItem('token', token);
    },
}