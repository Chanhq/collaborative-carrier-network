/* eslint-disable quotes */
/* eslint-disable react/react-in-jsx-scope */
import Container from '@mui/material/Container';
import Box from '@mui/material/Box';
import { Avatar, LinearProgress } from '@mui/material';
import { TextField as FormikTextField } from 'formik-mui';
import LockOutlinedIcon from '@mui/icons-material/Lock';
import Typography from '@mui/material/Typography';
import Button from '@mui/material/Button';
import Grid from '@mui/material/Grid';
import Link from '@mui/material/Link';
import * as yup from 'yup';
import { Formik, Form, Field } from 'formik';
import { REGISTER_ACTION } from '../AuthenticationPage';
import { SimpleError } from '../Errors/SimpleError';
import { userLogin } from '../../../BusinessDomain/Service/auth.service';

const SignInSchema = yup.object({
  email: yup.string().email('Enter a valid email address').required('Email is required'),
  password: yup.string().required('Password is required')
});

export default function SignIn({ props }) {
  return (
    <Container component="main" maxWidth="xs">
      <Box
        sx={{
          marginTop: 8,
          display: 'flex',
          flexDirection: 'column',
          alignItems: 'center'
        }}
      >
        <Avatar
          sx={{
            m: 1,
            bgcolor: 'secondary.main'
          }}
        >
          <LockOutlinedIcon />
        </Avatar>
        <Typography component="h1" variant="h5">
          Sign In
        </Typography>
        <Formik
          initialValues={{
            email: '',
            password: ''
          }}
          validationSchema={SignInSchema}
          onSubmit={async (values, { setSubmitting, setStatus }) => {
            setSubmitting(false);
            userLogin({
              email: values.email,
              password: values.password,
              setAuthState: props.setAuthState,
              setFormStatus: setStatus
            });
          }}
        >
          {({ submitForm, isSubmitting, status }) => (
            <Box component={Form} noValidate sx={{ mt: 1 }}>
              {status?.error ? SimpleError({ status }) : null}
              <Field
                component={FormikTextField}
                name="email"
                type="email"
                id="email"
                label="Email"
                autoComplete="email"
                fullWidth
              />
              <Field
                component={FormikTextField}
                name="password"
                type="password"
                id="password"
                label="Password"
                autoComplete="current-password"
                margin="normal"
                fullWidth
              />
              {isSubmitting && <LinearProgress />}
              <Button
                type="submit"
                fullWidth
                variant="contained"
                disabled={isSubmitting}
                onClick={submitForm}
                sx={{ mt: 2, mb: 2 }}
              >
                Sign In
              </Button>
              <Grid container>
                <Grid item>
                  <Link onClick={() => props.setAuthAction(REGISTER_ACTION)} href="#">
                    Don't have an account? Sign Up
                  </Link>
                </Grid>
              </Grid>
            </Box>
          )}
        </Formik>
      </Box>
    </Container>
  );
}
