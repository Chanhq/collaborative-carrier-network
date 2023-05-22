import { useState } from 'react';
import axios from 'axios';

function Register() {
  const [username, setUsername] = useState('');
  const [password, setPassword] = useState('');
  const [type, setType] = useState('');

  const handleSubmit = (event) => {
    event.preventDefault();
    axios.post('/api/register', { username, password, type })
      .then((response) => {
        console.log(response.data);
      })
      .catch((error) => {
        console.error(error);
      });
  };

  return (
    <form onSubmit={handleSubmit}>
      <label>
        Username:
        <input type="text" value={username} onChange={(event) => setUsername(event.target.value)} />
      </label>
      <label>
        Password:
        <input type="password" value={password} onChange={(event) => setPassword(event.target.value)} />
      </label>
      <label>
        Type:
        <select value={type} onChange={(event) => setType(event.target.value)}>
          <option value="auctioneer">Auctioneer</option>
          <option value="carrier">Carrier</option>
        </select>
      </label>
      <button type="submit">Register</button>
    </form>
  );
}

export default Register;
