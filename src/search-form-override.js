// @ts-check

/**
 * Override the default WordPress search from
 */
class FindkitSearchFormOverride {
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
		document.addEventListener("DOMContentLoaded", () => {
			this.onDomReady();
		});

		const qs = new URLSearchParams(window.location.search);
		if (qs.has("findkit_wp_override_q")) {
			this.getFindkitUI();
		}
	}

	async getFindkitUI() {
		if (this.uiPromise) {
			return this.uiPromise;
		}

		this.uiPromise = this.initFindkitUI();

		return this.uiPromise;
	}

	async initFindkitUI() {
		const { FindkitUI } = await import(
			// @ts-ignore
			`https://cdn.findkit.com/ui/v${this.version}/esm/index.js`
		);

		const ui = new FindkitUI({
			publicToken: this.publicToken,
			instanceId: "findkit_wp_override",
		});

		ui.preload();

		return ui;
	}

	onDomReady() {
		// Find the search form(s)
		const forms = document.querySelectorAll('form[role="search"]');

		for (const form of forms) {
			if (form instanceof HTMLFormElement) {
				this.bindSearchForm(form);
			}
		}
	}

	/**
	 * @param {HTMLFormElement} form
	 */
	bindSearchForm(form) {
		// Start loading the ui assets when the search input is focused
		form.addEventListener("focusin", () => {
			this.getFindkitUI();
		});

		form.addEventListener("submit", (event) => {
			event.preventDefault();
			const formData = new FormData(form);

			let searchTerms = "";

			// Just guess the initial value of searchTerms by picking the first
			// non-empty string. The search form has only one input field
			// usually.
			for (const [_name, value] of formData) {
				if (typeof value === "string" && value) {
					searchTerms = value;
					break;
				}
			}

			this.getFindkitUI().then((ui) => {
				ui.open(searchTerms);
			});
		});
	}
}