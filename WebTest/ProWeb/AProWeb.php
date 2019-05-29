<?php

namespace ProWeb;
use Facebook\WebDriver\Chrome\ChromeOptions;
use ProTest\Framework\Config;
use Facebook\WebDriver\Remote\WebDriverCapabilityType;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverDimension;
use Facebook\WebDriver\Remote\DesiredCapabilities;

/**
 * Abstract web test case
 * 
 * autocomplite section
 * 
 * Admin
// * @property \ProTest\Framework\Web\Pages\Admin\Tasks $AdminTasks
 *
 * Customer
 * @property \ProTest\Framework\Web\Pages\Customer\Index $CustomerIndex
 * @property \ProTest\Framework\Web\Pages\Customer\Product $CustomerProduct
 * @property \ProTest\Framework\Web\Pages\Customer\Cart $CustomerCart
 * @property \ProTest\Framework\Web\Pages\Customer\Checkout $CustomerCheckout
 * @property \ProTest\Framework\Web\Pages\Customer\MultiComfort $CustomerMultiComfort
 * @property \ProTest\Framework\Web\Pages\Customer\CalculatorMultiComfort $CustomerCalculatorMultiComfort
 * @property \ProTest\Framework\Web\Pages\Customer\HomeMultiComfort $CustomerHomeMultiComfort
 * @property \ProTest\Framework\Web\Pages\Customer\NameMultiComfort $CustomerNameMultiComfort
 * @property \ProTest\Framework\Web\Pages\Customer\Order $CustomerOrder
 * @property \ProTest\Framework\Web\Pages\Customer\UserInformation $CustomerUserInformation
 * @property \ProTest\Framework\Web\Pages\Customer\Payment $CustomerPayment
 *
 * @package ProWeb
 */
abstract class AProWeb extends \ProTest\Framework\TestCase
{
    /**
     * Storefront browser
     *
     * @var WebDriver
     */
    public static $storefrontDriver = null;

    /**
     * Backend browser
     *
     * @var WebDriver
     */
    public static $backendDriver = null;
    
    public static $capabilities = null;

    public static function setUpBeforeClass()
    {
        if ("chrome" == Config::getInstance()->getOptions('web_driver', 'browser_name')) {
            $options = new ChromeOptions();

            if ("" !=Config::getInstance()->getOptions('web_driver', 'browser_binary')) {
                $options->setBinary(Config::getInstance()->getOptions('web_driver', 'browser_binary'));
            }

            if (is_array(Config::getInstance()->getOptions('web_driver', 'chrome_args'))) {
                $options->addArguments(Config::getInstance()->getOptions('web_driver', 'chrome_args'));
            }

            self::$capabilities = DesiredCapabilities::chrome();
            self::$capabilities->setCapability(ChromeOptions::CAPABILITY, $options);
        } else {
            self::$capabilities = [
                WebDriverCapabilityType::BROWSER_NAME => Config::getInstance()->getOptions('web_driver', 'browser_name')
            ];
        }


        parent::setUpBeforeClass();
    }
    
    public function getConfig($section, $key)
    {
        return Config::getInstance()->getOptions($section, $key);
    }

    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();
    }

    /**
     * 
     * @return RemoteWebDriver
     */
    
    private function getWebdriverInstance()
    {
        $driver = RemoteWebDriver::create($this->getConfig('web_driver', 'driver_url'), self::$capabilities);
        $driver->manage()->timeouts()->implicitlyWait($this->getConfig('web_driver', 'implicitlyWait'));
        $driver->manage()->timeouts()->pageLoadTimeout($this->getConfig('web_driver', 'pageLoadTimeout'));
        $driver->manage()->timeouts()->setScriptTimeout($this->getConfig('web_driver', 'scriptTimeout'));

        if ("chrome" != Config::getInstance()->getOptions('web_driver', 'browser_name')) {
            $driver->manage()->window()->maximize();
            $driver->manage()->window()->setSize(new WebDriverDimension(1280, 1024));
        }

        return $driver;
    }


    public function getBackendDriver($forceNew = false) {

        if ($forceNew && self::$backendDriver != null) {
            self::$backendDriver->quit();
            self::$backendDriver = null;
        }

        if (self::$backendDriver == null) {
            // Start backend browser
            self::$backendDriver = $this->getWebdriverInstance();
            
        }
        return self::$backendDriver;
    }
    
    public function getStorefrontDriver($forceNew = false) {

        if ($forceNew && self::$storefrontDriver != null) {
            self::$storefrontDriver->quit();
            self::$storefrontDriver = null;
        }

        if (self::$storefrontDriver == null) {
            // Start storefront browser
            self::$storefrontDriver = $this->getWebdriverInstance();
        }

        return self::$storefrontDriver;
    }

    public function clearSession($driver)
    {
        $driver->manage()->deleteCookieNamed('xid');
    }

    public function __get($name) {
        $path = '';
        if (strpos($name, 'Admin') === 0) {
            
            $path = 'Admin\\' . substr($name, 5);
            
        } elseif (strpos($name, 'Customer') === 0) {
            
            $path = 'Customer\\' . substr($name, 8);
            
        } else {
            throw new \Exception('Error in magic method __get.');
        }
        
        return $this->getPage($path);
    }
    
    private function getPage($path)
    {
        $className = '\\ProTest\\Framework\\Web\\Pages\\' . $path;
        if (strpos($path, 'Admin') === 0) {
            $driver = $this->getBackendDriver();
        } elseif (strpos($path, 'Customer') === 0) {
            $driver = $this->getStorefrontDriver();
        } else {
            throw new \Exception('Page object not found by given path.');
        }
        return new $className($driver, $this->getConfig('web_driver', 'store_url'));
    }
} 
