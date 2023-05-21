import {useContext} from "react";
import {AuthContext} from "../../lib/context/AuthContext";

function AuctioneerHome() {
    const { user, authenticated } = useContext(AuthContext);

    // TODO: add redirect effect when no auctioneer (maybe guarded routes)
    return(
        (authenticated && user.isAuctioneer) && <><h1>Auctioneer Home works!</h1></>
    );
}

export default AuctioneerHome;