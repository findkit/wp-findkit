import { FindkitUI } from "@findkit/ui";

declare const FINDKIT_SEARCH_FORM_OVERRIDE: {
	publicToken: string;
};

const ui = new FindkitUI({
	publicToken: FINDKIT_SEARCH_FORM_OVERRIDE.publicToken,
	instanceId: "fdkwp",
});

function bindSearchForm(form: HTMLFormElement) {
	// Start loading the ui assets when the search input is focused
	form.addEventListener("focusin", () => {
		ui.preload();
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

		ui.open(searchTerms);
	});
}

document.addEventListener("DOMContentLoaded", () => {
	// Find the search form(s)
	const forms = document.querySelectorAll('form[role="search"]');
	if (forms.length === 0) {
		console.warn("[findkit] No search forms found");
	}

	for (const form of forms) {
		if (form instanceof HTMLFormElement) {
			bindSearchForm(form);
		}
	}
});
