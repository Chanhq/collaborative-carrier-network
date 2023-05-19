import {useContext} from "react";
import {AuthContext} from "../../lib/context/AuthContext";

function CarrierHome() {
    const { user, authenticated } = useContext(AuthContext);

    // TODO: add redirect when no carrier (maybe guarded routes)

    return(
        (authenticated && !user.isAuctioneer)&& <><h1>Carrier Home works!</h1></>
    );
}

export default CarrierHome;