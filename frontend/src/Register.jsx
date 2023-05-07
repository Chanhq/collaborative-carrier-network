import React, { useState } from "react";

export const Register = (props) => {
    const [username, setUsername] = useState('');
    const [pass, setPass] = useState('');

    const handleSubmit = (e) => {
        e.preventDefault();
        console.log(email);
    }

    return (
        <div className="auth-form-container">
            <h2>Registration</h2>         
            <label htmlFor="username">Username :</label>
            <input value={username} onChange={(e) => setUsername(e.target.value)}type="username" placeholder="Your Username" id="username" name="username" />
            <label htmlFor="password">Password :</label>
            <input value={pass} onChange={(e) => setPass(e.target.value)} type="password" placeholder="********" id="password" name="password" />
            <button type="submit">Register</button>           
        </div>
    )
}