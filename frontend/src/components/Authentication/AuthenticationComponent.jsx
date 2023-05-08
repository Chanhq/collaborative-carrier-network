import { Grid } from '@mui/material';
import LoginForm from './Forms/LoginForm';

function AuthenticationComponent() {
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
        <LoginForm />
      </Grid>
    </Grid>
  );
}

export default AuthenticationComponent;
