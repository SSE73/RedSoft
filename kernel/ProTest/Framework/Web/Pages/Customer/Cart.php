<?php
namespace ProTest\Framework\Web\Pages\Customer;

use Facebook\WebDriver\WebDriverBy;
use ProTest\Framework\Web\Utilities\Arrays;

/**
 * Description of
 *
 * @author cerber
 */

class Cart extends \ProTest\Framework\Web\Pages\CustomerPage
{

    /**
     * @findBy 'cssSelector'
     * @var WebDriverBy
     */
    protected $getMessagesStatus = ".messages.status";  // .placeholder

    /**
     * @findBy 'cssSelector'
     * @var WebDriverBy
     */
    protected $pressCalculateDelivery = "#edit-checkout";

    /**
     * @findBy 'xpath'
     * @var WebDriverBy
     */
    protected $inputPickup = ".//*[text()='Самовывоз ']/ancestor::*[1]/div";

    /**
     * @findBy 'cssSelector'
     * @var WebDriverBy
     */
    protected $getTotalCart = ".value > div:nth-child(1)";

    /**
     * @findBy 'cssSelector'
     * @var WebDriverBy
     */
    protected $getNameProductFromCart = "div.name a";

    /**
     * @findBy 'cssSelector'
     * @var WebDriverBy
     */
    protected $getNameProductTxtFromCart = ".col-2.title div.name";

    /**
     * @findBy 'cssSelector'
     * @var WebDriverBy
     */
    protected $getPricePerItemProductFromCart = "span.price-amount";

    /**
     * @findBy 'cssSelector'
     * @var WebDriverBy
     */
    protected $getQuantityProductFromCart = "span.ui-spinner input[value]";

    /**
     * @findBy 'cssSelector'
     * @var WebDriverBy
     */
    protected $getTotalPriceFromCart = "div.product-type-short";

    /**
     * @findBy 'cssSelector'
     * @var WebDriverBy
     */
    protected $addArrayCart = "#views-form-commerce-cart-form-cart-pane>div> table > tbody > tr";

    public function load($autologin = false)
    {
        $result = true;
        $this->driver->get($this->storeUrl . 'cart');
        if ($autologin === true && !$this->isLogedIn()) {
            $result = parent::load(true);
            if ($result === true) {
                $this->driver->get($this->storeUrl . 'cart');
            }
        }
        return $result;
    }

    public function getMessagesStatus()
    {
        $this->waitForAjax();
        return $this->driver->findElement($this->getMessagesStatus)->getText();
    }

    public function pressCalculateDeliveryOrder()
    {
        $this->waitForAjax();
        $this->driver->findElement($this->pressCalculateDelivery)->click();

    }

    public function inputPickup()
    {
        $this->waitForAjax();
        $this->driver->findElement($this->inputPickup)->click();
    }

    public function getTotalCart()
    {
        $this->waitForAjax();
        return $this->driver->findElement($this->getTotalCart)->getText();
    }

    public function getNameProductFromCart()
    {
        $this->waitForAjax();
        return $this->driver->findElement($this->getNameProductFromCart)->getAttribute("textContent");
    }

    public function addArrayCart()
    {
        $this->waitForAjax();
        $products= $this->driver->findElements($this->addArrayCart);

        $arrayCartComfort = array();

        $ii1 = 0;

        $arrayCartComfort[0] = array();
        $arrayCartComfort[1] = array();
        $arrayCartComfort[2] = array();
        $arrayCartComfort[3] = array();


        foreach ($products as $product) {

            $nameProductTxt = $product->findElement($this->getNameProductTxtFromCart)->getAttribute("textContent");

            if (trim($nameProductTxt) !== "Респиратор") {

                $nameProduct= $product->findElement($this->getNameProductFromCart)->getAttribute("textContent");
                $pricePerItem = $product->findElement($this->getPricePerItemProductFromCart)->getAttribute("textContent");
                $quantity = $product->findElement($this->getQuantityProductFromCart)->getAttribute("value");
                $total = $product->findElement($this->getTotalPriceFromCart)->getText();

                $arrayCartComfort[$ii1][0] = $nameProduct;
                $arrayCartComfort[$ii1][1] = $pricePerItem;
                $arrayCartComfort[$ii1][2] = $quantity;
                $arrayCartComfort[$ii1][3] = str_replace("," ,".", str_replace(" ","",$total) );

                $ii1 = $ii1 + 1;

            }

        }
        return $arrayCartComfort;
    }

    public function addArrayCartSortName($productsArrayCartSortName)
    {

        $arrayCartSortName = array();

        $ii1 = 0;

        $arrayCartSortName[0] = array();
        $arrayCartSortName[1] = array();
        $arrayCartSortName[2] = array();
        $arrayCartSortName[3] = array();

        foreach ($productsArrayCartSortName as $product) {

            $arrayCartSortName[$ii1][0] = $product['0'];
            $arrayCartSortName[$ii1][1] = $product['1'];
            $arrayCartSortName[$ii1][2] = $product['2'];
            $arrayCartSortName[$ii1][3] = $product['3'];

            $ii1 = $ii1 + 1;

        }
        return $arrayCartSortName;
    }

    public function sortArrayCart() {

        $productsArray = $this->addArrayCart();
        $productsArrayCartSortName = Arrays::array_msort($productsArray, array('0'=>SORT_ASC));
        $productsArrayCartSort = $this->addArrayCartSortName($productsArrayCartSortName);

        return $productsArrayCartSort;
    }
}