<?php

namespace ProTest\Framework\Web\Pages\Customer;

use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverKeys;

/**
 * Description of
 *
 * @author cerber
 */

class CalculatorMultiComfort extends \ProTest\Framework\Web\Pages\CustomerPage
{

    /**
     * @findBy 'cssSelector'
     * @var WebDriverBy
     */
    protected $inputFootage = ".field_text.js-calc__fld";

    /**
     * @findBy 'cssSelector'
     * @var WebDriverBy
     */
    protected $clickCalculate = ".b-calc-form .btn.btn_main";

    public function load($autologin = false)
    {
        $result = true;
        $this->driver->get($this->storeUrl . 'multicomfort#b-calc');
        if ($autologin === true && !$this->isLogedIn()) {
            $result = parent::load(true);
            if ($result === true) {
                $this->driver->get($this->storeUrl . 'multicomfort#b-calc');
            }
        }
        return $result;
    }

    public function inputFootage($valueFootage)
    {
        $this->waitForAjax();
        $this->driver->findElement($this->inputFootage)->clear()->sendKeys(WebDriverKeys::HOME)->sendKeys($valueFootage);
    }

    public function clickCalculate()
    {
        $this->waitForAjax();
        $this->driver->findElement($this->clickCalculate)->click();
    }
}