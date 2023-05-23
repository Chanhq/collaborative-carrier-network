export default {
    persistUserSessionClientSide: (user) => {
        localStorage.setItem('user', JSON.stringify(user));
    },
    getUserSessionClientSide: () => {
        return JSON.parse(localStorage.getItem('user'));
    },
    deleteUserSessionClientSide:() => {
        localStorage.removeItem('user')
    }
}