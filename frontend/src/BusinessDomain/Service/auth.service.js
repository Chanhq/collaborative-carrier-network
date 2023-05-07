import { LOGIN_ACTION } from '../../components/Authentication/AuthenticationPage';
import { axiosInstance } from '../../Infrastructure/Service/axios.service';
import { unAuthenticatedAuthState } from '../State/AuthState';

export const userLogin = (props) => {
  const axiosData = {
    email: props.email,
    password: props.password
  };

  axiosInstance
    .post('user/login', axiosData)
    .then(function (response) {
      const { data } = response;
      const newAuthState = {
        username: data.data.name,
        token: data.token,
        isLoggedIn: true
      };
      sessionStorage.setItem('Authorization', JSON.stringify(newAuthState));
      props.setAuthState(newAuthState);
    })
    .catch(function (error) {
      props.setFormStatus({
        error: true,
        message: error.response.data.message
      });
    });
};

export const userLogout = (props) => {
  const axiosConfig = {
    headers: {
      Authorization: `Bearer ${props.authState.token}`
    }
  };

  axiosInstance
    .post('user/logout', {}, axiosConfig)
    .then(function (response) {
      const { data } = response;
      if (data.status === 'success') {
        props.setAuthState(unAuthenticatedAuthState);
        sessionStorage.removeItem('Authorization');
      }
    })
    .catch(function () {
      alert('Unable to logout! Try again later');
    });
};

export const userRegister = (props) => {
  const axiosData = {
    name: props.name,
    email: props.email,
    password: props.password
  };

  axiosInstance
    .post('user/register', axiosData)
    .then(function (response) {
      const { data } = response;
      if (data.status === 'success') {
        alert('User successfully created! You are now being redirected to the login form!');
        props.setAuthAction(LOGIN_ACTION);
      }
    })
    // eslint-disable-next-line func-names

    .catch(function (error) {
      props.setFormStatus({
        error: true,
        message: error.response.data.message,
        validation_errors: error.response.data.validation_errors
      });
    });
};
