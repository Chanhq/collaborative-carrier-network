import React, { useContext, useEffect, useState } from 'react';
import { AuthContext } from '../../lib/context/AuthContext';
import Navbar from '../Common/Navbar';
import MapVisualizer from '../Map/MapVisualizer';
import Typography from '@mui/material/Typography';
import AirportShuttleIcon from '@mui/icons-material/AirportShuttle';
import Drawer from '@mui/material/Drawer';
import List from '@mui/material/List';
import ListItem from '@mui/material/ListItem';
import Button from '@mui/material/Button'; // Import the Button component
import carrierApi from '../../lib/api/carrier';

function CarrierHome() {
  const { user, authenticated } = useContext(AuthContext);
  const [transportRequests, setTransportRequests] = useState(null);
  const [auctionEvaluationData, setAuctionEvaluationData] = useState(null);
  const fetchData = async () => {
    if (user) {
      try {
        const transportRequestsData = await carrierApi.getTransportRequest(user.token);
        const auctionEvaluationResponse = await carrierApi.getAuctionEvaluationData(user.token);
        if (transportRequestsData) {
          setTransportRequests(transportRequestsData);
        }
        if (auctionEvaluationResponse.status === 204) {
          setAuctionEvaluationData([]);
        } else if (auctionEvaluationResponse.status === 200) {
          setAuctionEvaluationData(auctionEvaluationResponse.data.data);
        }
      } catch (error) {
        console.log(error);
      }
    }
  };

  const reloadPage = () => {
    window.location.reload(false);
  };

  const completeTransportRequests = async () => {
    try {
      await carrierApi.completeTransportRequests(user.token);
      // Refresh the transport requests data
      await fetchData();
    } catch (error) {
      console.log(error);
    }
  };

  useEffect(() => {
    fetchData();
    const id = setInterval(reloadPage, 20000);

    return () => clearInterval(id);
  }, [user]);

  return (
    authenticated &&
    !user.isAuctioneer && (
      <>
        <Navbar> {/* Wrap the CarrierHome component with the Navbar component */}
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
          {transportRequests && (
            <Drawer
              sx={{
                flexShrink: 0,
                '& .MuiDrawer-paper': {
                  width: '200px',
                  boxSizing: 'border-box',
                },
              }}
              variant="permanent"
              anchor="left"
            >
              <Typography align="center" variant="h6">
                Transport Requests
              </Typography>
              <List sx={{ marginLeft: '12px' }}>
                {transportRequests.map((transportRequest) => (
                  <ListItem key={transportRequest.id} disablePadding>
                    Pickup: {transportRequest.origin_node}, Delivery: {transportRequest.destination_node}
                  </ListItem>
                ))}
              </List>
            </Drawer>
          )}
          {auctionEvaluationData && (
            <Drawer
              sx={{
               flexShrink: 0,
                '& .MuiDrawer-paper': {
                  width: '325px',
                  boxSizing: 'border-box',
                },
              }}
              variant="permanent"
              anchor="right"
            >
              <Typography align="center" variant="h6">
                Auction Evaluations
              </Typography
              {auctionEvaluationData.length === 0 && (
                <div style={{ marginLeft: '12px' }}>No auctioned transport requests detected</div>
              )}
              {auctionEvaluationData.length !== 0 && (
                <List sx={{ marginLeft: '12px' }}>
                  {auctionEvaluationData.map((recapData) => (
                    <ListItem key={recapData.auction_id} disablePadding>
                      Auction: {recapData.auction_id}, Revenue Gain: {recapData.revenue_gain}, Price: {recapData.price_to_pay}
                    </ListItem>
                  ))}
                </List>
              )}
              <Button onClick={completeTransportRequests}>Complete Transport Requests</Button> {/* Place the button here */}
            </Drawer>
          )}
        </Navbar>
        <MapVisualizer />
      </>
    )
  );
}

export default CarrierHome;
