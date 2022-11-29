// @ts-check

/**
 * @param {{
 *  nonce: string,
 *  initialToken: string | null
 *  endpoint: string,
 * }} params
 */
function init(params) {
	Object.assign(window, {
		async FINDKIT_GET_JWT_TOKEN() {
			if (params.initialToken) {
				const token = params.initialToken;
				params.initialToken = null;
				return token;
			}

			const res = await fetch(params.endpoint, {
				method: "POST",
				headers: { "x-wp-nonce": params.nonce },
			});

			const data = await res.json();

			return data?.token;
		},
	});
}
