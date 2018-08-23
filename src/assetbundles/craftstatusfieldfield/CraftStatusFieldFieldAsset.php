<?php
/**
 * Craft Bulk Edit plugin for Craft CMS 3.x
 *
 * Craft Bulk Edit
 *
 * @link      www.kffein.com
 * @copyright Copyright (c) 2018 KFFEIN
 */

namespace kffein\craftbulkedit\assetbundles\craftbulkeditfieldfield;

use Craft;
use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

/**
 * @author    KFFEIN
 * @package   craftbulkedit
 * @since     1.0.1
 */
class craftbulkeditFieldFieldAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = "@kffein/craftbulkedit/assetbundles/craftbulkeditfieldfield/dist";

        $this->depends = [
            CpAsset::class,
        ];

        $this->js = [
            'js/craftbulkeditField.js',
        ];

        $this->css = [
            'css/craftbulkeditField.css',
        ];

        parent::init();
    }
}
