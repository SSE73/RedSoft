<?php
namespace ProTest\Framework\Web\Pages\Customer;

use Facebook\WebDriver\WebDriverBy;

/**
 * Description of
 *
 * @author cerber
 */

class Payment extends \ProTest\Framework\Web\Pages\CustomerPage
{
    /**
     * @findBy 'xpath'
     * @var WebDriverBy
     */
    protected $inputCashlessPayments = ".//*[text()='Безналичный расчет ']/ancestor::*[1]/div";

    /**
     * @findBy 'cssSelector'
     * @var WebDriverBy
     */
    protected $pressContinue = "#edit-continue";

    public function loadPayment($autologin = false,$numberOrder)
    {
        $result = true;
        $this->driver->get($this->storeUrl . 'checkout/' . $numberOrder . '/review');
        if ($autologin === true && !$this->isLogedIn()) {
            $result = parent::load(true);
            if ($result === true) {
                $this->driver->get($this->storeUrl . 'checkout/' . $numberOrder . '/review');
            }
        }
        return $result;
    }

    public function inputCashlessPayments()
    {
        $this->waitForAjax();
        $this->driver->findElement($this->inputCashlessPayments)->click();
    }

    public function pressContinue()
    {
        $this->waitForAjax();
        $this->driver->findElement($this->pressContinue)->click();
    }
}