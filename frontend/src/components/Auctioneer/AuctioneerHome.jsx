import {useContext} from "react";
import {AuthContext} from "../../lib/context/AuthContext";

function AuctioneerHome() {
    const { authenticated } = useContext(AuthContext);

    return(
        authenticated && <><h1>Carrier Home works!</h1></>
    );
}

export default AuctioneerHome;