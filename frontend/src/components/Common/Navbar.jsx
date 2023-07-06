import { useContext } from 'react';
import { AuthContext } from '../../lib/context/AuthContext';
import { SpeedDial, SpeedDialAction, SpeedDialIcon } from '@mui/material';
import PriceChangeIcon from '@mui/icons-material/PriceChange';
import LogoutIcon from '@mui/icons-material/Logout';
import authApi from '../../lib/api/auth';
import windowLocationHelper from '../../lib/helper/window-location';
import StartIcon from '@mui/icons-material/Start';
import React from 'react';
import auctionApi from '../../lib/api/auction';
import carrierApi from '../../lib/api/carrier';
import CheckCircleIcon from '@mui/icons-material/CheckCircle';

function NavBar() {
	const { user, authenticated } = useContext(AuthContext);

	const handleLogoutClick = async () => {
		authApi.logout(user.token).then(() => {
			alert('Logout successful!');
			windowLocationHelper.redirectToAuthPage();
		});
	};

	const handleSettingsClick = () => {
		windowLocationHelper.redirectTo('/settings');
	};

	const startAuction = async () => {
		if (user !== null) {
			auctionApi.startAuction(user.token).then((r) => {
				if (r.response.status === 409) {
					alert('There is already an ongoing auction');
				} else {
					alert('Successfully started auction transport requests selection process.');
				}
			});
			setTimeout(function () {
				window.location.reload(false);
			}, 1500);
		}
	};

	const completeTransportRequests = async () => {
		if (user !== null) {
			carrierApi.completeTransportRequests(user.token).then(() => {
				alert('Completed transport request! New dispatching period started.');
			}).catch((error) => {
				alert(error.response.data.message);
			});
		}
	};

	let actions = [
		{ icon: <LogoutIcon />, name: 'Logout', onClick: handleLogoutClick },
	];

	if (!user.isAuctioneer) {
		actions.push({ icon: <PriceChangeIcon />, name: 'Cost/Price-Model Settings', onClick: handleSettingsClick });
		actions.push({ icon: <CheckCircleIcon />, name: 'Complete Transport Requests', onClick: completeTransportRequests });
	} else {
		actions.push({ icon: <StartIcon />, name: 'Start auction', onClick: startAuction });
	}


	return (
		authenticated &&
    <SpeedDial
    	ariaLabel="SpeedDial basic example"
    	sx={{ zIndex: '10000', position: 'absolute', bottom: 16, right: 16 }}
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

export default NavBar;
