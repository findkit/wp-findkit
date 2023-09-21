import * as React from "react";
import {
	TemplateArray,
	registerBlockType,
	registerBlockVariation,
} from "@wordpress/blocks";
import { InnerBlocks, useBlockProps } from "@wordpress/block-editor";
import metadata from "./block.json";
import "./editor.css";

const TRIGGER_TEMPLATE: TemplateArray = [
	[
		"core/button",
		{
			text: "Search",
		},
	],
];
const ALLOWED_BLOCKS = ["core/button", "core/image"];

registerBlockType(metadata.name, {
	attributes: {},
	title: metadata.title,
	category: "widgets",
	edit() {
		const blockProps = useBlockProps();
		return (
			<div {...blockProps}>
				<InnerBlocks
					allowedBlocks={ALLOWED_BLOCKS}
					template={TRIGGER_TEMPLATE}
				/>
			</div>
		);
	},
	save() {
		const blockProps = useBlockProps.save();
		return (
			<div {...blockProps}>
				<InnerBlocks.Content />
			</div>
		);
	},
});
