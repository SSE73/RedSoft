<?php

namespace ProTest\Framework\Web\Pages;

use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\Remote\RemoteWebElement;

/**
 * Description of Component
 *
 * @author cerber
 */
class Component extends \ProTest\Framework\Web\Pages\Page
{
    private $component = null;
    
    protected $findBy = null;

    /**
     * @return RemoteWebElement
     * @throws \Exception
     */
    public function getComponent() {
        
        if ($this->findBy == null) {
            throw new \Exception('Error in component declaration. findBy is null.');
        }
        if ($this->component == null) {
            $this->component = $this->driver->findElement($this->findBy);
        }
        return $this->component;
    }
    
    public function isElementPresent(WebDriverBy $by, RemoteWebElement $element = null) {
        return parent::isElementPresent($by, $this->component);
    }

}
