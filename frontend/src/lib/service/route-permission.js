import windowLocationHelper from '../helper/window-location';

/**
 * permission semantics:
 * entries in array are the route permissions
 * each entry encodes the permissions for one route
 * all routes that are not defined in there can be
 * accessed by all users.
 *
 * One route permission entry can be described like the following
 * ['/path/of/route', {carrier: boolean, auctioneer: boolean}]
 * each of the booleans encodes whether the user has to be of the respective type
 *
 * Example:
 * ['/carrier', {carrier: true, auctioneer: false}],
 * to access the route /carrier the user has to be a carrier and must not be an auctioneer
 *
 * Remark: to make a route accessible by carrier and auctioneer, simply add the route to the
 * router within the AuthProvider in the App.jsx and omit it here in the route permissions map.
 */


const routePermissions = new Map([
	['/carrier', {carrier: true, auctioneer: false}],
	['/auctioneer', {carrier: false, auctioneer: true}],
]);

export default {
	isUserPermittedToAccessRoute: (user) => {
		const path = windowLocationHelper.getPath();
		const permissions = routePermissions.get(path);

		if (!permissions) {
			return true;
		}

		return user.isAuctioneer === permissions.auctioneer && !user.isAuctioneer === permissions.carrier;
	},
};