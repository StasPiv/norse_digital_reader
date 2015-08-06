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
        $xml = simplexml_load_string($content);

        $items  = [];

        foreach ($xml->channel->item as $item) {
            $items[] = [
                'title' => $item->title,
                'content' => $item->description
            ];
        }

        return $items;
    }

}