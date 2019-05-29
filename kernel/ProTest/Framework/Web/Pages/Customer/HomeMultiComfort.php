<?php

namespace ProTest\Framework\Web\Pages\Customer;

use Facebook\WebDriver\WebDriverBy;

/**
 * Description of
 *
 * @author cerber
 */

class HomeMultiComfort extends \ProTest\Framework\Web\Pages\CustomerPage
{

    /**
     * @findBy 'cssSelector'
     * @var WebDriverBy
     */
    protected $getTotalSummPremium = ".b-tarif.c3 .b-tarif__price span";

    /**
     * @findBy 'cssSelector'
     * @var WebDriverBy
     */
    protected $clickBuy = ".b-tarif.c3  .b-tarif__button a";

    public function loadMultiComfort($autologin = false,$idComfort)
    {
        $result = true;
        $this->driver->get($this->storeUrl . 'multicomfort/home/' . $idComfort . '#b-calc');
        if ($autologin === true && !$this->isLogedIn()) {
            $result = parent::load(true);
            if ($result === true) {
                $this->driver->get($this->storeUrl . 'multicomfort/home/' . $idComfort . '#b-calc');
            }
        }
        return $result;
    }

    public function getTotalSummPremium()
    {
        $this->waitForAjax();
        return $this->driver->findElement($this->getTotalSummPremium)->getText();
    }

    public function clickBuy()
    {
        $this->waitForAjax();
        $this->driver->findElement($this->clickBuy)->click();
    }

}