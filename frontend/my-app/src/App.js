import React from 'react';
import { BrowserRouter } from 'react-router-dom';
import NavigationBar from './NavigationBar';
import Routes from './Routing';

function App() {
  return (
    <BrowserRouter>
      <div>
        <NavigationBar />
        <Routes />
      </div>
    </BrowserRouter>
  );
}

export default App;
