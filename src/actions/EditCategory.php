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
use craft\elements\Category;
use craft\elements\db\ElementQueryInterface;

/**
 * Edit Category action to select only one category even if the field has no limit
 * it will overwrite and select only one
 *
 * @author KFFEIN
 * @since 1.0.4
 */
class EditCategory extends ElementAction
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
     * Group of the field
     */
    public $group;

    /*
     * Value of the triggered action
     */
    public $value;

    // Public Methods
    // =========================================================================

    /*
     * Create a dropdown edit class with the field's info, title, handle and options
     */
    public function __construct(string $_title, string $_handle,string $_source){
        // Parse the group info with 
        if(!empty($_source)){
            $_source = explode('group:', $_source);
            $_source = $_source[1];
            $_source = Craft::$app->getCategories()->getGroupById($_source);
        }
        $this->title = $_title;
        $this->handle = $_handle;
        $this->group = $_source;
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
        $parents = Category::find()
        ->group($this->group)
        ->level(1)
        ->limit('null')
        ->all();

        $categories = [];
        foreach ($parents as $parent) {
            $children = $parent->children->all();
            $title = $parent->title;
            $slug = $parent->slug;
            $enabled = $parent->enabled;
            $categories[] = compact('title','slug','enabled','children');
        }
        return Craft::$app->getView()->renderTemplate('craft-bulk-edit/_components/elementactions/Categories/trigger',['label'=>$this->title,'handle'=>$this->handle,'options'=>$categories]);
        
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

        $paramCategory = Category::find()
        ->group($this->group)
        ->slug($this->value)
        ->limit('null');

        /** @var Element[] $elements */
        $elements = $query->all();
        $failCount = 0;

        foreach ($elements as $element) {
            if(empty($paramCategory->one())){
                $failCount++;
                continue;
            }
            // Get category id and add the parent id in the array if it exist.
            $ids = $this->getParentId($paramCategory->one());

            $element->{$this->handle} = $ids;

            if ($elementsService->saveElement($element,false) === false) {
                // Validation error
                $failCount++;
            }
        }

        // Did all of them fail?
        if ($failCount === count($elements)) {
            if (count($elements) === 1) {
                $this->setMessage(Craft::t('app', 'Could not update category due to a validation error.'));
            } else {
                $this->setMessage(Craft::t('app', 'Could not update categories due to validation errors.'));
            }

            return false;
        }

        if ($failCount !== 0) {
            $this->setMessage(Craft::t('app', 'Category updated, with some failures due to validation errors.'));
        } else {
            if (count($elements) === 1) {
                $this->setMessage(Craft::t('app', 'Category updated.'));
            } else {
                $this->setMessage(Craft::t('app', 'Categories updated.'));
            }
        }

        return true;
    }

    private function getParentId($category){
        $ids = [];
        $ids[] = $category->id;
        if($category->getParent()){
            array_merge($ids,$this->getParentId($category->getParent()));
        }
        return $ids;
    }
}
