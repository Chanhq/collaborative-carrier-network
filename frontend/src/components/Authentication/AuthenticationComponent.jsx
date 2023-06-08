import { Grid } from '@mui/material';
import { useState } from 'react';
import LoginForm from './Forms/LoginForm';
import RegisterForm from './Forms/RegisterForm';
import React from 'react';

export const LOGIN_TEMPLATE = 'login';
export const REGISTER_TEMPLATE = 'register';

function AuthenticationComponent() {
	const [authenticationTemplate, setAuthenticationTemplate] = useState(LOGIN_TEMPLATE);
	let Form = <LoginForm switchAuthenticationTemplateTo={switchAuthenticationTemplateTo} />;

	if (authenticationTemplate === REGISTER_TEMPLATE) {
		Form = <RegisterForm switchAuthenticationTemplateTo={switchAuthenticationTemplateTo} />;
	}

	function switchAuthenticationTemplateTo(template) {
		setAuthenticationTemplate(template);
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
