import {useContext} from "react";
import {AuthContext} from "../../lib/context/AuthContext";
import Navbar from "../Common/Navbar";
import MapVisualizer from "../Map/MapVisualizer";
import React from 'react';

function AuctioneerHome() {
    const { user, authenticated } = useContext(AuthContext);

    return(
        (authenticated && user.isAuctioneer) &&
        <>
            <h1>Auctioneer Home works!</h1>
            <Navbar/>
            <MapVisualizer/>
        </>
    );
}

export default AuctioneerHome;