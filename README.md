# S3 Sync plugin for Craft CMS 3.x

Create Assets in Craft when a file is uploaded directly to S3

![Icon](resources/img/icon.png)

## Requirements

This plugin requires Craft CMS 3.0.0-beta.23 or later.

## Installation

To install the plugin, follow these instructions.

1. Open your terminal and go to your Craft project:

        cd /path/to/project

2. Then tell Composer to load the plugin:

        composer require masugadesign/craft-s3-sync

3. In the Control Panel, go to Settings → Plugins and click the “Install” button for S3 Sync.

## S3 Sync Overview

When receiving events from Amazon SNS, this plugin will make sure Assets and folders is added to the Asset index.

## Configuring S3 Sync

No configuration necessary.

## Using S3 Sync

Upload files to S3. That's about it.

Originally coded up by Fred @ [Superbig](https://superbig.co)

_NOTE: You should not install this. We will not support it in any way, shape, or form._
