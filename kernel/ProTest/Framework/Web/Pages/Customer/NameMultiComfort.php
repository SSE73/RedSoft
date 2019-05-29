<?php

namespace ProTest\Framework\Web\Pages\Customer;

use Facebook\WebDriver\WebDriverBy;
use ProTest\Framework\Web\Utilities\Arrays;

/**
 * Description of
 *
 * @author cerber
 */

class NameMultiComfort extends \ProTest\Framework\Web\Pages\CustomerPage
{
    /**
     * @findBy 'cssSelector'
     * @var WebDriverBy
     */
    protected $getTotalSummComfort = ".p-tarif-plan__panel-price";

    /**
     * @findBy 'cssSelector'
     * @var WebDriverBy
     */
    protected $getTotalCostMaterialsRepair = "tfoot .b-product-section__price";

    /**
     * @findBy 'cssSelector'
     * @var WebDriverBy
     */
    protected $clickAddToCart = ".btn.btn_main.sz_l";

    /**
     * @findBy 'cssSelector'
     * @var WebDriverBy
     */
    protected $addArrayProduct = ".nano-content table tbody tr";

    /**
     * @findBy 'cssSelector'
     * @var WebDriverBy
     */
    protected $clickDeviceComfort = "td.b-product-section__name.js-prods-trgr";

    /**
     * @findBy 'cssSelector'
     * @var WebDriverBy
     */
    protected $getNameProductMultiComfortFromCart = "td.td_1 .b-product__name";

    /**
     * @findBy 'cssSelector'
     * @var WebDriverBy
     */
    protected $getPricePerItemProductMultiComfortFromCart = "td.td_2";

    /**
     * @findBy 'cssSelector'
     * @var WebDriverBy
     */
    protected $getQuantityProductMultiComfortFromCart = "td.td_3";

    /**
     * @findBy 'cssSelector'
     * @var WebDriverBy
     */
    protected $getTotalPriceMultiComfortFromCart = "td.td_4";


    public function loadNameMultiComfort($autologin = false,$varNameMultiComfort,$idComfort)
    {
        $result = true;
        $this->driver->get($this->storeUrl . 'multicomfort/' . $varNameMultiComfort . '/' . $idComfort);
        if ($autologin === true && !$this->isLogedIn()) {
            $result = parent::load(true);
            if ($result === true) {
                $this->driver->get($this->storeUrl . 'multicomfort/' . $varNameMultiComfort . '/' . $idComfort);
            }
        }
        return $result;
    }

    public function getTotalSummComfort()
    {
        $this->waitForAjax();
        return $this->driver->findElement($this->getTotalSummComfort)->getText();
    }

    public function getTotalCostMaterialsRepair()
    {
        $this->waitForAjax();
        return $this->driver->findElement($this->getTotalCostMaterialsRepair)->getText();
    }

    public function clickAddToCart()
    {
        $this->waitForAjax();
        $this->driver->findElement($this->clickAddToCart)->click();
    }

    public function clickDeviceComfort()
    {
        $this->waitForAjax();
        $deviceComforts = $this->driver->findElements($this->clickDeviceComfort);

        foreach ($deviceComforts as $deviceComfort) {

            if (($deviceComfort->getAttribute("textContent") == "Устройство стяжки полов:") or
                ($deviceComfort->getAttribute("textContent") == "Устройство влажных зон:") or
                ($deviceComfort->getAttribute("textContent") == "Устройство потолков:") ) {

                $deviceComfort->click();
            }
        }
    }

    public function addArrayProduct()
    {
        $this->waitForAjax();
        $this->clickDeviceComfort();

        $products= $this->driver->findElements($this->addArrayProduct);

        $arrayComfort = array();

        $ii1 = 0;

        $arrayComfort[0] = array();
        $arrayComfort[1] = array();
        $arrayComfort[2] = array();
        $arrayComfort[3] = array();

        foreach ($products as $product) {

            $name= $product->findElement($this->getNameProductMultiComfortFromCart)->getAttribute("textContent");
            $posSKU = strpos($name, 'Артикул');

            $nameProduct = substr($name,0, $posSKU-1);
            $pricePerItem = $product->findElement($this->getPricePerItemProductMultiComfortFromCart)->getAttribute("textContent");
            $quantity = $product->findElement($this->getQuantityProductMultiComfortFromCart)->getText();
            $total = $product->findElement($this->getTotalPriceMultiComfortFromCart)->getText();

            $arrayComfort[$ii1][0] = $nameProduct;
            $arrayComfort[$ii1][1] = $pricePerItem;
            $arrayComfort[$ii1][2] = $quantity;
            $arrayComfort[$ii1][3] = $total;

            $ii1 = $ii1 + 1;
        }

        return $arrayComfort;
    }

    public function sortArrayProduct() {
        $productsArray = $this->addArrayProduct();
        $productsArraySort = Arrays::array_msort($productsArray, array('0'=>SORT_ASC));
        return $productsArraySort;
    }
}