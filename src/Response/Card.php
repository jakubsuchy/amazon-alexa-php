<?php

namespace Alexa\Response;

class Card
{
    public $type = 'Simple';
    public $title = '';
    public $content = '';

    public function render()
    {
        return array(
            'type' => $this->type,
            'title' => $this->title,
            'content' => $this->content,
        );
    }
}
