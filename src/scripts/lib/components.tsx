import * as React from "react";
import { ColorPalette } from "@wordpress/components";
import { useColorPalette } from "./tsgb";

export function ColorSlugPickerFromPalette(props: {
	value: string | undefined;
	label: string;
	help: string;
	onChange: (value: string | undefined) => void;
}) {
	const colors = useColorPalette();
	const selected = colors.find((c) => c.slug === props.value)?.color;

	return (
		<>
			<label className="findkit-label">{props.label}</label>
			<p className="findkit-help">{props.help}</p>
			<ColorPalette
				value={selected}
				colors={colors}
				disableCustomColors
				onChange={(value) => {
					const selected = colors.find((c) => c.color === value);
					props.onChange(selected?.slug);
				}}
			/>
		</>
	);
}
