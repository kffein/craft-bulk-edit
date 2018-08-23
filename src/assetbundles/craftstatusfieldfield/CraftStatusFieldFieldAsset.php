<?php
/**
 * Craft Status plugin for Craft CMS 3.x
 *
 * Custom status
 *
 * @link      www.kffein.com
 * @copyright Copyright (c) 2018 KFFEIN
 */

namespace kffein\craftstatus\assetbundles\craftstatusfieldfield;

use Craft;
use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

/**
 * @author    KFFEIN
 * @package   CraftStatus
 * @since     1.0.1
 */
class CraftStatusFieldFieldAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = "@kffein/craftstatus/assetbundles/craftstatusfieldfield/dist";

        $this->depends = [
            CpAsset::class,
        ];

        $this->js = [
            'js/CraftStatusField.js',
        ];

        $this->css = [
            'css/CraftStatusField.css',
        ];

        parent::init();
    }
}
