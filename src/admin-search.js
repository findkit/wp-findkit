// @ts-check

/**
 * Override the default WordPress search from
 */
class FindkitAdminSearch {
	publicToken = "";

	/**
	 * @type {Promise<any> | undefined}
	 */
	uiPromise = undefined;

	version = "";

	/**
	 * @param {{
	 *  publicToken: string,
	 *  version: string,
	 * }} options
	 */
	constructor(options) {
		this.publicToken = options.publicToken;
		this.version = options.version;
		document.addEventListener("click", (e) => {
			if (e.target instanceof HTMLButtonElement) {
				this.handleClick({
					target: e.target,
				});
			}
		});

		const sp = new URLSearchParams(window.location.search);
		if (sp.has("findkit_wp_admin_q")) {
			this.getFindkitUI();
		}
	}

	/**
	 *
	 * @param {{target: HTMLButtonElement}} event
	 */
	async handleClick(event) {
		if (!event.target.classList.contains("findkit-admin-search")) {
			return;
		}

		const origText = event.target.innerText;
		event.target.innerText = "Loading...";

		const ui = await this.getFindkitUI();

		ui.open();

		event.target.innerText = origText;
	}

	async getFindkitUI() {
		if (this.uiPromise) {
			return this.uiPromise;
		}

		this.uiPromise = this.initFindkitUI();

		return this.uiPromise;
	}

	async initFindkitUI() {
		const { FindkitUI, html } = await import(
			// @ts-ignore
			`https://cdn.findkit.com/ui/v${this.version}/esm/index.js`
		);

		const ui = new FindkitUI({
			publicToken: this.publicToken,
			instanceId: "findkit_wp_admin",
			css: `
				.findkit--wp-admin-link {
					display: block;
					margin-top: 10px;
					color: blue;
				}
			`,
			slots: {
				Hit(props) {
					const wpAdminEditUrl = new URL(window.location.toString());

					wpAdminEditUrl.search = "";

					wpAdminEditUrl.searchParams.set(
						"findkit_edit_redirect",
						props.hit.url,
					);

					return html`
						${props.children}

						<a
							href="${wpAdminEditUrl.toString()}"
							class="findkit--wp-admin-link findkit--hit-url findkit--link"
							>Edit in WP Admin</a
						>
					`;
				},
			},
		});

		ui.preload();

		return ui;
	}
}
