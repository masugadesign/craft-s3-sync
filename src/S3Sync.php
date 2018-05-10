<?php
/**
 * S3 Sync plugin for Craft CMS 3.x
 *
 * Create Assets in Craft when a file is uploaded directly to S3
 *
 * @link      https://gomasuga.com
 * @copyright Copyright (c) 2018 Masuga Design
 */

namespace masugadesign\s3sync;

use masugadesign\s3sync\services\S3SyncService as S3SyncServiceService;
use masugadesign\s3sync\utilities\S3SyncUtility as S3SyncUtilityUtility;

use Craft;
use craft\base\Plugin;
use craft\services\Plugins;
use craft\events\PluginEvent;
use craft\web\UrlManager;
use craft\services\Utilities;
use craft\events\RegisterComponentTypesEvent;
use craft\events\RegisterUrlRulesEvent;

use yii\base\Event;

/**
 * Class S3Sync
 *
 * @author    Masuga Design
 * @package   S3Sync
 * @since     1.0.0
 *
 * @property  S3SyncServiceService $s3SyncService
 */
class S3Sync extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * @var S3Sync
     */
    public static $plugin;

    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $schemaVersion = '1.0.0';

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_SITE_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules['s3-sync'] = 's3-sync/default';
            }
        );

        Event::on(
            Utilities::class,
            Utilities::EVENT_REGISTER_UTILITY_TYPES,
            function (RegisterComponentTypesEvent $event) {
                $event->types[] = S3SyncUtilityUtility::class;
            }
        );

        Craft::info(
            Craft::t(
                's3-sync',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );
    }

}
