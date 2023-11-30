import { FindkitUI, html, css } from "@findkit/ui";

const FINDKIT_ADMIN_SEARCH: {
	publicToken: string;
	settingsURL: string;
} = (window as any).FINDKIT_ADMIN_SEARCH;

const ui = new FindkitUI({
	publicToken: FINDKIT_ADMIN_SEARCH.publicToken,
	instanceId: "findkit_wp_admin",
	css: css`
		:host {
			--findkit--brand-color: #2271b1;
		}

		a {
			color: var(--findkit--brand-color);
		}

		.findkit--wp-admin-link {
			display: block;
			margin-top: 10px;
			font-weight: 800;
		}
	`,
	slots: {
		Header: (props) => {
			// prettier-ignore
			return html`
                ${props.children}
                ${FINDKIT_ADMIN_SEARCH.settingsURL
                    ? html`
                        <a href="${FINDKIT_ADMIN_SEARCH.settingsURL}"
                            class="findkit--wp-admin-link findkit--hit-url findkit--link">
                            Open Findkit WordPress Settings
                        </a>`
                    : null}
            `;
		},
		Hit(props) {
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

document.addEventListener("click", (e) => {
	if (e.target instanceof HTMLElement) {
		if (
			e.target.classList.contains("findkit-admin-search") ||
			e.target.closest(".findkit-adminbar-search")
		) {
			e.preventDefault();
			ui.open();
		}
	}
});
