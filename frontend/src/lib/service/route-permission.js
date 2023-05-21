import windowLocationHelper from "../helper/window-location";

const routePermissions = new Map([
    // /auth is the only route that is allowed to be null
    // we will have serious security and integrity leaks otherwise
    ['/auth', null],
    ['/carrier', false],
    ['/auctioneer', true],
]);

export default {
    isUserPermittedToAccessRoute: (user) => {
        const path = windowLocationHelper.getPath();
        const hasToBeAuctioneer = routePermissions.get(path);

        if (hasToBeAuctioneer === null) {
            return true;
        }

        return user.isAuctioneer === hasToBeAuctioneer;
    },
}