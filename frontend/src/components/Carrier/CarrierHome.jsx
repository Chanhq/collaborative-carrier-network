import {useContext} from "react";
import {AuthContext} from "../../lib/context/AuthContext";
import Navbar from "../Common/Navbar";
import MapVisualizer from "../Map/MapVisualizer";

function CarrierHome() {
    const { user, authenticated } = useContext(AuthContext);

    return(
        (authenticated && !user.isAuctioneer) &&
        <>
            <h1>Carrier Home works!</h1>
            <Navbar/>
            <MapVisualizer/>
        </>
    );
}

export default CarrierHome;