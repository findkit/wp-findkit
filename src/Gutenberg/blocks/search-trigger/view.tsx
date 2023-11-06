import "./view.css";
import { FindkitUI, FindkitUIOptions } from "@findkit/ui";

const settings: {
	projectId?: string;
} = (window as any).FINDKIT_SEARCH_TRIGGER_VIEW;

const customOptions: Partial<FindkitUIOptions> = (window as any)
	.FINDKIT_UI_OPTIONS;

if (!settings.projectId) {
	throw new Error(
		"Cannot activate Findkit public token is not defined in the settings",
	);
}

const ui = new FindkitUI({
	publicToken: settings.projectId,
});

function bind(el: Element) {
	if (!(el instanceof HTMLElement)) {
		return;
	}

	if (!(el instanceof HTMLButtonElement)) {
		el.role = "button";
		el.tabIndex = 0;
	}

	ui.openFrom(el);
}

function bindAll() {
	document
		.querySelectorAll(
			".wp-block-findkit-search-trigger a, .wp-block-findkit-search-trigger figure",
		)
		.forEach(bind);
}

if (document.readyState === "loading") {
	document.addEventListener("DOMContentLoaded", bindAll);
} else {
	bindAll();
}
