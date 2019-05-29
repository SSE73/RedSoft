<?php

namespace ProTest\Framework\Web\Pages\Customer;

use Facebook\WebDriver\Interactions\WebDriverActions;
use Facebook\WebDriver\WebDriverBy;

/**
 * Description of
 *
 * @author cerber
 */

class MultiComfort extends \ProTest\Framework\Web\Pages\CustomerPage
{
    /**
     * @findBy 'cssSelector'
     * @var WebDriverBy
     */
    protected $clickMultiComfort = ".b-intro__wrap .btn.btn_main.sz_l";

    /**
     * @findBy 'cssSelector'
     * @var WebDriverBy
     */
    protected $inputFootage = ".field_text.js-calc__fld";

    public function load($autologin = false)
    {
        $result = true;
        $this->driver->get($this->storeUrl . 'multicomfort');
        if ($autologin === true && !$this->isLogedIn()) {
            $result = parent::load(true);
            if ($result === true) {
                $this->driver->get($this->storeUrl . 'multicomfort');
            }
        }
        return $result;
    }

    public function clickMultiComfort()
    {
        $this->waitForAjax();
        $this->driver->findElement($this->clickMultiComfort)->click();
    }
}