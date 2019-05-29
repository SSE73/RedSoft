<?php

namespace ProWeb\tests;

/**
 * Description of
 *
 * @author cerber
 */
class testOrderWithPickupCashlessPayment extends \ProWeb\AProWeb
{
    public function testOrderWithPickupCashlessPaymentTest() {

        $storeFront = $this->CustomerIndex;
        $this->assertTrue($storeFront->load(false), 'Error loading Customer page.');
        $productId = $storeFront->openProduct(1);

        $product = $this->CustomerProduct;
        $this->assertTrue($product->loadProductId(false,$productId), 'Error loading Product page.');

        $nameProduct = $product->getNameProduct();

        $product->setSpinner(1);
        $product->addToCart();

        $cart = $this->CustomerCart;

        $this->assertEquals($cart->getNameProductFromCart(),$nameProduct);

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
}