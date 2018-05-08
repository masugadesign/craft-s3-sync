<?php
/**
 * S3 Sync plugin for Craft CMS 3.x
 *
 * Create Assets in Craft when a file is uploaded directly to S3
 *
 * @link      https://superbig.co
 * @copyright Copyright (c) 2018 Superbig
 */

namespace superbig\s3sync\records;

use superbig\s3sync\S3Sync;

use Craft;
use craft\db\ActiveRecord;

/**
 * @author    Superbig
 * @package   S3Sync
 * @since     1.0.0
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
}
