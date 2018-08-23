<?php
/**
 * Craft Status plugin for Craft CMS 3.x
 *
 * Custom status
 *
 * @link      www.kffein.com
 * @copyright Copyright (c) 2018 KFFEIN
 */

namespace kffein\craftstatus;

use kffein\craftstatus\actions\CustomStatus;
use kffein\craftstatus\fields\CraftStatusField as CraftStatusFieldField;

use Craft;
use craft\base\Element;
use craft\base\Plugin;
use craft\elements\Entry;
use craft\services\Plugins;
use craft\events\PluginEvent;
use craft\services\Fields;
use craft\events\RegisterComponentTypesEvent;
use craft\events\RegisterElementActionsEvent;

use yii\base\Event;

/**
 * Class CraftStatus
 *
 * @author    KFFEIN
 * @package   CraftStatus
 * @since     1.0.1
 *
 */
class CraftStatus extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * @var CraftStatus
     */
    public static $plugin;

    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $schemaVersion = '1.0.1';

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        Event::on(Entry::class, Element::EVENT_REGISTER_ACTIONS, function(RegisterElementActionsEvent $event) {
            $settings = $this->getSettings();
            var_dump($settings);die;
            $event->actions[] = new CustomStatus();
        });

        Craft::info(
            Craft::t(
                'craft-status',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );
    }

    // Protected Methods
    // =========================================================================

}
