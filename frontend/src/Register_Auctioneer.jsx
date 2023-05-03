import React, { useState } from "react";

export const Register_Auctioneer = (props) => {
    const [email, setEmail] = useState('');
    const [pass, setPass] = useState('');
    const [name, setName] = useState('');

    const handleSubmit = (e) => {
        e.preventDefault();
        console.log(email);
    }

    return (
        <div className="auth-form-container">
            <h2>Auctioneer Registration</h2>
            <form className="auctioneer-register-form" onSubmit={handleSubmit}>
                <label htmlFor="name">Enter your Full Name : </label>
                <input value={name} name="name" onChange={(e) => setName(e.target.value)} id="name" placeholder="Your Full Name" />
                <label htmlFor="email">Enter your new Email :</label>
                <input value={email} onChange={(e) => setEmail(e.target.value)}type="email" placeholder="youremail@gmail.com" id="email" name="email" />
                <label htmlFor="password">Enter your new password :</label>
                <input value={pass} onChange={(e) => setPass(e.target.value)} type="password" placeholder="********" id="password" name="password" />
                <label htmlFor="password">Re-enter your new password :</label>
                <input value={pass} onChange={(e) => setPass(e.target.value)} type="password" placeholder="********" id="password" name="password" />
                <button type="submit">Create Account and Log In</button>
            </form>
            <button className="link-btn" onClick={() => props.onFormSwitch('login')}>Already have an account? Login here.</button>
    </div>
    )
}