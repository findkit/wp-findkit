import { FindkitUI, html, css } from "@findkit/ui";

declare const FINDKIT_ADMIN_SEARCH: {
	publicToken: string;
	settingsURL: string;
	showSettingsLink?: boolean;
};

function observeSize(ui: FindkitUI, selectors: Record<string, string>) {
	ui.on("open", (event) => {
		for (const [name, selector] of Object.entries(selectors)) {
			if (event.container instanceof HTMLElement) {
				event.container.style.setProperty(`--${name}-height`, `0px`);
				event.container.style.setProperty(`--${name}-width`, `0px`);
			}

			const el = document.querySelector(selector);
			if (!el) {
				continue;
			}

			const observer = new ResizeObserver((entries) => {
				const height = entries[0]?.borderBoxSize[0]?.blockSize ?? 0;
				const width = entries[0]?.borderBoxSize[0]?.inlineSize ?? 0;

				if (event.container instanceof HTMLElement) {
					event.container.style.setProperty(`--${name}-height`, `${height}px`);
					event.container.style.setProperty(`--${name}-width`, `${width}px`);
				}
			});

			observer.observe(el);
			ui.once("close", () => {
				observer.disconnect();
			});
		}
	});
}

const ui = new FindkitUI({
	publicToken: FINDKIT_ADMIN_SEARCH.publicToken,
	instanceId: "admsearch",
	minTerms: 0,
	inert: "#wpbody",
	closeOnOutsideClick: true,
	css: css`
		.findkit--container {
			--findkit--brand-color: #2271b1;
		}

		.findkit--backdrop {
			/* Under the admin menu items but above the media gallery filter ui */
			z-index: 101;
		}

		.findkit--modal-container {
			left: var(--admin-menu-width);
			top: var(--admin-bar-height);
		}

		.findkit--magnifying-glass-lightning {
			visibility: visible;
		}

		a {
			color: var(--findkit--brand-color);
		}

		.findkit--em {
			color: white;
		}

		.findkit--wp-admin-link {
			display: block;
			margin-top: 10px;
			font-weight: 800;
		}
	`,
	slots: {
		Header(props) {
			// prettier-ignore
			return html`
                ${props.children}
                ${FINDKIT_ADMIN_SEARCH.showSettingsLink
                    ? html`
                        <a href="${FINDKIT_ADMIN_SEARCH.settingsURL}"
                            class="findkit--wp-admin-link findkit--hit-url findkit--link">
                            Open Findkit WordPress Settings
                        </a>`
                    : null}
            `;
		},
		Hit(props) {
			let canEdit = props.hit.tags.includes("wordpress");
			if (!canEdit) {
				const host = window.location.host;
				canEdit = props.hit.tags.some((tag) => {
					return tag.startsWith(`domain/${host}`);
				});
			}

			if (!canEdit) {
				return props.children;
			}

			const wpAdminEditUrl = new URL(window.location.toString());

			wpAdminEditUrl.search = "";
			wpAdminEditUrl.searchParams.set("findkit_edit_redirect", props.hit.url);

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

ui.openFrom("#wp-admin-bar-findkit-adminbar a");

// on /wp-admin/options-general.php?page=findkit_settings
const settingPageButton = document.querySelector("button.findkit-admin-search");
if (settingPageButton instanceof HTMLButtonElement) {
	ui.openFrom("button.findkit-admin-search");
}

observeSize(ui, {
	"admin-menu": "#adminmenu",
	"admin-bar": "#wpadminbar",
});

ui.on("fetch", (e) => {
	if (e.terms.trim() === "") {
		e.transientUpdateParams({
			sort: {
				modified: {
					$order: "desc",
				},
			},
		});
	}
});

ui.on("loading", () => {
	const el = document.querySelector(".findkit-adminbar-search a");
	if (el instanceof HTMLElement) {
		el.dataset.origContent = el.innerHTML;
		el.innerHTML = "Loading...";
	}
});

ui.on("loading-done", () => {
	const el = document.querySelector(".findkit-adminbar-search a");
	if (el instanceof HTMLElement && el.dataset.origContent) {
		el.innerHTML = el.dataset.origContent;
		delete el.dataset.origContent;
	}
});
