<?php

namespace ProWeb\tests;

/**
 * Description of
 *
 * @author cerber
 */
class testStandardOrderWithShippingCostCalculation extends \ProWeb\AProWeb
{
    public function testStandardOrderWithShippingCostCalculationTest() {

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

        $totalCart = $cart->getTotalCart();
        $cart->pressCalculateDeliveryOrder();

        $checkout = $this->CustomerCheckout;
        $numberOrder = $checkout->getNumberOrder();
        $this->assertTrue($checkout->loadCheckout(false,$numberOrder), 'Error loading Checkout page.');

        $checkout->inputShippingAddress("Россия, Москва, шоссе Энтузиастов, 86");
        $checkout->pressCalculate();
        $checkout->pressContinue();

        $information = $this->CustomerUserInformation;
        $this->assertTrue($information->loadInformation(false,$numberOrder), 'Error loading Customer Information page.');

        $amountWithoutDelivery = $information->getAmountWithoutDelivery();
        $deliveryAmount = $information->getDeliveryAmount();
        $totalCheckout = $information->getTotalCheckout();

        $information->fillFormCustomerInformation();

        $payment = $this->CustomerPayment;
        $this->assertTrue($payment->loadPayment(false,$numberOrder), 'Error loading Payment page.');

        $payment->inputCashlessPayments();
        $payment->pressContinue();

        $order = $this->CustomerOrder;
        $this->assertTrue($order->loadOrder(false,$numberOrder), 'Error loading Order page.');

        $this->assertEquals($amountWithoutDelivery, $order->getAmountWithoutDelivery(),"Суммы без доставки не совпадают");
        $this->assertEquals($deliveryAmount, $order->getDeliveryAmount(),"Суммы доставки не совпадают");
        $this->assertEquals($totalCheckout, $order->getTotalOrder(),"Суммы Итого не совпадают");

    }
}