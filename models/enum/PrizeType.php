<?php
namespace app\models\enum;

class PrizeType
{
    const
        CAR = 'car',
        HOUSE = 'house',
        COOKIE = 'cookie';

    public static function getAsArray(): array
    {
        return [self::CAR, self::HOUSE, self::COOKIE];
    }

    /**
     * @return string
     */
    public static function getRandom(): string
    {
        return self::getAsArray()[array_rand(self::getAsArray())];
    }
}