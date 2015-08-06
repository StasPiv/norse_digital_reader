<?php

namespace AppBundle\Parser;

interface IParser
{
    /**
     * @param string $content
     * @return array
     */
    public function getItems($content);
}