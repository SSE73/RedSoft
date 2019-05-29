<?php

namespace ProWeb\tests;

/**
 * Description of
 *
 * @author cerber
 */
class testComplexSolutions extends \ProWeb\AProWeb
{

    protected $data = [
        'products' => [
            ['productId' => 2],
            ['productId' => 6],
            ['productId' => 8],
            ['productId' => 9],
            ['productId' => 10]]
        ];

    public function testComplexSolutionsTest() {

        $this->addMultiProduct();

        $cart = $this->CustomerCart;
        $cart->inputPickup();
        $cart->pressCalculateDeliveryOrder();

        $information = $this->CustomerUserInformation;
        $numberOrder = $information->getNumberOrder();
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
    }

    public function addMultiProduct(){

        foreach ($this->data['products'] as $product) {

            $storeFront = $this->CustomerIndex;
            $this->assertTrue($storeFront->load(false), 'Error loading Customer page.');

            $storeFront->clickFilterComplexSolutionMansandra();

            $productId = $storeFront->openProduct($product['productId']);

            $product = $this->CustomerProduct;
            $this->assertTrue($product->loadProductId(false,$productId), 'Error loading Product page.');
            $product->setSpinner(1);
            $product->addToCart();

        }

    }

}