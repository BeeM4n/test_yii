<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use app\models\enum\RewardStatus;
use app\models\enum\RewardType;
use app\models\Reward;
use yii\console\Controller;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class RewardsController extends Controller
{
    /**
     * Process new money rewards
     */
    public function actionProcess()
    {
        $limit = 5;
        $allProcessed = false;
        $rewardsQuery = Reward::find()
            ->where('type = :type AND status = :status', ['type' => RewardType::MONEY, 'status' => RewardStatus::NEW])
            ->limit($limit);

        while (!$allProcessed) {
            $rewards = $rewardsQuery->all();
            if (count($rewards) > 0) {
                $idsToSend = [];
                foreach ($rewards as $reward) {
                    $idsToSend[] = $reward->id;
                }

                //TODO: send $idsToSend to API
                $success = true;

                if ($success) {
                    foreach ($rewards as $reward) {
                        $reward->status = RewardStatus::PROCESSED;
                        $reward->save();
                    }
                }
            } else {
                $allProcessed = true;
            }
        }
    }
}
