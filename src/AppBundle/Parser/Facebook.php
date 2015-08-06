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
            if (isset($item['message'])) {
                $content = $item['message'];
            } elseif (isset($item['story'])) {
                $content = $item['story'];
            }
            $items[] = [
                'title' => $item['id'],
                'content' => $content
            ];
        }

        return $items;
    }

}