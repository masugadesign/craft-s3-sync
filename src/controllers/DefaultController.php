<?php
/**
 * S3 Sync plugin for Craft CMS 3.x
 *
 * Create Assets in Craft when a file is uploaded directly to S3
 *
 * @link      https://superbig.co
 * @copyright Copyright (c) 2018 Superbig
 */

namespace superbig\s3sync\controllers;

use superbig\s3sync\S3Sync;

use Craft;
use craft\web\Controller;
use yii\web\HttpException;
use yii\web\JsonParser;

/**
 * @author    Superbig
 * @package   S3Sync
 * @since     1.0.0
 */
class DefaultController extends Controller
{

    // Protected Properties
    // =========================================================================

    /**
     * @var    bool|array Allows anonymous access to this controller's actions.
     *         The actions must be in 'kebab-case'
     * @access protected
     */
    protected $allowAnonymous = ['index'];

    public $enableCsrfValidation = false;

    // Public Methods
    // =========================================================================

    /**
     * @return mixed
     */
    public function actionIndex()
    {
        $this->enableCsrfValidation = false;
        $request                    = Craft::$app->getRequest();
        $parser                     = new JsonParser();
        $body                       = $parser->parse($request->getRawBody(), 'application/json');

        if ($type = $request->getHeaders()->get('x-amz-sns-message-type')) {
            if (in_array($type, ['SubscriptionConfirmation'])) {
                return $this->asJson([
                    'success' => S3Sync::$plugin->s3SyncService->confirmSubscription($body),
                ]);
            }
        }

        if (empty($body['Message'])) {
            throw new HttpException(400);
        }

        $body = $parser->parse($body['Message'], 'application/json');

        if (empty($body['Records'])) {
            throw new HttpException(400);
        }

        return $this->asJson([
            'success' => S3Sync::$plugin->s3SyncService->process($body['Records']),
        ]);
    }
}
