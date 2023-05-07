/* eslint-disable react/react-in-jsx-scope */
import { useState } from 'react';
import Header from '../Header/Header';
import SignIn from './Form/SignInForm';
import SignUp from './Form/SignUpForm';

export const LOGIN_ACTION = 'login';
export const REGISTER_ACTION = 'register';

export default function AuthenticationPage(props) {
  const [authAction, setAuthAction] = useState(LOGIN_ACTION);
  let Form = (
    <SignIn
      authState={props.authState}
      setAuthState={props.setAuthState}
      setAuthAction={setAuthAction}
    />
  );

  if (authAction === REGISTER_ACTION) {
    Form = (
      <SignUp
        authState={props.authState}
        setAuthState={props.setAuthState}
        setAuthAction={setAuthAction}
      />
    );
  }

  return (
    <>
      <Header authState={props.authState} setAuthState={props.setAuthState} />
      {Form}
    </>
  );
}
