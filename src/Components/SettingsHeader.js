const SettingsHeader = ( { icon: Icon, title, subTitle, actions } ) => {
	return (
		<div className="wpces-header-wrapper">
			<div className="settings-header">
				<div className="settings-header-inner">
					<div className="header-text-column">
						<div className="header-title-container">
							{ Icon && (
								<div className="settings-header-icon">
									<Icon />
								</div>
							) }
							<h2>{ title }</h2>
						</div>
						{ subTitle && <p className="header-subtitle">{ subTitle }</p> }
					</div>
					{ actions && <div className="header-actions">{ actions }</div> }
				</div>
			</div>
		</div>
	);
};

export default SettingsHeader;
