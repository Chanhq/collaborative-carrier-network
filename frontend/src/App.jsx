import './App.css';
import AuthenticationComponent from './components/Authentication/AuthenticationComponent';
import CarrierHome from './components/Carrier/CarrierHome';
import AuctioneerHome from './components/Auctioneer/AuctioneerHome';
import {BrowserRouter, Route, Routes} from 'react-router-dom';
import Typography from '@mui/material/Typography';
import Box from '@mui/material/Box';
import {red} from '@mui/material/colors';
import AuthProvider from './lib/context/AuthContext';
import React from 'react';
import Settings from './components/Carrier/Settings';

const primary = red[900]; // #f44336

function NotFound() {
	return (
		<Box
			sx={{
				display: 'flex',
				justifyContent: 'center',
				alignItems: 'center',
				minHeight: '100vh',
				backgroundColor: primary,
			}}
		>
			<Typography variant="h1" style={{ color: 'white' }}>
                404 Not Found
			</Typography>
		</Box>
	);
}

function App() {

	return (
		<AuthProvider>
			<BrowserRouter>
				<Routes>
					<Route path="/auth" element={<AuthenticationComponent />}/>
					<Route path="/carrier" element={<CarrierHome />} />
					<Route path="/auctioneer" element={<AuctioneerHome />} />
					<Route path="/settings" element={<Settings />} />
					<Route path="*" element={<NotFound/>} />
				</Routes>
			</BrowserRouter>
		</AuthProvider>
	);
}
export default App;
