import React, { useContext } from 'react';
import { AuthContext } from '../../lib/context/AuthContext';
import Navbar from '../Common/Navbar';
import MapVisualizer from '../Map/MapVisualizer';
import Typography from '@mui/material/Typography';
import AirportShuttleIcon from '@mui/icons-material/AirportShuttle';
import Drawer from '@mui/material/Drawer';
import Toolbar from '@mui/material/Toolbar';
import List from '@mui/material/List';
import Divider from '@mui/material/Divider';
import ListItem from '@mui/material/ListItem';
import ListItemButton from '@mui/material/ListItemButton';
import ListItemIcon from '@mui/material/ListItemIcon';
import ListItemText from '@mui/material/ListItemText';
import InboxIcon from '@mui/icons-material/MoveToInbox';
import MailIcon from '@mui/icons-material/Mail';


function CarrierHome() {
	const {user, authenticated} = useContext(AuthContext);

	return (
		(authenticated && !user.isAuctioneer) &&
        <>
        	<Typography align="center" variant="h1" gutterBottom>
				Coop Carrier Network -
        		<AirportShuttleIcon
        			style={{
        				width: '50px',
        				height: '50px',
        				margin: '0 0 0 25px',
        			}}
        		></AirportShuttleIcon>
        	</Typography>
        	<Drawer
        		sx={{
        			width: '200px',
        			flexShrink: 0,
        			'& .MuiDrawer-paper': {
        				width: '200px',
        				boxSizing: 'border-box',
        			},
        		}}
        		variant="permanent"
        		anchor="left"
        	>
        		<Toolbar />
        		<Divider />
        		<List>
        			{['Inbox', 'Starred', 'Send email', 'Drafts'].map((text, index) => (
        				<ListItem key={text} disablePadding>
        					<ListItemButton>
        						<ListItemIcon>
        							{index % 2 === 0 ? <InboxIcon /> : <MailIcon />}
        						</ListItemIcon>
        						<ListItemText primary={text} />
        					</ListItemButton>
        				</ListItem>
        			))}
        		</List>
        	</Drawer>
        	<Navbar/>
        	<MapVisualizer/>
        </>
	);
}

export default CarrierHome;