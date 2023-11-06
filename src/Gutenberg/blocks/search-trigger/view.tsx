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
	...customOptions,
});

function bind(el: Element | null) {
	if (!(el instanceof HTMLElement)) {
		return;
	}

	if (
		!(el instanceof HTMLButtonElement) &&
		!(el instanceof HTMLAnchorElement)
	) {
		el.role = "button";
		el.tabIndex = 0;
	}

	ui.openFrom(el);
}

function bindAll() {
	const triggers = Array.from(
		document.querySelectorAll(".wp-block-findkit-search-trigger"),
	);

	for (const trigger of triggers) {
		bind(trigger.firstElementChild);
	}
}

if (document.readyState === "loading") {
	document.addEventListener("DOMContentLoaded", bindAll);
} else {
	bindAll();
}
