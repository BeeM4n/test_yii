<?php
namespace app\models\enum;

class RewardStatus
{
    const
        NEW = 'new',
        PROCESSED = 'processed';

    /**
     * @param string $status
     * @return string
     */
    public static function getPrizeShipmentStatus(string $status): string
    {
        return $status === self::NEW ? 'expecting shipment' : 'shipped';
    }
}