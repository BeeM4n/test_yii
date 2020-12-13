<?php
namespace app\models\enum;

class RewardType
{
    const
        MONEY = 'money',
        BONUS = 'bonus',
        PRIZE = 'prize';

    public static function getAsArray(): array
    {
        return [self::MONEY, self::BONUS, self::PRIZE];
    }

    /**
     * @return string
     */
    public static function getRandom(): string
    {
        return self::getAsArray()[array_rand(self::getAsArray())];
    }
}