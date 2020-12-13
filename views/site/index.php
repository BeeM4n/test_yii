<?php

/**
 * @var $this yii\web\View
 * @var $pagination Pagination
 * @var $rewards array
 * @var $currentUser app\models\User
 */

use yii\data\Pagination;
use yii\helpers\Url;
use yii\widgets\LinkPager;

$this->title = 'Get Your prize now';
?>
<div class="site-index">

    <div class="jumbotron">
        <?php if (Yii::$app->user->isGuest): ?>
            <h1>Hello guest</h1>
        <?php else: ?>
            <h1>Hello <?php echo $currentUser->username ?></h1>

            <?php if (count($rewards)): ?>
                <ul>
                    <?php foreach ($rewards as $reward): ?>
                        <li>
                            <?= \yii\helpers\StringHelper::buildRewardString($reward) ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <p><?= LinkPager::widget(['pagination' => $pagination]) ?></p>
            <?php else:?>
                <p>You don't have prizes yet.</p>
            <?php endif; ?>
            <p><a href="<?= Url::to(['site/new']) ?>">Get new reward</a></p>
        <?php endif; ?>
    </div>

    <div class="body-content">

        <div class="row">

        </div>

    </div>
</div>
