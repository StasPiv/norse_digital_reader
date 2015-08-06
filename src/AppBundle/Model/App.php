<?php

namespace AppBundle\Model;

final class App
{
    /**
     * @var integer
     */
    private static $currentUserId;

    /**
     * @return int
     */
    public static function getCurrentUserId()
    {
        return self::$currentUserId;
    }

    /**
     * @param int $currentUserId
     */
    public static function setCurrentUserId($currentUserId)
    {
        self::$currentUserId = $currentUserId;
    }

}