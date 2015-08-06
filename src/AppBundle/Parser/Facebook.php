<?php

namespace AppBundle\Parser;

class Facebook implements IParser
{
    /**
     * @param string $content
     * @return array
     */
    public function getItems($content)
    {
        $data = json_decode($content, true);

        $items = [];

        foreach ($data['data'] as $item) {
            $items[] = [
                'title' => $item['id'],
                'content' => $item['message']
            ];
        }

        return $items;
    }

}