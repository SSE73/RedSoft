<?php
namespace ProTest\Framework\Web\Pages\Customer;

use Facebook\WebDriver\WebDriverBy;

/**
 * Description of
 *
 * @author cerber
 */

class UserInformation extends \ProTest\Framework\Web\Pages\CustomerPage
{

    /**
     * @findBy 'cssSelector'
     * @var WebDriverBy
     */
    protected $getNumberOrder = "#commerce-checkout-form-shipping";

    /**
     * @findBy 'cssSelector'
     * @var WebDriverBy
     */
    protected $inputEmail = "#edit-customer-profile-billing-field-customer-recepient-email-und-0-value";

    /**
     * @findBy 'cssSelector'
     * @var WebDriverBy
     */
    protected $inputFirstName = "#edit-customer-profile-billing-field-user-first-name-und-0-value";

    /**
     * @findBy 'cssSelector'
     * @var WebDriverBy
     */
    protected $inputLastName = "#edit-customer-profile-billing-field-user-last-name-und-0-value";

    /**
     * @findBy 'cssSelector'
     * @var WebDriverBy
     */
    protected $inputPhone = "#edit-customer-profile-billing-field-user-phone-und-0-value";

    /**
     * @findBy 'cssSelector'
     * @var WebDriverBy
     */
    protected $inputIAgreeProcessingPersonalData = "#edit-customer-profile-billing-agreement";

    /**
     * @findBy 'cssSelector'
     * @var WebDriverBy
     */
    protected $pressContinue = "#edit-continue";

    /**
     * @findBy 'xpath'
     * @var WebDriverBy
     */
    protected $getAmountWithoutDelivery = ".//div[text()='Сумма без доставки:']/following-sibling::div";

    /**
     * @findBy 'xpath'
     * @var WebDriverBy
     */
    protected $getDeliveryAmount = ".//div[text()='Доставка:']/following-sibling::div";

    /**
     * @findBy 'xpath'
     * @var WebDriverBy
     */
    protected $getTotalCheckout = ".//div[text()='итого']/following-sibling::*[1]";

    public function getNumberOrder()
    {
        $this->waitForAjax();
        $urlCheckout = $this->driver->findElement($this->getNumberOrder)->getAttribute("action");
        $posCheckout = strpos($urlCheckout, 'checkout')+9;
        $posShipping = strpos($urlCheckout, '/shipping');
        $numberOrder = substr($urlCheckout,$posCheckout,$posShipping -$posCheckout);
        return $numberOrder;
    }

    public function loadInformation($autologin = false,$numberOrder)
    {
        $result = true;
        $this->driver->get($this->storeUrl . 'checkout/' . $numberOrder . '/shipping');
        if ($autologin === true && !$this->isLogedIn()) {
            $result = parent::load(true);
            if ($result === true) {
                $this->driver->get($this->storeUrl . 'checkout/' . $numberOrder . '/shipping');
            }
        }
        return $result;
    }

    public function fillFormCustomerInformation()
    {
        $this->inputEmail("Test@mail.ru");
        $this->inputFirstName("Serg");
        $this->inputLastName("S");
        $this->inputPhone("+79371234567890");
        $this->inputIAgreeProcessingPersonalData();
        $this->pressContinue();
    }

    public function inputEmail($valueEmail)
    {
        $this->waitForAjax();
        $this->driver->findElement($this->inputEmail)->clear()->sendKeys($valueEmail);
    }

    public function inputFirstName($valueFirstName)
    {
        $this->waitForAjax();
        $this->driver->findElement($this->inputFirstName)->clear()->sendKeys($valueFirstName);
    }

    public function inputLastName($valueLastName)
    {
        $this->waitForAjax();
        $this->driver->findElement($this->inputLastName)->clear()->sendKeys($valueLastName);
    }

    public function inputPhone($valuePhone)
    {
        $this->waitForAjax();
        $this->driver->findElement($this->inputPhone)->clear()->sendKeys($valuePhone);
    }

    public function inputIAgreeProcessingPersonalData()
    {
        $this->waitForAjax();
        $this->driver->findElement($this->inputIAgreeProcessingPersonalData)->click();
    }

    public function pressContinue()
    {
        $this->waitForAjax();
        $this->driver->findElement($this->pressContinue)->click();

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

    public function getTotalCheckout()
    {
        $this->waitForAjax();
        return $this->driver->findElement($this->getTotalCheckout)->getText();
    }

}