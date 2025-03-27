import { RichText, useBlockProps } from "@wordpress/block-editor";

const Save = ({ attributes }) => {
	const { align } = attributes;
	const blockProps = useBlockProps.save({
		className: `align-${align}`,
	});

	return <div {...blockProps}>save</div>;
};

export default Save;
