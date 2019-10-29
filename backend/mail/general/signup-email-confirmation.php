<?php
/**
 * @var string $username
 * @var string $link
 */
?>

<h3>Hi <?= $username ?>,</h3>

<p>
    you have been registered on <?= Yii::$app->urlManager->hostInfo ?>
</p>

<p>
    <a href="<?= $link ?>">Accept this invitation</a>
</p>

<p>
    If you don't want to accept this invitation or think you were mistaken for someone else please let us know!
</p>