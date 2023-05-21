import {useContext} from "react";
import {AuthContext} from "../../lib/context/AuthContext";
import {SpeedDial, SpeedDialAction, SpeedDialIcon} from "@mui/material";
import FileCopyIcon from '@mui/icons-material/FileCopyOutlined';
import authApi from "../../lib/api/auth";


function CarrierHome() {
    const { authenticated } = useContext(AuthContext);

    const actions = [
        { icon: <FileCopyIcon />, name: 'Copy', onClick: authApi.logout},
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
                    onClick={action.onClick}
                />
            ))}
        </SpeedDial>
    );
}

export default CarrierHome;