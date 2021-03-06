<?php

/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Filter
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: FilterTest.php 12770 2008-11-22 16:12:23Z matthew $
 */


/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../TestHelper.php';

/**
 * @see Zend_Filter
 */
require_once 'Zend/Filter.php';


/**
 * @category   Zend
 * @package    Zend_Filter
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_FilterTest extends PHPUnit_Framework_TestCase
{
    /**
     * Zend_Filter object
     *
     * @var Zend_Filter
     */
    protected $_filter;

    /**
     * Creates a new Zend_Filter object for each test method
     *
     * @return void
     */
    public function setUp()
    {
        $this->error   = null;
        $this->_filter = new Zend_Filter();
    }

    /**
     * Ensures expected return value from empty filter chain
     *
     * @return void
     */
    public function testEmpty()
    {
        $value = 'something';
        $this->assertEquals($value, $this->_filter->filter($value));
    }

    /**
     * Ensures that filters are executed in the expected order (FIFO)
     *
     * @return void
     */
    public function testFilterOrder()
    {
        $this->_filter->addFilter(new Zend_FilterTest_LowerCase())
                      ->addFilter(new Zend_FilterTest_StripUpperCase());
        $value = 'AbC';
        $valueExpected = 'abc';
        $this->assertEquals($valueExpected, $this->_filter->filter($value));
    }

    /**
     * Ensures that we can call the static method get()
     * to instantiate a named validator by its class basename
     * and it returns the result of filter() with the input.
     */
    public function testStaticFactory()
    {
        $filteredValue = Zend_Filter::get('1a2b3c4d', 'Digits');
        $this->assertEquals('1234', $filteredValue);
    }

    /**
     * Ensures that a validator with constructor arguments can be called
     * with the static method get().
     */
    public function testStaticFactoryWithConstructorArguments()
    {
        // Test HtmlEntities with one ctor argument.
        $filteredValue = Zend_Filter::get('"O\'Reilly"', 'HtmlEntities', array(ENT_COMPAT));
        $this->assertEquals('&quot;O\'Reilly&quot;', $filteredValue);

        // Test HtmlEntities with a different ctor argument,
        // and make sure it gives the correct response
        // so we know it passed the arg to the ctor.
        $filteredValue = Zend_Filter::get('"O\'Reilly"', 'HtmlEntities', array(ENT_QUOTES));
        $this->assertEquals('&quot;O&#039;Reilly&quot;', $filteredValue);
    }

    /**
     * Ensures that if we specify a validator class basename that doesn't
     * exist in the namespace, get() throws an exception.
     *
     * Refactored to conform with ZF-2724.
     *
     * @group  ZF-2724
     * @return void
     */
    public function testStaticFactoryClassNotFound()
    {
        set_error_handler(array($this, 'handleNotFoundError'), E_WARNING);
        try {
            Zend_Filter::get('1234', 'UnknownFilter');
        } catch (Zend_Filter_Exception $e) {
        }
        restore_error_handler();
        $this->assertTrue($this->error);
        $this->assertTrue(isset($e));
        $this->assertContains('Filter class not found', $e->getMessage());
    }

    /**
     * Handle file not found errors
     * 
     * @group  ZF-2724
     * @param  int $errnum 
     * @param  string $errstr 
     * @return void
     */
    public function handleNotFoundError($errnum, $errstr)
    {
        if (strstr($errstr, 'No such file')) {
            $this->error = true;
        }
    }
}


class Zend_FilterTest_LowerCase implements Zend_Filter_Interface
{
    public function filter($value)
    {
        return strtolower($value);
    }
}


class Zend_FilterTest_StripUpperCase implements Zend_Filter_Interface
{
    public function filter($value)
    {
        return preg_replace('/[A-Z]/', '', $value);
    }
}
