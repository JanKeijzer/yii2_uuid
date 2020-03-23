<?php

namespace Keijzer\Yii2\Uuid\Behaviors;

use yii\behaviors\AttributeBehavior;
use yii\db\BaseActiveRecord;
use Yii;

/**
 * UuidBehavior automatically converts the value of the specified attribute 
 * from binary(16) UUID (in the database) to a readable UUID string 
 * (in the application) and vice versa.
 * It also automatically generates an UUID when creating a new record.
 *
 * @author jan
 */
class UuidBehavior extends AttributeBehavior 
{
    /**
     * @var string the attribute that is a UUID (binary(16))
     *      property in the database, that needs to be converted to and from
     *      a readable UUID string for use in the application. 
     *      Defaults to `uuid`.
     */
    public $uuidAttribute = 'uuid';
    
    /**
     * @var boolean whether a UUID should be generated when inserting a
     *      new record. Defaults to `true`.
     */
    public $generateOnInsert = true;
    
    /**
     *
     * @var boolean whether the uuidAttribute value should always be
     *  converted to and from a readable string while fetching / storing
     *  the model. Defaults to `true`.
     */
    public $autoTransform = true;
    
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        if (empty($this->attributes)) {
            $this->attributes[BaseActiveRecord::EVENT_BEFORE_INSERT] = 
                    $this->uuidAttribute;
            if ($this->autoTransform) {
                $this->attributes[BaseActiveRecord::EVENT_BEFORE_UPDATE] = 
                        $this->uuidAttribute;
                $this->attributes[BaseActiveRecord::EVENT_AFTER_FIND] = 
                        $this->uuidAttribute;
            }
        }
    }

    /**
     * {@inheritdoc}
     *
     */
    protected function getValue($event)
    {
        switch ($event->name) {
            case BaseActiveRecord::EVENT_BEFORE_INSERT:
                if ($this->generateOnInsert) {
                    return Yii::$app->uuid->uuid()->getBytes();
                }
                $uuid = $this->owner->{$this->uuidAttribute};
                return empty($uuid) ? null : Yii::$app->uuid->uuid2bin($uuid);
            case BaseActiveRecord::EVENT_BEFORE_UPDATE:
                $uuid = $this->owner->{$this->uuidAttribute};
                return empty($uuid) ? null : Yii::$app->uuid->uuid2bin($uuid);
            case BaseActiveRecord::EVENT_AFTER_FIND:
                $uuid = $this->owner->{$this->uuidAttribute};
                return empty($uuid) ? null : Yii::$app->uuid->bin2uuid($uuid);
            default:
                return parent::getValue($event);
        }
    }
    
    
}
