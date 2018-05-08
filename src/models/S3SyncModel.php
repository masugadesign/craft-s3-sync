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

use craft\base\Volume;
use superbig\s3sync\records\S3SyncRecord;
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
    const EVENT_CREATED     = 'ObjectCreated:*';
    const EVENT_CREATED_PUT = 'ObjectCreated:Put';

    // Public Properties
    // =========================================================================

    /**
     * @var int
     */
    public $id;

    /**
     * @var int
     */
    public $volumeId;

    /**
     * @var int
     */
    public $siteId;

    /**
     * @var int
     */
    public $volumeFolderId;

    /**
     * @var array
     */
    public $data = [];

    /**
     * @var Volume|null
     */
    public $volume;

    /**
     * @var \DateTime
     */
    public $dateCreated;

    // Public Methods
    // =========================================================================


    public static function create($eventData = [])
    {
        $model       = new self();
        $model->data = $eventData;

        return $model;
    }

    public static function createFromRecord(S3SyncRecord $record)
    {
        $model                 = new self();
        $model->volumeId       = $record->volumeId;
        $model->volumeFolderId = $record->volumeFolderId;
        $model->data           = unserialize($record->data);
        $model->volume         = $record->volume;
        $model->dateCreated    = $record->dateCreated;

        return $model;
    }

    public function getVolume()
    {
        return $this->volume;
    }

    public function getFolderId()
    {
        return $this->volumeFolderId;
    }

    public function getEventName()
    {
        return $this->data['eventName'] ?? null;
    }

    public function getBucketName()
    {
        return $this->data['s3']['bucket']['name'] ?? null;
    }

    public function getSize()
    {
        return $this->data['s3']['object']['size'] ?? null;
    }

    public function getDate()
    {
        return $this->data['eventTime'] ?? null;
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
