<?php
/**
 * S3 Sync plugin for Craft CMS 3.x
 *
 * Create Assets in Craft when a file is uploaded directly to S3
 *
 * @link      https://gomasuga.com
 * @copyright Copyright (c) 2018 Masuga Design
 */

namespace masugadesign\s3sync\assetbundles\s3syncutilityutility;

use Craft;
use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

/**
 * @author    Masuga Design
 * @package   S3Sync
 * @since     1.0.0
 */
class S3SyncUtilityUtilityAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = "@masugadesign/s3sync/assetbundles/s3syncutilityutility/dist";

        $this->depends = [
            CpAsset::class,
        ];

        $this->js = [
            'js/S3SyncUtility.js',
        ];

        $this->css = [
            'css/S3SyncUtility.css',
        ];

        parent::init();
    }
}
