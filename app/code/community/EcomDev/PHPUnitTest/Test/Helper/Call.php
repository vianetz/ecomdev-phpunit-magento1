<?php
/**
 * PHP Unit test suite for Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   EcomDev
 * @package    EcomDev_PHPUnit
 * @copyright  Copyright (c) 2013 EcomDev BV (http://www.ecomdev.org)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author     Ivan Chepurnyi <ivan.chepurnyi@ecomdev.org>
 */

class EcomDev_PHPUnitTest_Test_Helper_Call extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @var EcomDev_PHPUnit_HelperInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $helper;

    /**
     * Creates new helper for test
     */
    protected function setUp(): void
    {
        $this->helper = $this->getMockForAbstractClass('EcomDev_PHPUnit_HelperInterface');
        $this->helper->expects($this->any())
            ->method('invoke')
            ->with($this->equalTo('someCustomHelper'))
            ->will($this->returnSelf());

        $this->helper->expects($this->any())
            ->method('has')
            ->will($this->returnValueMap(array(
                array('someCustomHelper', true)
            )));

        EcomDev_PHPUnit_Helper::add($this->helper);
    }

    /**
     * Testing calling of helper via test case
     *
     */
    public function testCall()
    {
        $this->assertSame(
            $this->helper, $this->someCustomHelper()
        );
    }

    public function testCallError()
    {
        $this->expectException(ErrorException::class);
        $this->expectExceptionMessage('Call to undefined function EcomDev_PHPUnitTest_Test_Helper_Call->unknownHelper()');

        $this->unknownHelper('');
    }

    public function tearDown(): void
    {
        EcomDev_PHPUnit_Helper::remove($this->helper);
    }
}
