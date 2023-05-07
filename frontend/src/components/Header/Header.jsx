/* eslint-disable react/react-in-jsx-scope */
import './Header.css';
import LogoutIcon from '@mui/icons-material/Logout';
import { Box, AppBar, Toolbar, Typography } from '@mui/material';
import Drawer from './Drawer';
import { userLogout } from '../../BusinessDomain/Service/auth.service';

function Header(props) {
  const handleClick = (authState, setAuthState) => {
    userLogout({ authState, setAuthState });
  };

  return (
    <Box sx={{ flexGrow: 1 }}>
      <AppBar sx={{ backgroundColor: '#202020', marginBottom: '12px' }} position="static">
        <Toolbar>
          <Drawer />
          <Typography variant="h6" component="div" sx={{ flexGrow: 1 }}>
            Cooperative Carrier Network
          </Typography>
          {props.authState.isLoggedIn && (
            <div
              onClick={() => {
                handleClick(props.authState, props.setAuthState);
              }}
            >
              <LogoutIcon sx={{ marginRight: -0.5 }} />
            </div>
          )}
        </Toolbar>
      </AppBar>
    </Box>
  );
}

export default Header;
