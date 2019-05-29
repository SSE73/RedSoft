<?php
namespace ProTest\Framework\Web\Pages\Customer;

use Facebook\WebDriver\WebDriverBy;

/**
 * Description of
 *
 * @author cerber
 */

class Product extends \ProTest\Framework\Web\Pages\CustomerPage
{

    /**
     * @findBy 'cssSelector'
     * @var WebDriverBy
     */
    protected $inputSpinner = "#spinner";

    /**
     * @findBy 'cssSelector'
     * @var WebDriverBy
     */
    protected $getNameProduct = ".right-col>h1";

    /**
     * @findBy 'cssSelector'
     * @var WebDriverBy
     */
    protected $getSKUProduct = ".number-product>span";

    /**
     * @findBy 'cssSelector'
     * @var WebDriverBy
     */
    protected $getUnitPriceProduct = ".prices-box>table tr td.active div";  //.price.price1

    /**
     * @findBy 'cssSelector'
     * @var WebDriverBy
     */
    protected $findWholesalePriceProduct = ".price.price2";

    /**
     * @findBy 'cssSelector'
     * @var WebDriverBy
     */
    protected $getPriceProduct = ".price-box span.price";

    /**
     * @findBy 'cssSelector'
     * @var WebDriverBy
     */
    protected $addToCart = ".btn-buy.to-cart";

    public function loadProductId($autologin = false,$productId)
    {
        $result = true;
        $this->driver->get($productId);
        if ($autologin === true && !$this->isLogedIn()) {
            $result = parent::load(true);
            if ($result === true) {
                $this->driver->get($productId);
            }
        }
        return $result;
    }

    public function getNameProduct()
    {
        $this->waitForAjax();
        return $this->driver->findElement($this->getNameProduct)->getAttribute("textContent");
    }

    public function getNameSKU()
    {
        $this->waitForAjax();
        return $this->driver->findElement($this->getSKUProduct)->getText();
    }

    public function getUnitPriceProduct()
    {
        $this->waitForAjax();
        return $this->driver->findElement($this->getUnitPriceProduct)->getText();
    }

    public function getPriceProduct()
    {
        $this->waitForAjax();
        return $this->driver->findElement($this->getPriceProduct)->getText();
    }

    public function setSpinner($valueSpinner)
    {
        $this->waitForAjax();
        $this->driver->findElement($this->inputSpinner)->clear()->sendKeys($valueSpinner);
    }

    public function addToCart()
    {
        $this->waitForAjax();
        $this->driver->findElement($this->addToCart)->click();
    }

}