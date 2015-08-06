<?php

namespace AppBundle\Parser;

class Rss implements IParser
{
    /**
     * @param string $content
     * @return array
     */
    public function getItems($content)
    {
        return [
            [
                'title' => 'test',
                'content' => 'content'
            ]
        ];
    }

}