const APP_BASE_URL = 'http://localhost:3000';
export default {
	isAlreadyOnPath: (path) => {
		return window.location.href === APP_BASE_URL + path;
	},
	redirectTo(path) {
		window.location.href = APP_BASE_URL + path;
	},
	getPath() {
		return window.location.pathname;
	},
	isOnAuthPage() {
		return this.isAlreadyOnPath('/auth');
	},
	redirectToAuthPage() {
		if (!this.isOnAuthPage()) {
			this.redirectTo('/auth');
		}
	},
	redirectToHomePage(isAuctioneer) {
		if (isAuctioneer === null || isAuctioneer === undefined) {
			this.redirectToAuthPage();
		}
		if (isAuctioneer) {
			this.redirectTo('/auctioneer');
		} else {
			this.redirectTo('/carrier');
		}
	}
};