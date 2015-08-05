<?php

namespace AppBundle\Tests\Model;

final class App
{
    const FB_ACCESS_TOKEN = 'CAACFb9bXwZBUBAEidei6U0xFTDOKttjIFx5j3DRiF2iI8o2VvLAWhjR0psv2XC6VVG8ZCASiP7EVNTXkoSBXAo2cx1E8FQhYOiHx8PixlLKnxuY4mxk7p5tt2ld1dpEfZAYOM1oru0kSFhncROl7bKNuNrcl3agY7A7dJzrP6wVSPvCCOGzAy1wZAgbv6QYapiSCfVHlE7WY2F6LNRhV';

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