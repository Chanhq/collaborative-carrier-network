export const unAuthenticatedAuthState = {
  username: '',
  token: '',
  isLoggedIn: false
};

const sessionAuthState = sessionStorage.getItem('Authorization');

export const initAuthState =
  sessionAuthState !== null ? JSON.parse(sessionAuthState) : unAuthenticatedAuthState;
