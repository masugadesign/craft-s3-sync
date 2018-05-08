<?php
/**
 * S3 Sync plugin for Craft CMS 3.x
 *
 * Create Assets in Craft when a file is uploaded directly to S3
 *
 * @link      https://superbig.co
 * @copyright Copyright (c) 2018 Superbig
 */

namespace superbig\s3sync\utilities;

use superbig\s3sync\S3Sync;
use superbig\s3sync\assetbundles\s3syncutilityutility\S3SyncUtilityUtilityAsset;

use Craft;
use craft\base\Utility;

/**
 * S3 Sync Utility
 *
 * @author    Superbig
 * @package   S3Sync
 * @since     1.0.0
 */
class S3SyncUtility extends Utility
{
    // Static
    // =========================================================================

    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return Craft::t('s3-sync', 'S3SyncUtility');
    }

    /**
     * @inheritdoc
     */
    public static function id(): string
    {
        return 's3sync-s3-sync-utility';
    }

    /**
     * @inheritdoc
     */
    public static function iconPath()
    {
        return Craft::getAlias("@superbig/s3sync/assetbundles/s3syncutilityutility/dist/img/S3SyncUtility-icon.svg");
    }

    /**
     * @inheritdoc
     */
    public static function badgeCount(): int
    {
        return 0;
    }

    /**
     * @inheritdoc
     */
    public static function contentHtml(): string
    {
        Craft::$app->getView()->registerAssetBundle(S3SyncUtilityUtilityAsset::class);

        $someVar = 'Have a nice day!';
        return Craft::$app->getView()->renderTemplate(
            's3-sync/_components/utilities/S3SyncUtility_content',
            [
                'someVar' => $someVar
            ]
        );
    }
}
