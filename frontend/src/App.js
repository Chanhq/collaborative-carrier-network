import logo from './logo.svg';
import './App.css';
import {useEffect, useState} from "react";

async function testApiCall() {
  const response = await fetch("/test");
  return await response.json();
}
function App() {
  const url = "/test";
  const [data, setData] = useState([]);

  const fetchInfo = () => {
    return fetch(url)
        .then((res) => res.json())
        .then((d) => setData(d))
  }

  useEffect(() => {
    fetchInfo();
  }, []);

  return (
    <div className="App">
      <header className="App-header">
        <img src={logo} className="App-logo" alt="logo" />
        <p>
          { data.greeting }
        </p>
      </header>
    </div>
  );
}

export default App;
