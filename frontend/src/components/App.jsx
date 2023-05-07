/* eslint-disable react/react-in-jsx-scope */
import { useState } from 'react';
import AuthenticationPage from './Authentication/AuthenticationPage';
import Main from './Main/Main';
import { initAuthState } from '../BusinessDomain/State/AuthState';

function App() {
  const [authState, setAuthState] = useState(initAuthState);

  let Page = <AuthenticationPage authState={authState} setAuthState={setAuthState} />;

  if (authState.isLoggedIn) {
    Page = <Main authState={authState} setAuthState={setAuthState} />;
  }

  return Page;
}

export default App;
