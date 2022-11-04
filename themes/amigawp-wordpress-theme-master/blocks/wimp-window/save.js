/**
 * WordPress dependencies
 */
import { useBlockProps, InnerBlocks } from '@wordpress/block-editor';

const Save = (props) => {
	const {
		attributes: { title },
	} = props;
	const blockProps = useBlockProps.save();

	return (
		<div {...blockProps}>
			<div className="wimp-window-container">
				<div className="wimp-window-inner">
					<div className="winBar wimp-window-title">
						<span className="square"></span>
						<span className="windowTitle">
							<h1 className="entry-title">{title}</h1>
						</span>
						<span className="windowLines">
							<hr />
						</span>
						<span className="windowIcons">
							<span className="winIconOne"></span>
							<span className="winSep"></span>
							<span className="winIconTwo"></span>
						</span>
					</div>
					<div className="winBody wimp-window-content spaced">
						<InnerBlocks.Content />
					</div>
				</div>

			</div>
		</div>
	);
};
export default Save;
