<?php
/**
 * S3 Sync plugin for Craft CMS 3.x
 *
 * Create Assets in Craft when a file is uploaded directly to S3
 *
 * @link      https://superbig.co
 * @copyright Copyright (c) 2018 Superbig
 */

namespace superbig\s3sync\migrations;

use superbig\s3sync\S3Sync;

use Craft;
use craft\config\DbConfig;
use craft\db\Migration;

/**
 * @author    Superbig
 * @package   S3Sync
 * @since     1.0.0
 */
class Install extends Migration
{
    // Private Properties
    // =========================================================================

    private $_tableName = '{{%s3sync}}';

    // Public Properties
    // =========================================================================

    /**
     * @var string The database driver to use
     */
    public $driver;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->driver = Craft::$app->getConfig()->getDb()->driver;
        if ($this->createTables()) {
            $this->createIndexes();
            $this->addForeignKeys();
            // Refresh the db schema caches
            Craft::$app->db->schema->refresh();
            $this->insertDefaultData();
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->driver = Craft::$app->getConfig()->getDb()->driver;
        $this->removeTables();

        return true;
    }

    // Protected Methods
    // =========================================================================

    /**
     * @return bool
     */
    protected function createTables()
    {
        $tablesCreated = false;

        $tableSchema = Craft::$app->db->schema->getTableSchema($this->_tableName);
        if ($tableSchema === null) {
            $tablesCreated = true;
            $this->createTable(
                $this->_tableName,
                [
                    'id'             => $this->primaryKey(),
                    'dateCreated'    => $this->dateTime()->notNull(),
                    'dateUpdated'    => $this->dateTime()->notNull(),
                    'uid'            => $this->uid(),
                    'siteId'         => $this->integer()->notNull(),
                    'volumeId'       => $this->integer()->notNull(),
                    'volumeFolderId' => $this->integer()->notNull(),
                    'data'           => $this->text()->null()->defaultValue(null),
                ]
            );
        }

        return $tablesCreated;
    }

    /**
     * @return void
     */
    protected function createIndexes()
    {
        $this->createIndex(
            $this->db->getIndexName(
                $this->_tableName,
                'volumeId',
                false
            ),
            $this->_tableName,
            'volumeId',
            false
        );

        // Additional commands depending on the db driver
        switch ($this->driver) {
            case DbConfig::DRIVER_MYSQL:
                break;
            case DbConfig::DRIVER_PGSQL:
                break;
        }
    }

    /**
     * @return void
     */
    protected function addForeignKeys()
    {
        $this->addForeignKey(
            $this->db->getForeignKeyName($this->_tableName, 'siteId'),
            $this->_tableName,
            'siteId',
            '{{%sites}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            $this->db->getForeignKeyName($this->_tableName, 'volumeId'),
            $this->_tableName,
            'volumeId',
            '{{%volumes}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            $this->db->getForeignKeyName($this->_tableName, 'volumeFolderId'),
            $this->_tableName,
            'volumeFolderId',
            '{{%volumefolders}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * @return void
     */
    protected function insertDefaultData()
    {
    }

    /**
     * @return void
     */
    protected function removeTables()
    {
        $this->dropTableIfExists($this->_tableName);
    }
}
