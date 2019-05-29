<?php
namespace ProTest\Framework\Web\Pages\Customer;

use Facebook\WebDriver\WebDriverBy;

/**
 * Description of
 *
 * @author cerber
 */

class Checkout extends \ProTest\Framework\Web\Pages\CustomerPage
{
    /**
     * @findBy 'cssSelector'
     * @var WebDriverBy
     */
    protected $getNumberOrder = "#commerce-checkout-form-checkout";

    /**
     * @findBy 'cssSelector'
     * @var WebDriverBy
     */
    protected $inputShippingAddress = "#edit-customer-profile-shipping-string-address";

    /**
     * @findBy 'cssSelector'
     * @var WebDriverBy
     */
    protected $pressCalculate = "#edit-customer-profile-shipping-delivery-submit-1";

    /**
     * @findBy 'cssSelector'
     * @var WebDriverBy
     */
    protected $pressContinue = "#edit-continue";

    public function getNumberOrder()
    {
        $this->waitForAjax();
        $urlCheckout = $this->driver->findElement($this->getNumberOrder)->getAttribute("action");
        $posCheckout = strpos($urlCheckout, 'checkout');
        $numberOrder = substr($urlCheckout,$posCheckout+9,strlen($urlCheckout));
        $this->waitForAjax();
        return $numberOrder;
    }

    public function loadCheckout($autologin = false,$numberOrder)
    {
        $result = true;
        $this->driver->get($this->storeUrl . 'checkout/' . $numberOrder);
        if ($autologin === true && !$this->isLogedIn()) {
            $result = parent::load(true);
            if ($result === true) {
                $this->driver->get($this->storeUrl . 'checkout/' . $numberOrder);
            }
        }
        return $result;
    }

    public function inputShippingAddress($valueShippingAddress)
    {
        $this->waitForAjax();
        $this->driver->findElement($this->inputShippingAddress)->clear()->sendKeys($valueShippingAddress);
    }

    public function pressCalculate()
    {
        $this->waitForAjax();
        $this->driver->findElement($this->pressCalculate)->click();
    }

    public function pressContinue()
    {
        $this->waitForAjax();
        $this->driver->findElement($this->pressContinue)->click();
    }
}