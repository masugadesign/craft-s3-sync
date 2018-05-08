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
use superbig\s3sync\models\S3SyncModel;
use superbig\s3sync\records\S3SyncRecord;
use superbig\s3sync\S3Sync;

use Craft;
use craft\base\Component;

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
        try {
            array_map(function($event) {
                $model = S3SyncModel::create($event);

                $this->processEvent($model);
            }, $events);
        } catch (\Exception $e) {
            echo $e->getMessage();

            return false;
        }

        return true;
    }

    public function processEvent(S3SyncModel $model)
    {
        $assets         = Craft::$app->getAssets();
        $matchingVolume = $this->getMatchingVolume($model->getBucketName());

        if (!$matchingVolume) {
            return false;
        }

        $folders = $model->getFolderNames();

        if (!$folders) {
            $targetFolder = $assets->getRootFolderByVolumeId($matchingVolume->id);
        }
        else {
            $targetFolder = $assets->ensureFolderByFullPathAndVolume($model->getPath(), $matchingVolume);
        }

        // Check if there is an Asset already
        $asset = $this->getExistingAsset($model);

        if (!$asset) {
            $asset               = new Asset();
            $asset->tempFilePath = $tempPath;
            $asset->filename     = $file['filename'];
            $asset->newFolderId  = $targetFolder->id;
            $asset->volumeId     = $folder->volumeId;
            $asset->setScenario(Asset::SCENARIO_CREATE);
            $asset->avoidFilenameConflicts = true;

            Craft::$app->getElements()->saveElement($asset);
        }


        echo $model->getFilename() . PHP_EOL;
        echo print_r($model->getFolderNames(), true) . PHP_EOL;
    }

    public function getExistingAsset(S3SyncModel $model)
    {

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
