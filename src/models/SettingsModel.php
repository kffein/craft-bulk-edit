<?php
/**
 * Craft Bulk Edit for Craft CMS 3.x
 *
 * @link      https://kffein.com
 * @copyright Copyright (c) 2018 KFFEIN
 */

namespace kffein\craftbulkedit\models;

use kffein\craftbulkedit\SocialWall;

use Craft;
use craft\base\Model;

/**
 * SocialWall Settings Model
 *
 * https://craftcms.com/docs/plugins/models
 *
 * @author    KFFEIN
 * @package   SocialWall
 * @since     1.0.0
 */
class SettingsModel extends Model
{
    // Public Properties
    // =========================================================================

    /**
     * Some field model attribute
     *
     * @var boolean
     */
    public $addEditFieldAction = [];
    // Public Methods
    // =========================================================================

    /**
     * Returns the validation rules for attributes.
     *
     * Validation rules are used by [[validate()]] to check if attribute values are valid.
     * Child classes may override this method to declare different validation rules.
     *
     * More info: http://www.yiiframework.com/doc-2.0/guide-input-validation.html
     *
     * @return array
     */
    public function rules()
    {
        return [
            ['addEditFieldAction', 'array'],
        ];
    }
}
