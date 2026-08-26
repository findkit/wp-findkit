import { useSelect, useDispatch } from "@wordpress/data";
import {
	store as blockEditorStore,
	useSettings,
} from "@wordpress/block-editor";
import { Block } from "@wordpress/blocks";

interface BlockEditorDispatch {
	insertBlock(block: Block, index: number, rootClientId?: string): void;
}

export function useBlockEditorStore(): BlockEditorDispatch {
	return useDispatch(blockEditorStore);
}

export type InferAttributes<T extends { attributes: {} }> = {
	[P in keyof T["attributes"]]?: string;
};

interface TaxonomyTerm {
	id: number;
	name: string;
	slug: string;
	count: number;
	taxonomy: string;
}

interface PostType {
	name: string;
	slug: string;
	supports: {
		editor: boolean;
	};
	viewable: boolean;
}

interface PaletteColor {
	name: string;
	color: string;
	slug: string;
}

export function useColorPalette(): PaletteColor[] {
	const [palette] = useSettings("color.palette");
	return palette ?? [];
}

export function useTaxonomyTerms(termName: string): TaxonomyTerm[] {
	return (
		useSelect(
			(select: any) => select("core").getEntityRecords("taxonomy", termName),
			[termName],
		) ?? []
	);
}

export function usePostTypes(): PostType[] {
	return useSelect((select: any) => select("core").getPostTypes(), []) ?? [];
}

interface InnerBlock<A = any> {
	attributes: A;
	name: string;
	isValid: boolean;
	clientId: string;
}

export function useInnerBlocks(clientId: string): InnerBlock[] {
	return useSelect(
		(select: any) => select("core/block-editor").getBlock(clientId).innerBlocks,
		[clientId],
	);
}
