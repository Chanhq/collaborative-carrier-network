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
import { Checkbox, FormControlLabel } from '@mui/material';
import { LOGIN_TEMPLATE } from '../AuthenticationComponent';
import PropTypes from 'prop-types';
import httpClient from '../../../lib/infrastructure/http-client';
import {useState} from 'react';

export default function RegisterForm({ switchAuthenticationTemplateTo }) {
	const [username, setUsername] = useState('');
	const [password, setPassword] = useState('');
	const [isAuctioneerRegistration, setIsAuctioneerRegistration] = useState(false);

	const handleSubmit = (event) => {
		event.preventDefault();

		httpClient.post('/api/auth/register', { username, password, isAuctioneerRegistration })
			.then(() => {
				alert('Created user successfully!');
				switchToLoginForm();
			})
			.catch(() => {
				alert('Something went wrong! Try again.');
				setUsername('');
				setPassword('');
				setIsAuctioneerRegistration(false);
			});
	};

	const switchToLoginForm = () => {
		switchAuthenticationTemplateTo(LOGIN_TEMPLATE);
	};

	return (
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
				<Avatar sx={{ m: 1 }}>
					<LockOutlinedIcon />
				</Avatar>
				<Typography component="h1" variant="h5">
		Sign Up
				</Typography>
				<Box component="form" onSubmit={handleSubmit} noValidate sx={{ mt: 1 }}>
					<TextField
						margin="normal"
						required
						fullWidth
						id="username"
						label="Username"
						name="username"
						value={username}
						autoComplete="username"
						autoFocus
						onChange={(e) => setUsername(e.target.value)}
					/>
					<TextField
						margin="normal"
						required
						fullWidth
						name="password"
						value={password}
						label="Password"
						type="password"
						id="password"
						autoComplete="current-password"
						onChange={(e) => setPassword(e.target.value)}
					/>
					<FormControlLabel
						id="isAuctioneerRegistration"
						name="isAuctioneerRegistration"
						control={<Checkbox
							value={isAuctioneerRegistration}
							onChange={(e) => setIsAuctioneerRegistration(e.target.value)}
						/>}
						label="Register as an auctioneer"
					/>
					<Button type="submit" fullWidth variant="contained" sx={{ mt: 3, mb: 2 }}>
		  Sign Up
					</Button>
					<Grid container>
						<Grid item>
							<Link href="#" variant="body2" onClick={switchToLoginForm}>
			  Already have an account? Sign In
							</Link>
						</Grid>
					</Grid>
				</Box>
			</Box>
		</Container>
	);
}

RegisterForm.propTypes = {
	switchAuthenticationTemplateTo: PropTypes.func
};
