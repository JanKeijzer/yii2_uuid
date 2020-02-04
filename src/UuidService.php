<?php

namespace Keijzer\Yii2\Uuid;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidFactory;
use Ramsey\Uuid\Codec\GuidStringCodec;
use Ramsey\Uuid\Codec\StringCodec;
use Ramsey\Uuid\Codec\OrderedTimeCodec;

use yii\base\BaseObject;

/**
 * Services for generating and validating UUIDs.
 *
 * Inspired by the laravel-binary-uuid implementation
 * 
 * @author jan
 * @link https://github.com/thamtech/yii2-uuid/blob/master/src/helpers/UuidHelper.php
 * @link https://github.com/spatie/laravel-binary-uuid/blob/master/src/UuidServiceProvider.php
 */
class UuidService extends BaseObject {
    
    /**
     * The uuid version used when generating UUIDs
     * Can be 1, 3, 4 or 5. Defaults to 1;
     * @var int 
     */
    public $version = 1;
    
    /**
     *
     * @var string
     */
    public $codec = 'OrderedTimeCodec';
    
    
    /**
     * Initializes this component by ensuring the existence of the cache path.
     */
    public function init()
    {
        parent::init();
        $this->optimizeUuids();
    }
    
    /**
     * Generates a UUID.
     *
     * @return Ramsey\Uuid\UuidInterface 
     */
    public function uuid()
    {
        switch ($this->version) {
            case 1: 
                $uuid = Uuid::uuid1();
                break;
            case 3:
                $uuid = Uuid::uuid3($this->ns, $this->name);
                break;
            case 4:
                $uuid = Uuid::uuid4();
                break;
            case 5:
                $uuid = Uuid::uuid5($this->ns, $this->name);
                break;
            default:
                $uuid = Uuid::uuid1();
                break;               
        }
        return $uuid;
    }
    
    
    /**
     * Converts a UUID string into a compact binary string.
     *
     * @param  string $uuid UUID in canonical format, i.e. 25769c6c-d34d-4bfe-ba98-e0ee856f3e7a
     *
     * @return string compact 16-byte binary representation of the UUID.
     */
    public function uuid2bin($uuid)
    {
        if (! Uuid::isValid($uuid)) {
            return $uuid;
        }
        if (! $uuid instanceof Uuid) {
            $uuid = Uuid::fromString($uuid);
        }
        return $uuid->getBytes();
    }
    
    /**
     * Converts a compact 16-byte binary representation of the UUID into
     * a string in canonical format, i.e. 25769c6c-d34d-4bfe-ba98-e0ee856f3e7a.
     *
     * @param  string $binaryUuid compact 16-byte binary representation of the UUID.
     *
     * @return string UUID in canonical format, i.e. 25769c6c-d34d-4bfe-ba98-e0ee856f3e7a
     */
    public function bin2uuid($binaryUuid)
    {
        if (Uuid::isValid($binaryUuid)) {
            return $binaryUuid;
        }
        return Uuid::fromBytes($binaryUuid)->toString();
    }

    private function optimizeUuids()
    {
        $factory = new UuidFactory();
        switch ($this->codec) {
            case 'OrderedTimeCodec':
                $codec = new OrderedTimeCodec($factory->getUuidBuilder());
                break;
            case 'GuidStringCodec':
                $codec = new GuidStringCodec($factory->getUuidBuilder());
                break;
            case 'StringCodec':
                $codec = new StringCodec($factory->getUuidBuilder());
                break;
            default:
                $codec = new OrderedTimeCodec($factory->getUuidBuilder());
                break;
        }
        $factory->setCodec($codec);
        Uuid::setFactory($factory);
    }    

}
