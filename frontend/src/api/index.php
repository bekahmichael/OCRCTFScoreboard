<?php
// Composer
require(__DIR__ . '/../../backend/vendor/autoload.php');

// Environment
require(__DIR__ . '/../../backend/env.php');

// Yii
require(__DIR__ . '/../../backend/vendor/yiisoft/yii2/Yii.php');

$config = require(__DIR__ . '/../../backend/config/web.php');

(new yii\web\Application($config))->run();
