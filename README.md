# Craft Bulk Edit plugin for Craft CMS 3.x

![Screenshot](resources/img/plugin-logo.png)

## Requirements

This plugin requires Craft CMS 3.0.0-beta.23 or later.

## Installation

To install the plugin, follow these instructions.

1. In the Control Panel, go to Settings → Plugins and click the “Install” button for Craft Bulk Edit.

2. Create a craft-bulk-edit.php file in config/ and return an array of sections and field handle e.g. :

		<?php
		return array(
			'addEditFieldAction'=> array(
				'sectionTwo' => array(
					'firstDopdown',
					'firstRadioButton'
				)
			)
		);

## Preview

![Screenshot](resources/img/preview.png)