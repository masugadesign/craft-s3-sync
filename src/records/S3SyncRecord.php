<?php
/**
 * S3 Sync plugin for Craft CMS 3.x
 *
 * Create Assets in Craft when a file is uploaded directly to S3
 *
 * @link      https://gomasuga.com
 * @copyright Copyright (c) 2018 Masuga Design
 */

namespace masugadesign\s3sync\records;

use craft\records\Volume;
use masugadesign\s3sync\S3Sync;

use Craft;
use craft\db\ActiveRecord;

/**
 * @author    Masuga Design
 * @package   S3Sync
 * @since     1.0.0
 *
 * @property string    $message
 * @property string    $event
 * @property string    $status
 * @property int       $siteId
 * @property int       $volumeId
 * @property int       $volumeFolderId
 * @property array     $data
 * @property \DateTime $dateCreated
 */
class S3SyncRecord extends ActiveRecord
{
    // Public Static Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%s3sync}}';
    }

    public function getVolume()
    {
        return $this->hasOne(Volume::class, ['id' => 'volumeId']);
    }
}
