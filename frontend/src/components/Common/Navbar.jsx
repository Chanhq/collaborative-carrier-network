import {useContext} from "react";
import {AuthContext} from "../../lib/context/AuthContext";
import {SpeedDial, SpeedDialAction, SpeedDialIcon} from "@mui/material";
import LogoutIcon from '@mui/icons-material/Logout';
import authApi from "../../lib/api/auth";
import windowLocationHelper from "../../lib/helper/window-location";


function CarrierHome() {
    const { user, authenticated } = useContext(AuthContext);

    function handleLogoutClick() {
        authApi.logout(user.token).then(() => {
            alert('Logout successful!');
            windowLocationHelper.redirectToAuthPage();
        });
    }

    const actions = [
        { icon: <LogoutIcon />, name: 'Copy', onClick: handleLogoutClick},
    ];


    return(
        authenticated &&
        <SpeedDial
            ariaLabel="SpeedDial basic example"
            sx={{ position: 'absolute', bottom: 16, right: 16 }}
            icon={<SpeedDialIcon />}
        >
            {actions.map((action) => (
                <SpeedDialAction
                    key={action.name}
                    icon={action.icon}
                    tooltipTitle={action.name}
                    onClick={handleLogoutClick}
                />
            ))}
        </SpeedDial>
    );
}

export default CarrierHome;