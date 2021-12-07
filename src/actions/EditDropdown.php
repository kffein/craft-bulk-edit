<?php
/**
 * Craft Bulk Edit for Craft CMS 3.x
 *
 * @link      https://kffein.com
 * @copyright Copyright (c) 2018 KFFEIN
 */

namespace kffein\craftbulkedit\actions;

use Craft;
use craft\base\Element;
use craft\base\ElementAction;
use craft\elements\db\ElementQueryInterface;

/**
 * Edit Dropdown action to change value of a given dropdown field
 *
 * @author KFFEIN
 * @since 1.0.2
 */
class EditDropdown extends ElementAction
{
    // Properties
    // =========================================================================

    /*
     * Name of the field
     */
    public $title;

    /*
     * Handle of the field
     */
    public $handle;

    /*
     * Array of options from the field
     *
     */
    public $options;

    /*
     * Value of the triggered action
     *
     */
    public $value;

    // Public Methods
    // =========================================================================

    /*
     * Create a dropdown edit class with the field's info, title, handle and options
     */
    public function __construct(string $_title, string $_handle, array $_options)
    {
        $this->title = $_title;
        $this->handle = $_handle;
        $this->options = $_options;
    }
    /**
     * @inheritdoc
     */
    public function getTriggerLabel(): string
    {
        return $this->title;
    }

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = parent::rules();
        $rules[] = [['title'], 'required'];

        return $rules;
    }

    /**
     * @inheritdoc
     */
    public function getTriggerHtml()
    {
        return Craft::$app->getView()->renderTemplate('craft-bulk-edit/_components/elementactions/Dropdown/trigger', ['label'=>$this->title,'handle'=>$this->handle,'options'=>$this->options]);
    }
    /**
     * Performs the action on any elements that match the given criteria.
     *
     * @param ElementQueryInterface $query The element query defining which elements the action should affect.
     * @return bool Whether the action was performed successfully.
     */
    public function performAction(ElementQueryInterface $query): bool
    {
        $elementsService = Craft::$app->getElements();

        /** @var Element[] $elements */
        $elements = $query->all();
        $failCount = 0;

        foreach ($elements as $element) {
            // Skip if there's nothing to change
            if (isset($element->{$this->handle}) && $element->{$this->handle}->value == $this->value) {
                continue;
            }

            $element->{$this->handle}->value = $this->value;
            $element->setFieldValue($this->handle, $this->value);
            if ($elementsService->saveElement($element) === false) {
                // Validation error
                $failCount++;
            }
        }
        
        // Did all of them fail?
        if ($failCount === count($elements)) {
            if (count($elements) === 1) {
                $this->setMessage(Craft::t('app', "Could not update $this->title due to a validation error."));
            } else {
                $this->setMessage(Craft::t('app', "Could not update all $this->title due to validation errors."));
            }

            return false;
        }

        if ($failCount !== 0) {
            $this->setMessage(Craft::t('app', '$this->title updated, with some failures due to validation errors.'));
        } else {
            if (count($elements) === 1) {
                $this->setMessage(Craft::t('app', "$this->title updated."));
            } else {
                $this->setMessage(Craft::t('app', "All $this->title updated."));
            }
        }

        return true;
    }
}
