import "./search-blocks/view.css";
import {
	Filter,
	FindkitUI,
	FindkitUIOptions,
	GroupDefinition,
	SearchParams,
	css,
} from "@findkit/ui";

declare const FINDKIT_SEARCH_BLOCK: {
	publicToken?: string;
};

interface Attributes {
	publicToken?: string;
	instanceId?: string;
	groupTitle?: string;
	colorSlug?: string;

	categories: string[];
	tags: string[];
	postTypes: string[];
	domains: string[];
	rawTags: string[];
}

/**
 * Split a string by comma, space or new line
 */
function splitByCommaSpaceLine(str: string | undefined) {
	if (!str) {
		return [];
	}

	return str.split(/,\s*|\s+/g).filter(Boolean);
}

function parseAttributes(container: HTMLElement): Attributes {
	const attrs = JSON.parse(container.dataset.attributes || "{}");

	const categories = splitByCommaSpaceLine(attrs.categories);
	const tags = splitByCommaSpaceLine(attrs.tags);
	const postTypes = splitByCommaSpaceLine(attrs.postTypes);
	const domains = splitByCommaSpaceLine(attrs.domains);
	const rawTags = splitByCommaSpaceLine(attrs.rawTags);

	return {
		publicToken: attrs.publicToken,
		instanceId: attrs.instanceId,
		groupTitle: attrs.groupTitle,
		colorSlug: attrs.colorSlug,
		categories,
		tags,
		postTypes,
		domains,
		rawTags,
	};
}

function createFilter(attrs: Attributes): Filter {
	const $and: Filter[] = [];

	if (attrs.categories.length > 0) {
		$and.push({
			tags: {
				$in: attrs.categories.map((c) => `wp_taxonomy/category/${c}`),
			},
		});
	}

	if (attrs.tags.length > 0) {
		$and.push({
			tags: {
				$in: attrs.tags.map((tag) => `wp_taxonomy/post_tag/${tag}`),
			},
		});
	}

	if (attrs.postTypes.length > 0) {
		$and.push({
			tags: {
				$in: attrs.postTypes.map((postType) => `wp_post_type/${postType}`),
			},
		});
	}

	if (attrs.domains.length > 0) {
		$and.push({
			tags: { $in: attrs.domains.map((domain) => `domain/${domain}`) },
		});
	}

	if (attrs.rawTags.length > 0) {
		$and.push({
			tags: { $in: attrs.rawTags },
		});
	}

	return { $and };
}

const USED_IDS = new Set<string>();

function initUI(
	container: Element,
	overrides: Partial<FindkitUIOptions<any>> = {},
) {
	if (!(container instanceof HTMLElement)) {
		throw new Error("Invalid container element");
	}

	const attributes = parseAttributes(container);

	const publicToken =
		attributes.publicToken || FINDKIT_SEARCH_BLOCK.publicToken;

	let instanceId = attributes.instanceId || "fdk";

	if (!publicToken) {
		throw new Error(
			"Cannot activate Findkit Search Modal. Public token is not defined in the block settings",
		);
	}

	let groups: GroupDefinition[] | undefined = Array.from(
		container.querySelectorAll(".wp-block-findkit-search-group"),
	).map((el) => {
		if (!(el instanceof HTMLElement)) {
			throw new Error("Invalid group element");
		}

		const attributes = parseAttributes(el);

		return {
			title: attributes.groupTitle,
			params: {
				filter: createFilter(attributes),
			},
		};
	});

	let params: SearchParams | undefined = undefined;

	if (groups.length === 0) {
		groups = undefined;
		params = {
			filter: createFilter(attributes),
		};
	}

	let i = 0;
	while (true) {
		if (!USED_IDS.has(instanceId)) {
			break;
		}

		i++;
		instanceId = `${instanceId}${i}`;
	}

	USED_IDS.add(instanceId);

	const brandColor = attributes.colorSlug
		? `var(--wp--preset--color--${attributes.colorSlug}, #c828d2)`
		: "#c828d2";

	return new FindkitUI({
		publicToken,
		instanceId,
		params,
		groups: groups as any,
		css: css`
			.findkit--container {
				--findkit--brand-color: ${brandColor};
			}
		`,
		...overrides,
	});
}

for (const container of document.querySelectorAll(
	".wp-block-findkit-search-modal",
)) {
	const ui = initUI(container);
	const img = container.querySelector(".wp-block-image img");
	if (img instanceof HTMLImageElement) {
		img.role = "button";
		img.tabIndex = 0;
		ui.openFrom(img);
	}

	const buttonLink = container.querySelector(".wp-block-button a");
	if (buttonLink instanceof HTMLAnchorElement) {
		ui.openFrom(buttonLink);
	}

	const form = container.querySelector("form.wp-block-search");
	const input = form?.querySelector("input[type=search]");
	if (form instanceof HTMLFormElement && input instanceof HTMLInputElement) {
		// Allow opening empty modal
		input.required = false;

		form.addEventListener("mouseover", () => {
			ui.preload();
		});

		// for keyboard navigation
		input.addEventListener("focus", () => {
			ui.preload();
		});

		form.addEventListener("submit", (e) => {
			e.preventDefault();
			ui.open(input.value);
		});
	}
}

for (const container of document.querySelectorAll(
	".wp-block-findkit-search-embed",
)) {
	const searchContainer =
		container.querySelector(".wp-findkit-container") ?? undefined;
	const input = container.querySelector("input[type=search]");

	const ui = initUI(container, {
		header: false,
		container: searchContainer,
		infiniteScroll: false,
		minTerms: 0,
	});

	if (input instanceof HTMLInputElement) {
		ui.bindInput(input);
	}
}
