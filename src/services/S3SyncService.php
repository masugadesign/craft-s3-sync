<?php
/**
 * S3 Sync plugin for Craft CMS 3.x
 *
 * Create Assets in Craft when a file is uploaded directly to S3
 *
 * @link      https://superbig.co
 * @copyright Copyright (c) 2018 Superbig
 */

namespace superbig\s3sync\services;

use craft\base\FlysystemVolume;
use craft\elements\Asset;
use craft\errors\AssetDisallowedExtensionException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use superbig\s3sync\models\S3SyncModel;
use superbig\s3sync\records\S3SyncRecord;
use superbig\s3sync\S3Sync;

use Craft;
use craft\base\Component;
use craft\helpers\Assets as AssetsHelper;

/**
 * @author    Superbig
 * @package   S3Sync
 * @since     1.0.0
 */
class S3SyncService extends Component
{
    // Public Methods
    // =========================================================================

    public function process($events = [])
    {
        $events = array_filter($events, function($event) {
            return $event['eventName'] === S3SyncModel::EVENT_CREATED_PUT;
        });

        array_map(function($event) {
            $model = S3SyncModel::create($event);

            $this->processEvent($model);
        }, $events);

        return true;
    }

    public function confirmSubscription($body = [])
    {
        $log        = S3SyncModel::create($body);
        $log->event = S3SyncModel::EVENT_CONFIRMATION;

        try {
            $url = $body['SubscribeURL'];
            $log->setMessage('Confirmed subscription for {arn}', ['arn' => $log->getTopicArn()]);

            $client = new Client();
            $client->get($url);

            $this->_saveRecord($log);

        } catch (RequestException $e) {
            $log
                ->setErrorStatus()
                ->setMessage('{error}', ['error' => $e->getMessage()]);

            $this->_saveRecord($log);

            Craft::error(
                Craft::t(
                    's3-sync',
                    'Failed to confirm url: {error}',
                    [
                        'error' => $e->getMessage(),
                    ]
                ), __METHOD__);


            return false;
        } catch (\Exception $e) {
            $log
                ->setErrorStatus()
                ->setMessage('{error}', ['error' => $e->getMessage()]);

            $this->_saveRecord($log);
        }

        return true;
    }

    public function processEvent(S3SyncModel $model)
    {
        $model->event = S3SyncModel::EVENT_CREATE_ASSET;
        $assets       = Craft::$app->getAssets();
        $filename     = $model->getFilename();
        $volume       = $this->getMatchingVolume($model->getBucketName());

        if (!$volume) {
            return false;
        }

        try {
            $asset                 = Craft::$app->getAssetIndexer()->indexFile($volume, $model->getObjectKey());
            $model->volumeFolderId = $asset->getFolder()->id;
            $model->volumeId       = $volume->id;
            $model->setMessage('Created {path}', ['path' => $model->getObjectKey()]);

            $this->_saveRecord($model);
        } catch (AssetDisallowedExtensionException $e) {
            $model
                ->setErrorStatus()
                ->setMessage($e->getMessage());

            $this->_saveRecord($model);

            return false;
        } catch (\Exception $e) {
            $model
                ->setErrorStatus()
                ->setMessage($e->getMessage());

            $this->_saveRecord($model);

            return false;
        }

        return true;
    }

    /*
     * @return FlysystemVolume|null
     */
    public function getMatchingVolume($bucketName = '')
    {
        $volumes = array_filter(Craft::$app->getVolumes()->getAllVolumes(), function(
            /** @var FlysystemVolume */
            $volume) use ($bucketName) {
            $volumeBucketName = $volume->getSettings()['bucket'] ?? false;

            return $bucketName === $volumeBucketName;
        });

        return end($volumes) ?? null;
    }

    public function getLogs()
    {
        $records = S3SyncRecord::find()
                               ->with(['volume'])
                               ->orderBy('dateCreated DESC')
                               ->all();

        if (!$records) {
            return null;
        }

        // Delete older than 30 days
        return array_map(function($record) {
            return S3SyncModel::createFromRecord($record);
        }, $records);
    }

    private function _saveRecord(S3SyncModel &$model)
    {
        try {
            if ($model->id) {
                $record = S3SyncRecord::findOne($model->id);
            }
            else {
                $record = new S3SyncRecord();
            }

            $record->event          = $model->event;
            $record->status         = $model->status;
            $record->message        = $model->message;
            $record->volumeId       = $model->volumeId;
            $record->volumeFolderId = $model->volumeFolderId;
            $record->siteId         = Craft::$app->getSites()->getPrimarySite()->id;
            $record->data           = serialize($model->data);

            if (!$record->save()) {
                Craft::error(
                    Craft::t('s3-sync', 'An error occured when saving s3-sync log record: {error}',
                        [
                            'error' => print_r($record->getErrors(), true),
                        ]),
                    's3-sync');
            }
            $model->id = $record->id;

            return true;
        } catch (Exception $e) {
            Craft::error(
                Craft::t('s3-sync', 'An error occured when saving s3-sync log record: {error}',
                    [
                        'error' => $e->getMessage(),
                    ]),
                's3-sync');

            return false;
        }
    }
}
