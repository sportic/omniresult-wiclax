<?php

namespace Sportic\Omniresult\Wiclax\Parsers\Traits;

use SimpleXMLElement;

/**
 * Trait HasXmlTrait
 * @package Sportic\Omniresult\Wiclax\Parsers\Traits
 */
trait HasXmlTrait
{
    protected ?SimpleXMLElement $xmlObject = null;

    /**
     * @return SimpleXMLElement
     * @throws \Exception
     */
    protected function getXmlObject(): mixed
    {
        if ($this->xmlObject === null) {
            $this->xmlObject = new SimpleXMLElement($this->getResponse()->getContent());
        }

        return $this->xmlObject;
    }
}
