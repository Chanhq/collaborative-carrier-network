import React, { useState } from 'react';
import './App.css';
import { Login } from './Login';
import { Register_Carrier } from './Register_Carrier' ;
import { Register_Auctioneer } from './Register_Auctioneer' ;

function App() {

  const [currentForm, setCurrentForm] = useState('login');

  const ToggleForm = (formName) => {
    setCurrentForm(formName);
  }

  if(loginCurrentForm === "login"){
    return (
      <div className="App">
        <Login onFormSwitch={ToggleForm} />      
      </div>
    );
  }else if(carrierCurrentForm === "register_carrier"){
      return (
        <div className="App">
          <Register_Carrier onFormSwitch={carrierToggleForm} />
        </div>
      );
  }else if(auctioneerCurrentForm === "register_auctioneer"){
      return (
        <div className="App">
          <Register_Auctioneer onFormSwitch={auctioneerToggleForm} />
        </div>
      );
  }  
}
export default App;


