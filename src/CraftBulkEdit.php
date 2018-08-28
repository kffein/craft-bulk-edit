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

use kffein\craftbulkedit\models\SettingsModel;
use kffein\craftbulkedit\actions\EditDropdown;
use kffein\craftbulkedit\assetbundles\craftbulkedit\CraftBulkEditAsset;

use Craft;
use craft\base\Element;
use craft\base\Plugin;
use craft\elements\Entry;
use craft\services\Fields;
use craft\services\Plugins;
use craft\services\Elements as SeviceElements;
use craft\events\PluginEvent;
use craft\events\ElementActionEvent;
use craft\events\RegisterComponentTypesEvent;
use craft\events\RegisterElementActionsEvent;
use craft\web\View;

use yii\base\Event;

/**
 * Class CraftBulkEdit
 *
 * @author    KFFEIN
 * @package   craftbulkedit
 * @since     1.0.1
 *
 */
class CraftBulkEdit extends Plugin
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
    public $schemaVersion = '1.0.2';

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;
        
        Event::on(Entry::class, Element::EVENT_REGISTER_ACTIONS, function(RegisterElementActionsEvent $event){
            Craft::$app->getView()->registerAssetBundle(CraftBulkEditAsset::class);
            Craft::$app->getView()->registerJs("new Craft.BulkEditPlugin()");

            // Get settings from config/
            $settings = $this->getSettings();

            // Loop through all first level of the settings which should be section's handle.
            foreach ($settings->addEditFieldAction as $sectionHandle => $fields) {
                // Get section by the handle from craft section's service
                $section = Craft::$app->sections->getSectionByHandle($sectionHandle);

                // Add the element list action only if it's a section and it's a valid existant section
                if(!empty($section) && strpos($event->source,'section')!==false && strpos($event->source, $section->id) !==false ){

                    // Loop through the settings fields list associated to the section
                    foreach ($fields as $fieldHandle) {

                        // Get the field object
                        $field = Craft::$app->fields->getFieldByHandle($fieldHandle);
                        // Get the field class
                        $type = get_class($field);

                        // Add an action  by passing argument to his constructor based on his type
                        switch ($type) {
                            case 'craft\fields\Dropdown':
                                $event->actions[] = new EditDropdown($field->name,$field->handle,$field->options);
                                break;
                            default:
                                break;
                        }
                    }
                }
            }
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
    protected function createSettingsModel()
    {
        return new SettingsModel();
    }
}
