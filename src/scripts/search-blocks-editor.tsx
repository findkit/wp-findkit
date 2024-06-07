import * as React from "react";
import {
	BlockEditProps,
	registerBlockType,
	createBlock,
} from "@wordpress/blocks";
import {
	InnerBlocks,
	useBlockProps,
	InspectorControls,
} from "@wordpress/block-editor";

import "./search-blocks/view.css";
import "./search-blocks/editor.css";

import {
	Button,
	Panel,
	PanelBody,
	PanelRow,
	SelectControl,
	TextControl,
	TextareaControl,
} from "@wordpress/components";

import searchModalMetadata from "../blocks/search-modal/block.json";
import searchEmbedMetadata from "../blocks/search-embed/block.json";
import searchGroupMetadata from "../blocks/search-group/block.json";
import {
	InferAttributes,
	useBlockEditorStore,
	useInnerBlocks,
	usePostTypes,
	useTaxonomyTerms,
} from "./lib/tsgb";
import { ReactNode } from "react";
import { ColorSlugPickerFromPalette } from "./lib/components";

export interface FindkitFilterAttributes {
	categories?: string;
	tags?: string;
	postTypes?: string;
	domains?: string;
	rawTags?: string;
}

declare const FINDKIT_SEARCH_BLOCK: {
	publicToken?: string;
};

const ALLOWED_BLOCKS = ["core/button", "core/image", "core/search"];

function splitByComma(str: string | undefined): string[] {
	return str?.split(",").filter(Boolean) ?? [];
}

function SearchFilterEditor(props: {
	publicToken: string | undefined;
	initialOpen?: boolean;
	attributes: BlockEditProps<FindkitFilterAttributes>["attributes"];
	setAttributes: BlockEditProps<FindkitFilterAttributes>["setAttributes"];
}) {
	const categories = useTaxonomyTerms("category");
	const tags = useTaxonomyTerms("post_tag");
	const postTypes = usePostTypes().filter(
		(postType) => postType.viewable && postType.supports.editor,
	);
	return (
		<>
			<PanelRow>
				<p>Filter down the search results</p>
			</PanelRow>
			<PanelRow>
				<SelectControl
					multiple
					label="Post types"
					help="Show search results only form the selected post types"
					value={splitByComma(props.attributes.postTypes)}
					onChange={(values) => {
						props.setAttributes({ postTypes: values.join(",") });
					}}
					options={postTypes.map((tax) => ({
						value: tax.slug,
						label: tax.name,
					}))}
				/>
			</PanelRow>
			<PanelRow>
				<SelectControl
					multiple
					label="Categories"
					help="Show search results only from the selected categories"
					value={splitByComma(props.attributes.categories)}
					onChange={(values) => {
						props.setAttributes({ categories: values.join(",") });
					}}
					options={categories.map((tax) => ({
						value: tax.slug,
						label: tax.name,
					}))}
				/>
			</PanelRow>
			<PanelRow>
				<SelectControl
					multiple
					label="WordPress Post Tags"
					help="Show search results only from the selected tags"
					value={splitByComma(props.attributes.tags)}
					onChange={(values) => {
						props.setAttributes({ tags: values.join(",") });
					}}
					options={tags.map((tax) => ({
						value: tax.slug,
						label: tax.name,
					}))}
				/>
			</PanelRow>
			<PanelRow>
				<TextareaControl
					label="Domains"
					help="Limit search results to these domains. One per line."
					value={props.attributes.domains || ""}
					onChange={(values) => {
						props.setAttributes({ domains: values });
					}}
				/>
			</PanelRow>
			<PanelRow>
				<TextareaControl
					label="Findkit Tags"
					help={
						<>
							Limit search results to these these Findkit Tags. One per line.
							{props.publicToken && (
								<>
									{" "}
									See available tags from the{" "}
									<a
										href={`https://hub.findkit.com/p/${props.publicToken}?view=inspect`}
										target="_blank"
									>
										Findkit Hub
									</a>{" "}
									Inspect view.
								</>
							)}
						</>
					}
					value={props.attributes.rawTags || ""}
					onChange={(values) => {
						props.setAttributes({ rawTags: values });
					}}
				/>
			</PanelRow>
		</>
	);
}

function ConfigureSearchBlock(
	props: BlockEditProps<InferAttributes<typeof searchModalMetadata>> & {
		children?: ReactNode;
	},
) {
	return (
		<Panel>
			<PanelBody title="Settings" initialOpen={true}>
				<PanelRow>
					<TextControl
						value={props.attributes.publicToken || ""}
						onChange={(value) => {
							props.setAttributes({
								publicToken: value,
							});
						}}
						label="Findkit Public Token"
						help="Get public token from the Findkit Hub"
					/>
				</PanelRow>
				<PanelRow>
					<TextControl
						value={props.attributes.instanceId || ""}
						onChange={(value) => {
							props.setAttributes({
								instanceId: value,
							});
						}}
						label="FindkitUI Instance ID"
						help={
							<>
								Must be unique for each block on a page. See the{" "}
								<a href="https://findk.it/instanceid">docs</a>.
							</>
						}
					/>
				</PanelRow>
				<PanelRow className="findkit-color-picker">
					<ColorSlugPickerFromPalette
						label="Brand Color"
						help="Select brand color for the search UI"
						value={props.attributes.colorSlug}
						onChange={(value) => {
							props.setAttributes({
								colorSlug: value,
							});
						}}
					/>
				</PanelRow>

				{props.children}
			</PanelBody>
			<PanelBody title="Search Filters" initialOpen={false}>
				<SearchFilterEditor
					publicToken={props.attributes.publicToken}
					initialOpen={false}
					{...props}
				/>
			</PanelBody>
		</Panel>
	);
}

function EditSearchModal(
	props: BlockEditProps<InferAttributes<typeof searchModalMetadata>>,
) {
	const blockProps = useBlockProps();
	const hasInnerBlocks =
		useInnerBlocks(props.clientId).filter(
			(block) => block.name !== "findkit/search-group",
		).length > 0;

	return (
		<div {...blockProps}>
			<InspectorControls>
				<ConfigureSearchBlock {...props} />
			</InspectorControls>
			{!hasInnerBlocks ? (
				<div className="findkit-no-modal-inner-blocks">
					<b>Search Modal</b>
					<p>Add an inner block</p>
				</div>
			) : null}
			<InnerBlocks allowedBlocks={ALLOWED_BLOCKS} />
		</div>
	);
}

function EditSearchEmbed(
	props: BlockEditProps<InferAttributes<typeof searchEmbedMetadata>>,
) {
	const blockProps = useBlockProps();
	const { insertBlock } = useBlockEditorStore();
	const innerBlocks = useInnerBlocks(props.clientId);

	const addExampleBlocks = () => {
		const group1 = createBlock("findkit/search-group", {
			postTypes: "post",
			groupTitle: "Posts",
		});

		const group2 = createBlock("findkit/search-group", {
			postTypes: "page",
			groupTitle: "Pages",
		});

		insertBlock(group2, 0, props.clientId);
		insertBlock(group1, 0, props.clientId);
	};

	return (
		<div {...blockProps}>
			<InspectorControls>
				<ConfigureSearchBlock {...props}>
					<PanelRow>
						<TextControl
							value={props.attributes.inputPlaceholder || ""}
							placeholder="Search..."
							onChange={(value) => {
								props.setAttributes({
									inputPlaceholder: value,
								});
							}}
							label="Input placeholder"
							help="Placeholder text on the search input before user types anything"
						/>
					</PanelRow>
				</ConfigureSearchBlock>
			</InspectorControls>
			<div className="wp-findkit-input-wrap">
				<input
					className="wp-findkit-search-input"
					type="search"
					placeholder={props.attributes.inputPlaceholder || "Search..."}
					disabled
				/>
			</div>

			{innerBlocks.length === 0 ? (
				<div className="findkit-no-embed-inner-blocks">
					<b>Search Results</b>
					<p>Search results will be rendered here on the frontend.</p>
					<p>
						Filter results by using the "Search Filter" options in the block
						inspector. Group the results by adding inner Findkit Group blocks.
					</p>
					<Button variant="secondary" onClick={addExampleBlocks}>
						Add example groups
					</Button>
				</div>
			) : null}

			<InnerBlocks allowedBlocks={["findkit/search-group"]} />
		</div>
	);
}

function EditSearchGroup(
	props: BlockEditProps<InferAttributes<typeof searchGroupMetadata>>,
) {
	const blockProps = useBlockProps();
	const publicTokenFromContext = props.context["findkit/publicToken"] as
		| string
		| undefined;

	return (
		<div {...blockProps}>
			<div className="findkit-search-group-title">
				{props.attributes.groupTitle || "Untitled"}
			</div>
			<div className="findkit-search-group-help">
				Search result group. Edit the search filters in the block inspector.
				Remove all groups to show the results in a single search.
			</div>

			<InspectorControls>
				<Panel>
					<PanelBody title="Settings" initialOpen={true}>
						<PanelRow>
							<TextControl
								label="Group Title"
								value={props.attributes.groupTitle || ""}
								onChange={(value) => {
									props.setAttributes({
										groupTitle: value,
									});
								}}
							/>
						</PanelRow>

						<SearchFilterEditor
							publicToken={
								publicTokenFromContext ?? FINDKIT_SEARCH_BLOCK.publicToken
							}
							{...props}
							initialOpen={true}
						/>
					</PanelBody>
				</Panel>
			</InspectorControls>
		</div>
	);
}

registerBlockType(searchModalMetadata as any, {
	edit: EditSearchModal,
	save() {
		return <InnerBlocks.Content />;
	},
});

registerBlockType(searchGroupMetadata as any, {
	edit: EditSearchGroup,
	save() {
		return <InnerBlocks.Content />;
	},
});

registerBlockType(searchEmbedMetadata as any, {
	edit: EditSearchEmbed,
	save() {
		return <InnerBlocks.Content />;
	},
});
