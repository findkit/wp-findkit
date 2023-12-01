import { FindkitUI, html, css } from "@findkit/ui";

declare const FINDKIT_ADMIN_SEARCH: {
	publicToken: string;
	settingsURL: string;
};

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
		Header(props) {
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
			let canEdit = props.hit.tags.includes("wordpress");
			if (!canEdit) {
				const host = window.location.host;
				canEdit = props.hit.tags.some((tag) => {
					return tag.startsWith(`domain${host}`);
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

function isAdminItem(e: { target: unknown }) {
	if (e.target instanceof HTMLElement) {
		if (
			e.target.classList.contains("findkit-admin-search") ||
			e.target.closest(".findkit-adminbar-search")
		) {
			return true;
		}
	}

	return false;
}

document.addEventListener("click", (e) => {
	if (isAdminItem(e)) {
		e.preventDefault();
		ui.open();
	}
});

document.addEventListener("mouseover", (e) => {
	if (isAdminItem(e)) {
		ui.preload();
	}
});
