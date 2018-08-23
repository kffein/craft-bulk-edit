<?php
/**
 * Craft Bulk Edit plugin for Craft CMS 3.x
 *
 * Bulk Edit
 *
 * @link      www.kffein.com
 * @copyright Copyright (c) 2018 KFFEIN
 */

namespace kffein\craftbulkedit;

use kffein\craftbulkedit\fields\craftbulkeditField as craftbulkeditFieldField;

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
 * Class craftbulkedit
 *
 * @author    KFFEIN
 * @package   craftbulkedit
 * @since     1.0.1
 *
 */
class craftbulkedit extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * @var craftbulkedit
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
        });

        Craft::info(
            Craft::t(
                'craft-bulk-edit',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );
    }

    // Protected Methods
    // =========================================================================

}
