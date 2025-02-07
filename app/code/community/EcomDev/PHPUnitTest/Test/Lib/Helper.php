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

use EcomDev_PHPUnit_Helper as Helper;

class EcomDev_PHPUnitTest_Test_Lib_Helper extends \PHPUnit\Framework\TestCase
{
    /**
     * Preserved array of already set helpers,
     * to return them back when test case finished its run
     *
     * @var EcomDev_PHPUnit_HelperInterface[]
     */
    protected $initializedHelpers;


    protected function setUp(): void
    {
        // Retrieve existing helpers and store them for future revert
        $this->initializedHelpers = EcomDev_Utils_Reflection::getRestrictedPropertyValue(
            EcomDev_PHPUnit_Helper::class,
            'helpers'
        );

        $this->resetHelpers();
    }

    /**
     * Resets helpers for test
     */
    protected function resetHelpers()
    {
        EcomDev_Utils_Reflection::setRestrictedPropertyValue(EcomDev_PHPUnit_Helper::class, 'helpers', array());
    }

    /**
     * Returns amount of helpers for test
     *
     * @return EcomDev_PHPUnit_HelperInterface[]|\PHPUnit\Framework\MockObject\MockObject[]
     */
    protected function getHelpersForTest(int $count = 2, bool $setThem = false)
    {
        $result = array();

        $helperInterfaces = array(
            true => EcomDev_PHPUnit_HelperInterface::class,
            false => EcomDev_PHPUnit_Helper_ListenerInterface::class,
        );

        for ($i = 0; $i < $count; $i ++) {
            $helperInterface = $helperInterfaces[$i % 2 === 0];
            $result[] = $this->getMockForAbstractClass(
                $helperInterface, array(), 'Test_Helper_Name' . $i
            );
        }

        if ($setThem) {
            EcomDev_Utils_Reflection::setRestrictedPropertyValue(EcomDev_PHPUnit_Helper::class, 'helpers', $result);
        }

        return $result;
    }

    /**
     * Tests regular helper addition
     */
    public function testAdd(): void
    {
        $helpers = $this->getHelpersForTest(3);

        Helper::add($helpers[0]);
        Helper::add($helpers[1]);
        Helper::add($helpers[2]);

        $this->assertSame($helpers, Helper::get());
    }

    /**
     * Tests addition of helpers to static property
     */
    public function testAddOrdered(): void
    {
        $helpers = $this->getHelpersForTest(6);

        Helper::add($helpers[0]);
        Helper::add($helpers[1]);
        Helper::add($helpers[2], array('before' => $helpers[1]));

        $this->assertSame(array($helpers[0], $helpers[2], $helpers[1]), Helper::get());

        Helper::add($helpers[4], array('after' => $helpers[2]));

        $this->assertSame(array($helpers[0], $helpers[2], $helpers[4], $helpers[1]), Helper::get());

        Helper::add($helpers[3], array('before' => 'Test_Helper_Name2'));

        $this->assertSame(array($helpers[0], $helpers[3], $helpers[2], $helpers[4], $helpers[1]), Helper::get());

        Helper::add($helpers[5], array('after' => 'Test_Helper_Name3'));

        $this->assertSame(array($helpers[0], $helpers[3], $helpers[5], $helpers[2], $helpers[4], $helpers[1]), Helper::get());
    }

    public function testAddWrongPosition()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unknown position specified');

        $helper = current($this->getHelpersForTest(1));
        Helper::add($helper, array('unknown' => 'position'));
    }

    /**
     * Tests removal of each helper
     */
    public function testRemove()
    {
        $helpers = $this->getHelpersForTest(5, true);
        // Check helpers are exists before editing
        $this->assertSame($helpers, Helper::get());

        Helper::remove($helpers[1]);

        $this->assertSame(array($helpers[0], $helpers[2], $helpers[3], $helpers[4]), Helper::get());

        Helper::remove($helpers[0]);

        $this->assertSame(array($helpers[2], $helpers[3], $helpers[4]), Helper::get());

        Helper::remove($helpers[4]);

        $this->assertSame(array($helpers[2], $helpers[3]), Helper::get());

        Helper::remove($helpers[2]);
        Helper::remove($helpers[3]);

        $this->assertSame(array(), Helper::get());
    }

    /**
     * Tests removal of each helper
     */
    public function testRemoveByClassName()
    {
        $helpers = $this->getHelpersForTest(5, true);
        // Check helpers are exists before editing
        $this->assertSame($helpers, Helper::get());
        Helper::add($helpers[4]); // Added two times

        Helper::removeByClass('Test_Helper_Name2');

        $this->assertSame(array($helpers[0], $helpers[1], $helpers[3], $helpers[4], $helpers[4]), Helper::get());

        Helper::removeByClass('Test_Helper_Name4');

        $this->assertSame(array($helpers[0], $helpers[1], $helpers[3]), Helper::get());
    }

    /**
     * Tests getting of helper by action
     *
     */
    public function testGetByAction()
    {
        $helpers = $this->getHelpersForTest(3, true);

        // Should be used for firstName
        $helpers[0]->expects($this->any())
            ->method('has')
            ->will($this->returnValueMap(array(
                array('firstName', true),
                array('secondName', false),
                array('thirdName', false),
            )));

        // Should be used for thirdName
        $helpers[1]->expects($this->any())
            ->method('has')
            ->will($this->returnValueMap(array(
                array('firstName', false),
                array('secondName', false),
                array('thirdName', true),
            )));

        // Should be used for secondName
        $helpers[2]->expects($this->any())
            ->method('has')
            ->will($this->returnValueMap(array(
                array('firstName', false),
                array('secondName', true),
                array('thirdName', true),
            )));

        $this->assertSame($helpers[0], Helper::getByAction('firstName'));
        $this->assertSame($helpers[1], Helper::getByAction('thirdName'));
        $this->assertSame($helpers[2], Helper::getByAction('secondName'));
        $this->assertSame(false, Helper::getByAction('uknownName'));
    }


    /**
     * Creates invoke method tests stub
     *
     * @return EcomDev_PHPUnit_HelperInterface[]|\PHPUnit\Framework\MockObject\MockObject[]
     */
    protected function invokeStub()
    {
        $helpers = $this->getHelpersForTest(1, true);

        // Should be used for firstName
        $helpers[0]->expects($this->any())
            ->method('has')
            ->will($this->returnValueMap(array(
                array('firstName', true)
            )));

        // Invocation stub for firstName
        $helpers[0]->expects($this->any())
            ->method('invoke')
            ->will($this->returnValueMap(array(
                array('firstName', array('one'), 'firstName_one'),
                array('firstName', array('one', 'two'), 'firstName_one_two'),
                array('firstName', array('one', 'two', 'three'), 'firstName_one_two_three')
            )));
        return $helpers;
    }

    /**
     * Tests invoking of helper by action
     */
    public function testInvokeArgs()
    {
        $this->invokeStub();

        $this->assertSame('firstName_one', Helper::invokeArgs('firstName', array('one')));
        $this->assertSame('firstName_one_two', Helper::invokeArgs('firstName', array('one', 'two')));
        $this->assertSame('firstName_one_two_three', Helper::invokeArgs('firstName', array('one', 'two', 'three')));

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Cannot find a helper for action "unknownName"');
        Helper::invokeArgs('unknownName', array('argument'));
    }

    /**
     * Tests invoking of helper by action
     */
    public function testInvoke()
    {
        $this->invokeStub();

        $this->assertSame('firstName_one', Helper::invoke('firstName', 'one'));
        $this->assertSame('firstName_one_two', Helper::invoke('firstName', 'one', 'two'));
        $this->assertSame('firstName_one_two_three', Helper::invoke('firstName', 'one', 'two', 'three'));
    }

    /**
     * Tests method for checking action existence in the helper
     */
    public function testHas()
    {
        $helpers = $this->getHelpersForTest(1, true);

        // Should be used for firstName
        $helpers[0]->expects($this->any())
            ->method('has')
            ->will($this->returnValueMap(array(
                array('firstName', true),
                array('secondName', true)
            )));

        $this->assertTrue(Helper::has('firstName'));
        $this->assertTrue(Helper::has('secondName'));
        $this->assertFalse(Helper::has('unknownName'));
    }

    /**
     * Test that setTestCase method was correctly invoked
     *
     */
    public function testSetTestCase()
    {
        $helpers = $this->getHelpersForTest(4, true);

        // Initialize mock for test
        foreach ($helpers as $helper) {
            $helper->expects($this->once())
                ->method('setTestCase')
                ->with(new \PHPUnit\Framework\Constraint\IsIdentical($this))
                ->will($this->returnSelf());
        }

        EcomDev_PHPUnit_Helper::setTestCase($this);
    }

    /**
     * Test that when set up is invoked,
     * test helpers that support setUp method invoked as well
     */
    public function testSetUp()
    {
        $helpers = $this->getHelpersForTest(4, true);

        $helpers[0]->expects($this->never())
            ->method($this->anything());
        $helpers[1]->expects($this->once())
            ->method('setUp');
        $helpers[2]->expects($this->never())
            ->method($this->anything());
        $helpers[3]->expects($this->once())
            ->method('setUp');

        EcomDev_PHPUnit_Helper::setUp();
    }

    /**
     * Test that when tear down is invoked,
     * test helpers that support tearDown method invoked as well
     */
    public function testTearDown()
    {
        $helpers = $this->getHelpersForTest(4, true);

        $helpers[0]->expects($this->never())
            ->method($this->anything());
        $helpers[1]->expects($this->once())
            ->method('tearDown');
        $helpers[2]->expects($this->never())
            ->method($this->anything());
        $helpers[3]->expects($this->once())
            ->method('tearDown');

        EcomDev_PHPUnit_Helper::tearDown();
    }

    protected function tearDown(): void
    {
        // Revert helpers in helper class
        EcomDev_Utils_Reflection::setRestrictedPropertyValue(
            EcomDev_PHPUnit_Helper::class,
            'helpers',
            $this->initializedHelpers
        );
    }
}
