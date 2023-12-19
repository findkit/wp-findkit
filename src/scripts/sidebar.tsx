import * as React from "react";
import { PluginDocumentSettingPanel } from "@wordpress/edit-post";
import { registerPlugin } from "@wordpress/plugins";

import { TextareaControl, ToggleControl } from "@wordpress/components";
import { useSelect } from "@wordpress/data";
import { useEntityProp } from "@wordpress/core-data";
import { useEffect } from "react";

interface FindkitWPPageMeta {
	_findkit_superwords?: string;
	_findkit_content_no_highlight?: string;
	// real boolean value did not work in post meta for some reason, so we use
	// stringy value
	_findkit_show_in_search?: "yes" | "no";
}

declare const FINDKIT_GUTENBERG_SIDEBAR: {
	postTypes?: string[];
	showSuperwordsEditor?: boolean;
	showContentNoHighlightEditor?: boolean;
};

function usePostMeta<Meta>(): [
	Meta | undefined,
	(newValue: Meta) => void,
	unknown,
] {
	const postType = useSelect(
		(select: any) => select("core/editor").getCurrentPostType(),
		[],
	);

	return useEntityProp("postType", postType, "meta") as any;
}

function usePostType(): {
	type: string;
	supportsCustomFields: boolean;
} {
	const postTypeSlug: string = useSelect(
		(select: any) => select("core/editor").getCurrentPostType(),
		[],
	);

	const postType = useSelect(
		(select: any) => select("core").getPostType(postTypeSlug),
		[postTypeSlug],
	);

	return {
		type: postTypeSlug,
		supportsCustomFields: Boolean(postType?.supports["custom-fields"]),
	};
}

registerPlugin("findkit-sidebar", {
	icon: "smiley",
	render(): any {
		const postType = usePostType();
		const [meta, setMeta] = usePostMeta<FindkitWPPageMeta>();

		const superwords = meta?._findkit_superwords ?? "";
		const contentNoHighlight = meta?._findkit_content_no_highlight ?? "";

		const showInSearch = meta?._findkit_show_in_search;
		const sidebarEnabled = FINDKIT_GUTENBERG_SIDEBAR.postTypes?.includes(
			postType.type,
		);

		const canSavePostMeta =
			postType.supportsCustomFields ||
			// custom fields is only needed for custom post types
			postType.type === "page" ||
			postType.type === "post";

		// use effect to log only once
		useEffect(() => {
			if (sidebarEnabled && !canSavePostMeta) {
				console.warn(
					`[findkit] Findkit sidebar is enabled for post type "${postType.type}" but it does not support custom fields. Add "custom-fieds" to the "supports" array in the post type registration.`,
				);
			}
		}, [canSavePostMeta, sidebarEnabled]);

		if (!sidebarEnabled || !canSavePostMeta) {
			return null;
		}

		return (
			<PluginDocumentSettingPanel
				name="findkit-panel"
				title="Findkit"
				className="findkit-panel"
			>
				{FINDKIT_GUTENBERG_SIDEBAR.showSuperwordsEditor ? (
					<TextareaControl
						label="Superwords"
						value={superwords}
						help={
							<>
								A space-separated list of words which will promote this page to
								the top of the search results when these words are searched for.
							</>
						}
						onChange={(newValue) => {
							setMeta({ ...meta, _findkit_superwords: newValue });
						}}
					/>
				) : null}

				{FINDKIT_GUTENBERG_SIDEBAR.showContentNoHighlightEditor ? (
					<TextareaControl
						label="Content No Highlight"
						value={contentNoHighlight}
						help={
							<>
								Searchable text that will not be highlighted in the search
								results or shown on the actual page.
							</>
						}
						onChange={(newValue) => {
							setMeta({ ...meta, _findkit_content_no_highlight: newValue });
						}}
					/>
				) : null}

				<ToggleControl
					label="Show in Search"
					help={
						<>
							This page is shown in the search results when this is active and
							the page is public. This toggle won't remove the page from the
							public search engines such as Google.
						</>
					}
					checked={showInSearch !== "no"}
					onChange={(value) => {
						if (value) {
							setMeta({ ...meta, _findkit_show_in_search: "yes" });
						} else {
							setMeta({ ...meta, _findkit_show_in_search: "no" });
						}
					}}
				/>
			</PluginDocumentSettingPanel>
		);
	},
});
