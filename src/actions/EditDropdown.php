<?php
/**
 * @link https://craftcms.com/
 * @copyright Copyright (c) Pixel & Tonic, Inc.
 * @license https://craftcms.github.io/license/
 */

namespace kffein\craftbulkedit\actions;

use Craft;
use craft\base\Element;
use craft\base\ElementAction;
use craft\elements\db\ElementQueryInterface;

/**
 * Edit Dropdown action to change value of a given dropdown field
 *
 * @author Pixel & Tonic, Inc. <support@pixelandtonic.com>
 * @since 3.0
 */
class EditDropdown extends ElementAction
{
    // Properties
    // =========================================================================

    /**
     * @var string|null The status elements should be set to
     */
    public $status;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function getTriggerLabel(): string
    {
        return Craft::t('craft-status', 'Modify');
    }

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = parent::rules();
        $rules[] = [['status'], 'required'];

        return $rules;
    }

    /**
     * @inheritdoc
     */
    public function getTriggerHtml()
    {
        $status = Craft::$app->fields->getFieldByHandle('comment_status');
        if($status !== null && !empty($status->options)){
            return Craft::$app->getView()->renderTemplate('craft-status/_components/elementactions/CustomStatus/trigger',['options'=>$status->options]);
        }
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
            if (isset($element->comment_status) && $element->comment_status->value == $this->status ) {
                continue;
            }

            $element->comment_status->value = $this->status;

            if ($elementsService->saveElement($element) === false) {
                // Validation error
                $failCount++;
            }
        }

        // Did all of them fail?
        if ($failCount === count($elements)) {
            if (count($elements) === 1) {
                $this->setMessage(Craft::t('app', 'Could not update status due to a validation error.'));
            } else {
                $this->setMessage(Craft::t('app', 'Could not update statuses due to validation errors.'));
            }

            return false;
        }

        if ($failCount !== 0) {
            $this->setMessage(Craft::t('app', 'Status updated, with some failures due to validation errors.'));
        } else {
            if (count($elements) === 1) {
                $this->setMessage(Craft::t('app', 'Status updated.'));
            } else {
                $this->setMessage(Craft::t('app', 'Statuses updated.'));
            }
        }

        return true;
    }
}
