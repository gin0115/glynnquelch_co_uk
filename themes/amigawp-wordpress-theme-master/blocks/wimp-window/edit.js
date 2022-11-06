/**
 * WordPress dependencies
 */

import { useBlockProps, InspectorControls, InnerBlocks } from '@wordpress/block-editor';
import { Panel, PanelBody, PanelRow, TextControl } from '@wordpress/components';
const Edit = (props) => {
	const {
		attributes: { title },
		setAttributes,
	} = props;


	const blockProps = useBlockProps();

	/**
	 * Sets the title of the window.
	 * @param {string} newTitle Block title
	 */
	const onChangeTitle = (newTitle) => {
		setAttributes({ title: newTitle });
	};
	return (
		<div>
			<InspectorControls key="setting">
				<Panel header="Wimp Window">
					<PanelBody title="Window Settings">
						<PanelRow>
							<TextControl
								label="Window Title"
								value={title}
								onChange={onChangeTitle}
							/>
						</PanelRow>
					</PanelBody>
				</Panel>
			</InspectorControls>
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
							<InnerBlocks />
						</div>
					</div>
					<span class="resizeIcon">
						<span class="boxOne"></span>
						<span class="boxTwo"></span>
					</span>
				</div>
			</div>
		</div>
	);
};
export default Edit;
