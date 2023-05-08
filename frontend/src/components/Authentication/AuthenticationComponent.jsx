import { Grid } from '@mui/material';
import LoginForm from './Forms/LoginForm';
import RegisterForm from './Forms/RegisterForm';
import { useState } from 'react';

export const LOGIN_ACTION = 'login';
export const REGISTER_ACTION = 'register';

function AuthenticationComponent() {
  const [authenticationAction, setAuthenticationAction] = useState(LOGIN_ACTION);
  let Form = <LoginForm setAuthentiationAction={setAuthenticationAction}></LoginForm>;

  if (authenticationAction === REGISTER_ACTION) {
    Form = <RegisterForm setAuthenticationAction={setAuthenticationAction}></RegisterForm>;
  }

  return (
    <Grid
      container
      spacing={0}
      direction="column"
      alignItems="center"
      justifyContent="center"
      style={{ minHeight: '100vh' }}
    >
      <Grid item xs={3}>
        {Form}
      </Grid>
    </Grid>
  );
}

export default AuthenticationComponent;
