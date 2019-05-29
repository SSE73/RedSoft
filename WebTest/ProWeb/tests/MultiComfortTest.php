<?php

namespace ProWeb\tests;

/**
 * Description of
 *
 * @author cerber
 */
class testMultiComfort extends \ProWeb\AProWeb
{
    public function testMultiComfortTest() {

        $footage = '156';
        $nameMultiComfort = "comfort-premium";

        $comfort = $this->CustomerMultiComfort;
        $this->assertTrue($comfort->load(false), 'Error loading Multi Comfort page.');
        $comfort->clickMultiComfort();

        $calculator = $this->CustomerCalculatorMultiComfort;
        $this->assertTrue($calculator->load(false), 'Error loading Calculator Multi Comfort page.');

        $calculator->inputFootage($footage);
        $calculator->clickCalculate();

        $homeComformt = $this->CustomerHomeMultiComfort;
        $this->assertTrue($homeComformt->loadMultiComfort(false,$footage), 'Error loading Home Multi Comfort page.');

        $totalSummPremium = $homeComformt->getTotalSummPremium();
        $homeComformt->clickBuy();

        $nameComfort = $this->CustomerNameMultiComfort;
        $this->assertTrue($nameComfort->loadNameMultiComfort(false, $nameMultiComfort, $footage), 'Error loading Name Multi Comfort page.');

        $productsArraySort = $nameComfort->sortArrayProduct();

        $nameComfort = $this->CustomerNameMultiComfort;
        $this->assertTrue($nameComfort->loadNameMultiComfort(false, $nameMultiComfort, $footage), 'Error loading Name Multi Comfort page.');

        $totalSummComfort = $nameComfort->getTotalSummComfort();
        $totalCostMaterialsRepair = $nameComfort->getTotalCostMaterialsRepair();

        $nameComfort->clickAddToCart();

        $cart = $this->CustomerCart;
        $productsArrayCartSort = $cart->sortArrayCart();

        $cart->inputPickup();
        $cart->pressCalculateDeliveryOrder();

        $information = $this->CustomerUserInformation;
        $numberOrder = $information->getNumberOrder();
        $this->assertTrue($information->loadInformation(false,$numberOrder), 'Error loading Customer Information page.');

        $arrayWholesalePrices = $this->addProductWholesalePrices($productsArraySort);  //стало

        $information = $this->CustomerUserInformation;
        $this->assertTrue($information->loadInformation(false,$numberOrder), 'Error loading Customer Information page.');

        $totalCheckout = $information->getTotalCheckout();


        $information->fillFormCustomerInformation();

        $payment = $this->CustomerPayment;
        $this->assertTrue($payment->loadPayment(false,$numberOrder), 'Error loading Payment page.');

        $payment->inputCashlessPayments();
        $payment->pressContinue();

        $order = $this->CustomerOrder;
        $this->assertTrue($order->loadOrder(false,$numberOrder), 'Error loading Order page.');
        $this->assertEquals($totalCheckout, $order->getTotalOrder(),"Суммы Итого не совпадают");
        $this->assertTrue($productsArrayCartSort == $arrayWholesalePrices,"Неправильный расчет");
    }

    public function addProductWholesalePrices($productsArraySort)
    {

        $arrayCartComfort = array();

        $ii1 = 0;

        $arrayCartComfort[0] = array();
        $arrayCartComfort[1] = array();
        $arrayCartComfort[2] = array();
        $arrayCartComfort[3] = array();

        foreach ($productsArraySort as $product) {

            $storeFront = $this->CustomerIndex;
            $this->assertTrue($storeFront->load(false), 'Error loading Customer page.');

            $productId = $storeFront->clickFindProductName($product['0']);
            $quantity = $product['2'];

            $productWholesalePrices = $this->CustomerProduct;
            $this->assertTrue($productWholesalePrices->loadProductId(false, $productId), 'Error loading Product page.');

            $productWholesalePrices->setSpinner($quantity);

            $pricePerItemWholesalePrices = $productWholesalePrices->getUnitPriceProduct();
            $totalWholesalePrices = $productWholesalePrices->getPriceProduct();

            $arrayCartComfort[$ii1][0] = $product['0'];
            $arrayCartComfort[$ii1][1] = $pricePerItemWholesalePrices;
            $arrayCartComfort[$ii1][2] = $product['2'];
            $arrayCartComfort[$ii1][3] = $totalWholesalePrices;

            $ii1 = $ii1 + 1;

        }
        return $arrayCartComfort;
    }
}
