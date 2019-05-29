<?php

namespace ProTest\Framework\Web\Pages\Customer;

use Facebook\WebDriver\WebDriverBy;

/**
 * Description of
 *
 * @author cerber
 */

class Index extends \ProTest\Framework\Web\Pages\CustomerPage
{
    /**
     * @findBy 'xpath'
     * @var WebDriverBy
     */
    protected $clickFilterComplexSolution = ".//h3[text()='КОМПЛЕКСНЫЕ РЕШЕНИЯ'  and @data-content = 'filter-integratedsolution']";

    /**
     * @findBy 'xpath'
     * @var WebDriverBy
     */
    protected $clickMansandra = ".//label[text()='Мансарда']";

    public function load($autologin = false)
    {

        $this->waitForAjax();
        $result = true;
        $this->driver->get($this->storeUrl . 'search');
        if ($autologin === true && !$this->isLogedIn()) {
            $result = parent::load(true);
            if ($result === true) {
                $this->driver->get($this->storeUrl . 'search');
            }
        }
        return $result;
    }

    public function openProduct($idProduct)
    {
        $this->waitForAjax();
        $openProduct = ".b-product:nth-child($idProduct) .name a";
        $this->driver->findElement(WebDriverBy::cssSelector($openProduct))->click();
        return $this->driver->findElement(WebDriverBy::cssSelector($openProduct))->getAttribute("href");

    }

    public function clickFindProductName($name)
    {
        $productId = $this->driver->findElement(WebDriverBy::xpath('.//a[text()="' .$name . '"]/ancestor::*[1]/a'))->getAttribute("href");
        return $productId;
    }

    public function clickFilterComplexSolutionMansandra()
    {
        $this->waitForAjax();
        $this->driver->findElement($this->clickFilterComplexSolution)->click();
        $this->waitForAjax();
        $this->driver->findElement($this->clickMansandra)->click();
        $this->waitForAjax();
    }

}