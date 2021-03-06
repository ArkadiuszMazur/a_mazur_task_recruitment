<?php
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Amf_Value_MessageBodyTest::main');
}

require_once dirname(__FILE__) . '/../../../TestHelper.php';
require_once 'Zend/Amf/Value/MessageBody.php';

/**
 * Test case for Zend_Amf_Value_MessageBody
 *
 * @package Zend_Amf
 * @subpackage UnitTests
 * @version $Id: MessageBodyTest.php 12004 2008-10-18 14:29:41Z mikaelkael $
 */
class Zend_Amf_Value_MessageBodyTest extends PHPUnit_Framework_TestCase
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("Zend_Amf_Value_MessageBodyTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    public function setUp()
    {
        $this->body = new Zend_Amf_Value_MessageBody('/foo', '/bar', 'data');
    }

    public function testMessageBodyShouldAllowSettingData()
    {
        $this->assertEquals('data', $this->body->getData());
        $this->body->setData('foobar');
        $this->assertEquals('foobar', $this->body->getData());
    }

    public function testMessageBodyShouldAttachDataAsIs()
    {
        $object = new Zend_Amf_Value_MessageBodyTest_SerializableData();
        $this->body->setData($object);
        $this->assertSame($object, $this->body->getData());
    }

    public function testReplyMethodShouldModifyTargetUri()
    {
        $this->body->setReplyMethod('?action=bar');
        $this->assertEquals('/foo?action=bar', $this->body->getTargetUri());
    }

    public function testReplyMethodShouldInsertPathSeparatorIfNoQueryStringProvided()
    {
        $this->body->setReplyMethod('bar');
        $this->assertEquals('/foo/bar', $this->body->getTargetUri());
    }

    public function testPassingNullToTargetUriShouldResultInEmptyString()
    {
        $this->body->setTargetUri(null);
        $this->assertSame('', $this->body->getTargetUri());
    }
}

class Zend_Amf_Value_MessageBodyTest_SerializableData
{
    public function __toString()
    {
        return __CLASS__;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Amf_Value_MessageBodyTest::main') {
    Zend_Amf_Value_MessageBodyTest::main();
}
