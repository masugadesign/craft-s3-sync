<?php
/**
 * S3 Sync plugin for Craft CMS 3.x
 *
 * Create Assets in Craft when a file is uploaded directly to S3
 *
 * @link      https://superbig.co
 * @copyright Copyright (c) 2018 Superbig
 */

namespace superbig\s3sync\models;

use superbig\s3sync\S3Sync;

use Craft;
use craft\base\Model;

/**
 * @author    Superbig
 * @package   S3Sync
 * @since     1.0.0
 */
class S3SyncModel extends Model
{
    // Public Properties
    // =========================================================================

    /**
     * @var int
     */
    public $volumeId;

    /**
     * @var array
     */
    public $data = [];

    // Public Methods
    // =========================================================================


    public static function create($eventData = [])
    {
        $model       = new self();
        $model->data = $eventData;

        return $model;
    }

    public function getEventName()
    {
        return $this->data['eventName'] ?? null;
    }

    public function getBucketName()
    {
        return $this->data['s3']['bucket']['name'] ?? null;
    }

    public function getFilename()
    {
        $segments = explode('/', $this->getObjectKey());

        return end($segments);
    }

    public function getPath()
    {
        $folderNames = $this->getFolderNames();

        if (!$folderNames) {
            return null;
        }

        return implode('/', $folderNames);
    }

    public function getFolderNames()
    {
        $segments = explode('/', $this->getObjectKey());

        // If only filename is here, we put it in the base folder
        if (count($segments) === 1) {
            return null;
        }

        array_pop($segments);

        return $segments;
    }

    public function getObjectKey()
    {
        return $this->getObject()['key'] ?? null;
    }

    public function getObject()
    {
        return $this->data['s3']['object'] ?? null;
    }

    public function getDataValue($key = '')
    {

    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [];
    }
}
