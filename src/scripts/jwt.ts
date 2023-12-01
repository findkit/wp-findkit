declare const FINDKIT_JWT: {
	nonce: string;
	initialToken: string | null;
	endpoint: string;
};

Object.assign(window, {
	async FINDKIT_GET_JWT_TOKEN() {
		if (FINDKIT_JWT.initialToken) {
			const token = FINDKIT_JWT.initialToken;
			FINDKIT_JWT.initialToken = null;
			return token;
		}

		const res = await fetch(FINDKIT_JWT.endpoint, {
			method: "POST",
			headers: { "x-wp-nonce": FINDKIT_JWT.nonce },
		});

		return res.json();
	},
});
