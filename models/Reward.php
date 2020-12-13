<?php
namespace app\models;

use app\models\enum\RewardStatus;
use app\models\enum\RewardType;
use http\Exception\RuntimeException;
use Yii;
use yii\db\ActiveRecord;

class Reward extends ActiveRecord
{
    protected function _before()
    {
        \Yii::$app->user->logout();
    }

    /**
     * @return bool
     */
    public function isProcessed(): bool
    {
        return $this->status === RewardStatus::PROCESSED;
    }

    public function convert()
    {
        if ($this->isProcessed()) {
            throw new RuntimeException('Reward already processed');
        }

        switch ($this->type) {
            case RewardType::BONUS:
                $this->type = RewardType::MONEY;
                $this->amount = $this->amount * Yii::$app->params['moneyToBonusRate'];
                break;
            case RewardType::MONEY:
                $this->type = RewardType::BONUS;
                $this->amount = $this->amount / Yii::$app->params['moneyToBonusRate'];
                break;
            default:
                break;
        }
    }

}