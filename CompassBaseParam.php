<?php
/**
 * @file
 * ${FILE_DESCRIPTION}
 */

class CompassBaseParam
{
    /**
     * @var string
     */
    protected $text;

    /**
     * @param string $text
     */
    public function addText($text)
    {
        $this->text = $text;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }
}
