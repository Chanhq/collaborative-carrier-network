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
import { LOGIN_ACTION } from '../AuthenticationPage';
import { SimpleError } from '../Errors/SimpleError';
import './SignUpForm.css';
import InfoIcon from '@mui/icons-material/Info';
import { userRegister } from '../../../BusinessDomain/Service/auth.service';

const SignUpSchema = yup.object({
  name: yup.string().required('Username is required'),
  email: yup.string().email('Enter a valid email address').required('Email is required'),
  password: yup.string().required('Password is required'),
  passwordConfirmation: yup
    .string()
    .oneOf([yup.ref('password'), null], 'Passwords must match')
    .required('Password is required')
});

export default function SignUp(props) {
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
          Sign Up
        </Typography>
        <Formik
          initialValues={{
            name: '',
            email: '',
            password: '',
            passwordConfirmation: ''
          }}
          validationSchema={SignUpSchema}
          onSubmit={async (values, { setSubmitting, setStatus }) => {
            setSubmitting(false);
            userRegister({
              name: values.name,
              email: values.email,
              password: values.password,
              setFormStatus: setStatus,
              setAuthAction: props.setAuthAction
            });
          }}
        >
          {({ submitForm, isSubmitting, status }) => (
            <Box component={Form} noValidate sx={{ mt: 1 }}>
              {status?.error ? SimpleError({ status: status }) : null}
              <Field
                component={FormikTextField}
                name="name"
                type="text"
                id="name"
                label="Username"
                fullWidth
                className={'username-field'}
              />
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
                autoComplete="new-password"
                margin="normal"
                fullWidth
              />
              <Field
                component={FormikTextField}
                name="passwordConfirmation"
                type="password"
                id="passwordConfirmation"
                label="Confirm Password"
                autoComplete="new-password"
                fullWidth
              />
              <div className="notice-container">
                <InfoIcon className="notice-icon" />
                <p className="notice-message">
                  By signing up you accept our <br /> <Link href="#">Terms of Service</Link> and{' '}
                  <Link href="#">Privacy Policy</Link>
                </p>
              </div>
              {isSubmitting && <LinearProgress />}
              <Button
                type="submit"
                fullWidth
                variant="contained"
                disabled={isSubmitting}
                onClick={submitForm}
                sx={{ mt: 2, mb: 2 }}
              >
                Sign Up
              </Button>
              <Grid container>
                <Grid item>
                  <Link onClick={() => props.setAuthAction(LOGIN_ACTION)} href="#">
                    {'Already have an account? Sign In'}
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
