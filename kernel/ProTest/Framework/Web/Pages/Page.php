<?php

namespace ProTest\Framework\Web\Pages;
use ProTest\Framework\Config;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\Remote\RemoteWebElement;
use Facebook\WebDriver\WebDriverElement;
use Facebook\WebDriver\WebDriverWait;
use Facebook\WebDriver\WebDriverSelect;
use Facebook\WebDriver\Exception\WebDriverException;

/**
 * Description of Page
 *
 * @author cerber
 */
class Page {    

    /**
     *
     * @var  RemoteWebDriver
     */
    protected $driver;
    
    protected $webElementsGetters = array();
    /**
    * Base URL
    *
    * @var  String
    */
    protected $storeUrl;

    /**
     * @findBy 'cssSelector'
     * @var WebDriverBy
     */
    protected $errorMessage = '#lc-error .text';

    /**
     * @findBy 'cssSelector'
     * @var WebDriverBy
     */
    protected $errorMessagePopup = '.error';

    /**
     * @findBy 'cssSelector'
     * @var WebDriverBy
     */
    protected $bodyTextarea = ".fr-view";

    protected $ignoredLinks = array(
        'target=login',
        'action=change_language',
        'action=quick_data',
        'action=clear_cache',
        'action=rebuild_view_lists',
        'action=remove_product_filter_cache'
    );

    protected $uniqueParams = array(
        'target',
        'action'
    );

    public $areaEndPoint = '';

    protected $jsErrorMessage = '';
    
    public function getConfig($section, $key)
    {
        return Config::getInstance()->getOptions($section, $key);
    }
    
    public function initializeComponents()
    {
        $reflectionClass = new \ReflectionClass(get_class($this));
        $properties = $reflectionClass->getProperties(\ReflectionProperty::IS_PROTECTED);
        
        foreach ($properties as $property) {
            $propertyAnnotation = $property->getDocComment();
            $propertyName = $property->getName();
            
            $mathes = array();
            if (1 == preg_match("/@findBy[ ]*'(.*)'/", $propertyAnnotation, $mathes)) {
                $type = $mathes[1];
                $this->$propertyName = WebDriverBy::$type($this->$propertyName);
                $this->webElementsGetters['get_' . $propertyName]=$propertyName;
            }
        }
    }
    
    public function __construct(RemoteWebDriver $driver, $storeUrl) {
        $this->initializeComponents();
        $this->driver = $driver;
        $this->storeUrl = $storeUrl;
    }
    
    public function load($autologin = false) {
        return true;
    }
    
    public function validate() {
        return false;
    }
    
    public function isErrorOnPage() {
        
        try {
            $this->waitForElement(10, $this->driver->findElement($this->errorMessagePopup));
            $this->driver->findElement($this->errorMessagePopup);
            return true;
        } catch (WebDriverException $e) {
            return false;
        }
        
    }

    public function isOopsErrorOnPage() {

        try {
            $this->driver->findElement($this->errorMessage);
            return true;
        } catch (WebDriverException $e) {
            return false;
        }

    }
    
    public function takeScreenshot($fileName='') {
        if ($fileName == '' ) {
            $fileName = "./" . date('h-i-s', time()) . "-" . time() . ".png";
        }
        $this->driver->takeScreenshot($fileName);
    }

    public function takeTestScreenshot($fileName) {
        $fileName = REDSOFT_TEST_ROOT . DIRECTORY_SEPARATOR . Config::getInstance()->getOptions('folder', 'new')
                    . DIRECTORY_SEPARATOR . $fileName;
        if (!file_exists($fileName)) {
            $this->takeScreenshot($fileName);
        }
    }

    public function getErrorMessageText() {
        if ($this->isOopsErrorOnPage()) {
            return $this->driver->findElement($this->errorMessage)->getText();
        } else {
            return '';
        }
    }

    public function getErrorText() {
        if ($this->isErrorOnPage()) {
            return $this->driver->findElement($this->errorMessagePopup)->getText();
        } else {
            return '';
        }
    }

    public function isJSErrorOnPage() {

        $log = $this->driver->manage()->getLog("browser");
        $errorMessageJsStr = '';

        foreach ($log as $message){
            if ($message['level'] == "SEVERE"){
                $errorMessageJsStr = $errorMessageJsStr . $message['message'] . ';';
            }
        }
        return $errorMessageJsStr;
    }

    public function getJSErrorMessage() {
        return $this->jsErrorMessage;
    }

    /**
     * 
     * @param WebDriverBy $by
     * @param RemoteWebElement $element
     * @return boolean
     */
    public function isElementPresent(WebDriverBy $by, RemoteWebElement $element = null) {
        if ($element == null) {
            $driver = $this->driver;
        } else {
            $driver = $element;
        }
        try {
            $el = $driver->findElement($by);
            return true;
        } catch (WebDriverException $e) {
            return false;
        }
    }
    
    public function elementClassNotDisabled(WebDriverBy $by, RemoteWebElement $element = null) {
        return function($driver) use ($by) {
            try {
                $el = $driver->findElement($by);
            } catch (WebDriverException $e) {
                return null;
            }
            if(strpos($el->getAttribute('class'), 'disabled') == FALSE) {
                return true;
            } else {
                return false;
            }
        };
    }

    public function waitForAjax($timeout=30 ,WebDriverBy $element = null) {
        if ($element == null) {
            $element = WebDriverBy::cssSelector('div.block-wait');
        } 
        if ($timeout <= 0) {
            $timeout = 1;
        }
        
        $timeout = $timeout * 2;
        
        while ($timeout > 0) {
            usleep(500000);
            if (!$this->isElementPresent($element)) {
                return $this;
            }
            $timeout--;
        }
        throw new \Exception('Ajax wait timeout');
    }

    public function waitForElement($timeout=30 ,WebDriverElement $element = null) {
        if ($element == null) {
            throw new \Exception('Web element is not presented.');
        } 
        
        $wait = new WebDriverWait($this->driver, $timeout, 500); 
        $wait->until(WebDriverExpectedCondition::visibilityOf($element), "Web element is not presented.");
    }

    public function waitForElementIsClickable($timeout=30 ,WebDriverBy $element = null) {
        if ($element == null) {
            throw new \Exception('Web element is not presented.');
        }

        $wait = new WebDriverWait($this->driver, $timeout, 500);
        $wait->until(WebDriverExpectedCondition::elementToBeClickable($element), "Web element is not clickable.");
    }
    public function __get($name) {
        if (isset($this->webElementsGetters[$name])) {
            $propertyName = $this->webElementsGetters[$name];
            $by = $this->$propertyName;
            return $this->driver->findElement($by);
        }
        throw new \Exception('Unknown property.');
    }
    
    protected function createComponent($path)
    {
        $className = '\\ProTest\\Framework\\Web\\Pages' . $path;

        return new $className($this->driver, $this->storeUrl);
    }
    
    public function fillForm($data)
    {
    }

    public function getUniqueLinks($links=array())
    {
        $config = Config::getInstance();

        $allLinks = $this->driver->findElements(WebDriverBy::cssSelector('a'));

        foreach ($allLinks as $link) {
            try {
                $href = $link->getAttribute('href');
            } catch (\Exception $e) {
                continue;
            }

            $query = parse_url($href, PHP_URL_QUERY);
            $params = explode('#', $query);
            $params = $params[0];
            $params = explode('&', $params);
            $self = $this;
            $params = array_filter($params, function($value) use($self) {
                $param = explode('=', $value);
                return in_array($param[0], $self->uniqueParams);
            });
            sort($params);
            $paramsHash = md5(implode('&', $params));

            if (
                !in_array($href, $links['links'])
                && !in_array($paramsHash, $links['hashes'])
                && 0 === strpos($href, $config->getOptions('web_driver', 'store_url') . $this->areaEndPoint)
            ) {
                $isForbbidenLink = false;
                foreach ($self->ignoredLinks as $ignoredLink) {
                    if (in_array($ignoredLink, explode('&', $query))) {
                        $isForbbidenLink = true;
                        break;
                    }
                }

                if (!$isForbbidenLink) {
                    $links['links'][] = $href;
                    $links['hashes'][] = $paramsHash;
                }
            }
        }

        return $links;
    }

    public function checkForm($data)
    {
        $failedElements = array();
        foreach ($data as $element=>$value) {
            $methodName = 'check' . ucfirst(str_replace('-', '_', $element));
            if (method_exists($this, $methodName)) {
                if (true !== $this->$methodName($value)) {
                    $failedElements[] = $element;
                }
            } else {
                $by = WebDriverBy::cssSelector('#' . $element);
                $webElement = $this->driver->findElement($by);
                $tag = $this->driver->findElement($by)->getTagName();

                if ($tag == 'select') {
                    $selectElm = new WebDriverSelect($webElement);

                    if ($selectElm->isMultiple()) {
                        foreach ($value as &$items) {
                            if (!is_string($items)){
                                $items = $this->getOptionByValue($items,$element);
                            }
                        }
                        $selectedElements = $selectElm->getAllSelectedOptions();
                        $selectedElementsValues = array();
                        foreach ($selectedElements as $messages) {
                            $selectedElementsValues[] = $messages->getText();
                        }
                        if (array_diff($selectedElementsValues, $value)
                            || array_diff($value, $selectedElementsValues)
                        ) {
                            $failedElements[] = $element;
                        }
                    } else {
                        if ($selectElm->getFirstSelectedOption()->getAttribute('value') != $value) {
                            $failedElements[] = $element;
                        }
                    }
                } elseif ($tag == 'textarea') {
                    if ($webElement->getAttribute('value') != $value) {
                        $failedElements[] = $element;
                    }
                } elseif ($tag == 'input' && 'checkbox' != $webElement->getAttribute('type')) {
                    if ($webElement->getAttribute('value') != $value) {
                        $failedElements[] = $element;
                    }
                } elseif ($tag == 'input' && 'checkbox' == $webElement->getAttribute('type')) {
                    if (($webElement->getAttribute('checked') && $value == 0)
                        || (!$webElement->getAttribute('checked') && $value == 1)) {
                        $failedElements[] = $element;
                    }
                } else {
                    if ($value != $webElement->getText()) {
                        $failedElements[] = $element;
                    }
                }
            }
        }

        return empty($failedElements)
            ? true
            : implode(', ', $failedElements);

    }

    public function inputTextIframe($text,$bodyIdIframe){
        $tinymceFrame = $this->driver->findElement($bodyIdIframe);
        $this->driver->switchTo()->frame($tinymceFrame);
        $this->driver->findElement($this->bodyTextarea)->sendKeys($text);
        $this->driver->switchTo()->defaultContent();
    }

    public function checkTextIframe($text,$idTextarea = null, $nameTextarea = null){
        $textareaXpath = '//textarea'
            . ($nameTextarea ? "[@name=\"$nameTextarea\"]" : "[@id=\"$idTextarea\"]")
            . "[.='<p>$text</p>']";

        $textareaLink = WebDriverBy::xpath($textareaXpath);
        return $this->isElementPresent($textareaLink);
    }

    public function getOptionByValue($value,$element){
        $elementOptionByvalue = WebDriverBy::cssSelector("#$element option[value='$value']");
        return $this->driver->findElement($elementOptionByvalue)->getText();
    }
    
    /**
     * Save HTML content of the current page for further check via W3C validator
     * 
     * @param string $fileName
     */
    public function savePageContent($fileName) {
    	$fileName = REDSOFT_TEST_ROOT . DIRECTORY_SEPARATOR . Config::getInstance()->getOptions('folder', 'html')
    	. DIRECTORY_SEPARATOR . $fileName;

    	if (!file_exists($fileName)) {
    		file_put_contents($fileName, $this->driver->getPageSource());
    	}
    }
}
