<?php
/**
 * S3 Sync plugin for Craft CMS 3.x
 *
 * Create Assets in Craft when a file is uploaded directly to S3
 *
 * @link      https://gomasuga.com
 * @copyright Copyright (c) 2018 Masuga Design
 */

namespace masugadesign\s3sync\utilities;

use masugadesign\s3sync\S3Sync;
use masugadesign\s3sync\assetbundles\s3syncutilityutility\S3SyncUtilityUtilityAsset;

use Craft;
use craft\base\Utility;

/**
 * S3 Sync Utility
 *
 * @author    Masuga Design
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
        return Craft::t('s3-sync', 'S3 Sync Log');
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
        return Craft::getAlias("@masugadesign/s3sync/assetbundles/s3syncutilityutility/dist/img/S3SyncUtility-icon.svg");
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

        $logs = S3Sync::$plugin->s3SyncService->getLogs();

        return Craft::$app->getView()->renderTemplate(
            's3-sync/_components/utilities/S3SyncUtility_content',
            [
                'logs' => $logs,
            ]
        );
    }
}
