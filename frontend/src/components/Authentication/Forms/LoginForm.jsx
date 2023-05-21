import * as React from 'react';
import Avatar from '@mui/material/Avatar';
import Button from '@mui/material/Button';
import CssBaseline from '@mui/material/CssBaseline';
import TextField from '@mui/material/TextField';
import Link from '@mui/material/Link';
import Grid from '@mui/material/Grid';
import Box from '@mui/material/Box';
import LockOutlinedIcon from '@mui/icons-material/LockOutlined';
import Typography from '@mui/material/Typography';
import Container from '@mui/material/Container';
import { createTheme, ThemeProvider } from '@mui/material/styles';
import { REGISTER_TEMPLATE } from '../AuthenticationComponent';
import PropTypes from "prop-types";
import authApi from "../../../lib/api/auth.js";
import {useState} from "react";
import sessionHelper from "../../../lib/helper/session.js";
import windowLocationHelper from "../../../lib/helper/window-location.js";

const theme = createTheme();

export default function LoginForm({ switchAuthenticationTemplateTo }) {
  const [username, setUsername] = useState('');
  const [password, setPassword] = useState('');

  const handleSubmit = (event) => {
    event.preventDefault();

    authApi.login(username, password).then((response) => {
      if (response.status) {
        if (response.status === 'success') {
          const user = {
            username: response.data.username,
            isAuctioneer: response.data.isAuctioneer,
            token: response.data.token,
          };
          sessionHelper.persistUserSessionClientSide(user);

          if (response.data.isAuctioneer) {
            windowLocationHelper.redirectTo('/auctioneer');
          } else {
            windowLocationHelper.redirectTo('/carrier');
          }
        } else {
          alert(response.message + ' Try again.');
        }
      } else {
        alert('An unknown error occurred! Try again later.');
      }
    })
  };

  const switchToRegisterForm = () => {
    switchAuthenticationTemplateTo(REGISTER_TEMPLATE);
  };

  return (
    <div>
      <ThemeProvider theme={theme}>
        <Container component="main" maxWidth="xs">
          <CssBaseline />
          <Box
            sx={{
              marginTop: 8,
              display: 'flex',
              flexDirection: 'column',
              alignItems: 'center',
            }}
          >
            <Avatar sx={{ m: 1, bgcolor: 'secondary.main' }}>
              <LockOutlinedIcon />
            </Avatar>
            <Typography component="h1" variant="h5">
              Sign In
            </Typography>
            <Box component="form" onSubmit={handleSubmit} noValidate sx={{ mt: 1 }}>
              <TextField
                margin="normal"
                required
                fullWidth
                id="username"
                label="Username"
                name="username"
                onChange={(e) => setUsername(e.target.value)}
                autoComplete="username"
                autoFocus
              />
              <TextField
                margin="normal"
                required
                fullWidth
                name="password"
                onChange={(e) => setPassword(e.target.value)}
                label="Password"
                type="password"
                id="password"
                autoComplete="current-password"
              />
              <Button type="submit" fullWidth variant="contained" sx={{ mt: 3, mb: 2 }}>
                Sign In
              </Button>
              <Grid container>
                <Grid item>
                  <Link href="#" variant="body2" onClick={switchToRegisterForm}>
                    Do not have an account? Sign Up
                  </Link>
                </Grid>
              </Grid>
            </Box>
          </Box>
        </Container>
      </ThemeProvider>
    </div>
  );
}

LoginForm.propTypes = {
  switchAuthenticationTemplateTo: PropTypes.func
}

