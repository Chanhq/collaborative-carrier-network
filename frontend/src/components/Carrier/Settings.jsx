import React, {useContext, useEffect, useState} from 'react';
import { AuthContext } from '../../lib/context/AuthContext';
import Typography from '@mui/material/Typography';
import {CircularProgress, Slider, Stack} from '@mui/material';
import Box from '@mui/material/Box';
import Button from '@mui/material/Button';
import carrierApi from '../../lib/api/carrier';

function Settings() {
	const {user, authenticated} = useContext(AuthContext);

	const [varCost, setVarCost] = useState(0);
	const [fixedCost, setFixedCost] = useState(0);
	const [varPrice, setVarPrice] = useState(0);
	const [fixedPrice, setFixedPrice] = useState(0);
	const [minRevenue, setMinRevenue] = useState(0);
	const [costModelLoaded, setCostModelLoaded] = useState(false);

	const handleSubmit = () => {
		 const data = {
			 transport_request_minimum_revenue: minRevenue,
			 transport_request_cost_base: fixedCost,
			 transport_request_cost_variable: varCost,
			 transport_request_price_base: fixedPrice,
			 transport_request_price_variable: varPrice,
		};
		 carrierApi.setCostModel(user.token, data);
	};

	const valueText = (value) => {
		return `${value} $`;
	};

	const fetchCostModel = async () => {
		if (user) {
			try {
				const costModel = await carrierApi.getCostModel(user.token);
				if (costModel) {
					setVarCost(costModel.transport_request_cost_variable);
					setFixedCost(costModel.transport_request_cost_base);
					setVarPrice(costModel.transport_request_price_variable);
					setFixedPrice(costModel.transport_request_price_base);
					setMinRevenue(costModel.transport_request_minimum_revenue);
					setCostModelLoaded(true);
				}
			} catch (error) {
				console.error('Error fetching map data:', error);
			}
		}
	};

	useEffect(() => {
		fetchCostModel();
	}, [user]);

	return (
		(authenticated && !user.isAuctioneer) &&
        <>
        	<Typography align="center" variant="h1" gutterBottom>
				Settings
        	</Typography>
        	{
        		costModelLoaded &&
				<div
					style={{
						display: 'flex',
						alignItems: 'center',
						justifyContent: 'center',
					}}
				>
					<Stack spacing={1}>
						<Box sx={{ width: 300 }}>
							<Typography gutterBottom>
								Variable cost per distance:
							</Typography>
							<Slider
								aria-label="Variable cost per distance"
								defaultValue={varCost}
								getAriaValueText={valueText}
								valueLabelDisplay="auto"
								step={1}
								min={1}
								max={10}
								color="primary"
								onChange={(event, value) => {setVarCost(value);}}
							/>
							<Typography gutterBottom>
								Fixed cost per transport request:
							</Typography>
							<Slider
								aria-label="Fixed cost per transport request"
								defaultValue={fixedCost}
								getAriaValueText={valueText}
								valueLabelDisplay="auto"
								step={1}
								min={1}
								max={100}
								onChange={(event, value) => {setFixedCost(value);}}
							/>
							<Typography gutterBottom>
								Variable price per distance:
							</Typography>
							<Slider
								aria-label="Variable price per distance"
								defaultValue={varPrice}
								getAriaValueText={valueText}
								valueLabelDisplay="auto"
								step={1}
								min={1}
								max={10}
								onChange={(event, value) => {setVarPrice(value);}}
							/>
							<Typography gutterBottom>
								Fixed price per transport request:
							</Typography>
							<Slider
								aria-label="Fixed price per transport request"
								defaultValue={fixedPrice}
								getAriaValueText={valueText}
								valueLabelDisplay="auto"
								step={1}
								min={1}
								max={100}
								onChange={(event, value) => {setFixedPrice(value);}}
							/>
							<Typography gutterBottom>
								Minimum revenue per transport request:
							</Typography>
							<Slider
								aria-label="Minimum revenue per transport request"
								defaultValue={minRevenue}
								getAriaValueText={valueText}
								valueLabelDisplay="auto"
								step={1}
								min={1}
								max={100}
								onChange={(event, value) => {setMinRevenue(value);}}
							/>
						</Box>
						<Button variant="contained" onClick={handleSubmit}>Update cost model</Button>
					</Stack>
				</div>
        	}
        	{
        		!costModelLoaded &&
				<div
					style={{
						position: 'absolute', left: '50%', top: '50%',
						transform: 'translate(-50%, -50%)'
					}}
				>
					<CircularProgress />
				</div>
        	}
        </>
	);
}

export default Settings;