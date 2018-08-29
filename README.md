# Craft Bulk Edit plugin for Craft CMS 3.x

Bulk Edit

![Screenshot](resources/img/plugin-logo.png)

## Requirements

This plugin requires Craft CMS 3.0.0-beta.23 or later.

## Installation

To install the plugin, follow these instructions.

1. Open your terminal and go to your Craft project:

        cd /path/to/project

2. Then tell Composer to load the plugin:

        composer require kffein/craft-bulk-edit

3. In the Control Panel, go to Settings → Plugins and click the “Install” button for Craft Bulk Edit.

4. Create a craft-bulk-edit.php file in config/ and return an array of sections and field handle e.g. :

		<?php
		return array(
			'addEditFieldAction'=> array(
				'firstSection' => (
					'radioButtonField',
					'DropdownField'
				)
			)
		);

## Craft Bulk Edit Overview