import React from 'react';
import { BrowserRouter as Router, Routes, Route, Link } from "react-router-dom";
import LoginPage from './components/Login';
import RegisterPage from './components/Register';
import AuctioneerLandingPage from './components/LandingAuctioneer';
import CarrierLandingPage from './components/LandingCarrier';

class Routing extends React.Component {
  render() {
    return (
      <Routes>
        <Route exact path="/" element={<LoginPage/>} />
        <Route path="/register" element={<RegisterPage/>} />
        <Route path="/auctioneer" element={<AuctioneerLandingPage/>} />
        <Route path="/carrier" element={<CarrierLandingPage/>} />
      </Routes>
    );
  }
}

export default Routing;
