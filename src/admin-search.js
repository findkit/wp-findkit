// @ts-check

/**
 * Override the default WordPress search from
 */
class FindkitAdminSearch {
	publicToken = "";
	settingsURL = "";

	/**
	 * @type {Promise<any> | undefined}
	 */
	uiPromise = undefined;

	version = "";

	/**
	 * @param {{
	 *  publicToken: string,
	 *  version: string,
	 *  settingsURL: string,
	 * }} options
	 */
	constructor(options) {
		this.publicToken = options.publicToken;
		this.version = options.version;
		this.settingsURL = options.settingsURL;
		document.addEventListener("click", (e) => {
			if (e.target instanceof HTMLElement) {
				if (
					e.target.classList.contains("findkit-admin-search") ||
					e.target.closest(".findkit-adminbar-search")
				) {
					e.preventDefault();
					this.bindClick(e.target);
				}
			}
		});

		const sp = new URLSearchParams(window.location.search);
		if (sp.has("findkit_wp_admin_q")) {
			this.getFindkitUI();
		}
	}

	/**
	 *
	 * @param {HTMLElement} element
	 */
	async bindClick(element) {
		const origText = element.innerText;
		element.innerText = "Loading...";

		const ui = await this.getFindkitUI();

		ui.open();

		element.innerText = origText;
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
				Header: (props) => {
					// prettier-ignore
					return html`
						${props.children}
						${this.settingsURL
							? html`
								<a href="${this.settingsURL}"
									class="findkit--wp-admin-link findkit--hit-url findkit--link">
									Open Findkit WordPress Settings
								</a>`
							: null}
					`;
				},
				Hit(props) {
					const wpAdminEditUrl = new URL(window.location.toString());

					wpAdminEditUrl.search = "";

					wpAdminEditUrl.searchParams.set(
						"findkit_edit_redirect",
						props.hit.url,
					);

					// prettier-ignore
					return html`
						${props.children}
						<a href="${wpAdminEditUrl.toString()}"
							class="findkit--wp-admin-link findkit--hit-url findkit--link">
							Edit in WP Admin
						</a>
					`;
				},
			},
		});

		ui.preload();

		return ui;
	}
}
