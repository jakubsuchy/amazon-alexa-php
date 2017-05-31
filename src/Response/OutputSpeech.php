<?php

namespace Alexa\Response;

use Exception;

class OutputSpeech
{
    public $type = 'PlainText';
    public $text = '';
    public $ssml = '';

    public function render()
    {
        switch ($this->type) {
            case 'PlainText':
                return array(
                    'type' => $this->type,
                    'text' => $this->text
                );
            case 'SSML':
                return array(
                    'type' => $this->type,
                    'ssml' => $this->ssml
                );
            default:
                throw new Exception('Unsupported type ' . $this->type);
        }
    }
}