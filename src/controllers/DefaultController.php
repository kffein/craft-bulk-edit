<?php
/**
 * Craft Bulk Edit for Craft CMS 3.x
 *
 * @link      https://kffein.com
 * @copyright Copyright (c) 2018 KFFEIN
 */

namespace kffein\craftbulkedit\controllers;

use Craft;
use craft\elements\db\ElementQueryInterface;
use craft\web\Controller;
use craft\helpers\ElementHelper;

/**
 * Default controller which capture bulk edit action
 *
 * @author KFFEIN
 * @since 1.0.2
 */
class DefaultController extends Controller
{
    // Properties
    // =========================================================================
    private $_elementType;
    private $_context;
    private $_sourceKey;
    private $_source;
    /**
     * @var    bool|array Allows anonymous access to this controller's actions.
     *         The actions must be in 'kebab-case'
     * @access protected
     */
    protected $allowAnonymous = ['perform-edit-action'];

    // Public Methods
    // =========================================================================
    public function init(){
        parent::init();

        $this->_elementType = Craft::$app->getRequest()->getRequiredParam('elementType');
        $this->_context = Craft::$app->getRequest()->getParam('context');
        $this->_sourceKey = Craft::$app->getRequest()->getParam('source');
        $this->_source = ElementHelper::findSource($this->_elementType, $this->_sourceKey, $this->_context);
    }

    public function actionPerformEditAction(){
        $this->requirePostRequest();

        $requestService = Craft::$app->getRequest();
        $elementsService = Craft::$app->getElements();

        $actionHandle = $requestService->getRequiredBodyParam('actionHandle');
        $elementIds = $requestService->getRequiredBodyParam('elementIds');

        // Find that action from the list of available actions for the source
        if (!empty($this->_listOfAvailableAction())) {
            /** @var ElementAction $availableAction */
            foreach ($this->_listOfAvailableAction() as $availableAction) {
                // Select the action form the handle recieve by the trigger
                if (isset($availableAction->handle) && $availableAction->handle == $actionHandle ) {
                    $action = $availableAction;
                    break;
                }
            }
        }

        if (!isset($action)) {
            throw new BadRequestHttpException('Element action is not supported by the element type');
        }

        // Check for any params in the post data
        foreach ($action->settingsAttributes() as $paramName) {
            $paramValue = $requestService->getBodyParam($paramName);
            
            if ($paramValue !== null) {
                $action->$paramName = $paramValue;
            }
        }

        // Make sure the action validates
        if (!$action->validate()) {
            throw new BadRequestHttpException('Element action params did not validate');
        }

        // Perform the action
        /** @var ElementQuery $actionCriteria */

        $actionCriteria = clone $this->_elementQuery();
        $actionCriteria->offset = 0;
        $actionCriteria->limit = null;
        $actionCriteria->orderBy = null;
        $actionCriteria->positionedAfter = null;
        $actionCriteria->positionedBefore = null;
        $actionCriteria->id = $elementIds;
        
        $success = $action->performAction($actionCriteria);
        $message = $action->getMessage();

        if (!$success) {
            $success = false;
            $message = $event->message;
        }

        // Respond
        $responseData = [
            'success' => $success,
            'message' => $message,
        ];

        if ($success) {
            // Send a new set of elements
            $elementResponseData = Craft::$app->runAction('element-indexes/get-elements');
            $responseData = array_merge($responseData, $elementResponseData->data);
        }

        return $this->asJson($responseData);
    }

    /*
     * Get by source and element type all the actions class registered
     */
    private function _listOfAvailableAction(){
        if (Craft::$app->getRequest()->isMobileBrowser()) {
            return null;
        }

        /** @var string|ElementInterface $elementType */
        $source = Craft::$app->getRequest()->getParam('source');
        $actions = $this->_elementType::actions($source);
        return array_values($actions);
    }

    /*
     * @inheritDoc
     */
    private function _elementQuery(): ElementQueryInterface
    {
        /** @var string|ElementInterface $elementType */
        $elementType = $this->_elementType;
        $query = $elementType::find();

        $request = Craft::$app->getRequest();

        // Does the source specify any criteria attributes?
        if (isset($this->_source['criteria'])) {
            Craft::configure($query, $this->_source['criteria']);
        }

        // Override with the request's params
        if ($criteria = $request->getBodyParam('criteria')) {
            Craft::configure($query, $criteria);
        }

        // Exclude descendants of the collapsed element IDs
        $collapsedElementIds = $request->getParam('collapsedElementIds');

        if ($collapsedElementIds) {
            $descendantQuery = (clone $query)
                ->offset(null)
                ->limit(null)
                ->orderBy(null)
                ->positionedAfter(null)
                ->positionedBefore(null)
                ->anyStatus();

            // Get the actual elements
            /** @var Element[] $collapsedElements */
            $collapsedElements = (clone $descendantQuery)
                ->id($collapsedElementIds)
                ->orderBy(['lft' => SORT_ASC])
                ->all();

            if (!empty($collapsedElements)) {
                $descendantIds = [];

                foreach ($collapsedElements as $element) {
                    // Make sure we haven't already excluded this one, because its ancestor is collapsed as well
                    if (in_array($element->id, $descendantIds, false)) {
                        continue;
                    }

                    $elementDescendantIds = (clone $descendantQuery)
                        ->descendantOf($element)
                        ->ids();

                    $descendantIds = array_merge($descendantIds, $elementDescendantIds);
                }

                if (!empty($descendantIds)) {
                    $query->andWhere(['not', ['elements.id' => $descendantIds]]);
                }
            }
        }

        return $query;
    }
}

