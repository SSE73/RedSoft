<?php

namespace ProTest\Framework\Web\Pages\Customer;

use Facebook\WebDriver\WebDriverBy;

/**
 * Description of
 *
 * @author cerber
 */

class Order extends \ProTest\Framework\Web\Pages\CustomerPage
{

    /**
     * @findBy 'xpath'
     * @var WebDriverBy
     */
    protected $getAmountWithoutDelivery = ".//div[text()='Сумма без доставки :']/following-sibling::div";

    /**
     * @findBy 'xpath'
     * @var WebDriverBy
     */
    protected $getDeliveryAmount = ".//div[text()='Доставка :']/following-sibling::div";

    /**
     * @findBy 'xpath'
     * @var WebDriverBy
     */
    protected $getTotalOrder = ".//div[text()='итого :']/following-sibling::*[1]/div[1]";

    public function loadOrder($autologin = false,$numberOrder)
    {
        $result = true;
        $this->driver->get($this->storeUrl . 'checkout/' . $numberOrder . '/finish');
        if ($autologin === true && !$this->isLogedIn()) {
            $result = parent::load(true);
            if ($result === true) {
                $this->driver->get($this->storeUrl . 'checkout/' . $numberOrder . '/finish');
            }
        }
        return $result;
    }


    public function getAmountWithoutDelivery()
    {
        $this->waitForAjax();
        return $this->driver->findElement($this->getAmountWithoutDelivery)->getText();
    }

    public function getDeliveryAmount()
    {
        $this->waitForAjax();
        return $this->driver->findElement($this->getDeliveryAmount)->getText();
    }

    public function getTotalOrder()
    {
        $this->waitForAjax();
        return $this->driver->findElement($this->getTotalOrder)->getText();
    }
}