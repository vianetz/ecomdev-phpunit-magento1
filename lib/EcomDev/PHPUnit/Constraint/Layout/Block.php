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

/**
 * Constraint related to main layout block functionality
 *
 */
class EcomDev_PHPUnit_Constraint_Layout_Block extends EcomDev_PHPUnit_Constraint_AbstractLayout
{
    const TYPE_CREATED = 'created';
    const TYPE_REMOVED = 'removed';
    const TYPE_RENDERED = 'rendered';
    const TYPE_RENDERED_CONTENT = 'rendered_content';
    const TYPE_TYPE = 'type';
    const TYPE_INSTANCE_OF = 'instance_of';
    const TYPE_AFTER = 'after';
    const TYPE_BEFORE = 'before';
    const TYPE_PARENT_NAME = 'parent_name';
    const TYPE_ROOT_LEVEL = 'root_level';

    const ACTION_BLOCK_CREATED = EcomDev_PHPUnit_Constraint_Layout_LoggerInterface::ACTION_BLOCK_CREATED;
    const ACTION_BLOCK_RENDERED = EcomDev_PHPUnit_Constraint_Layout_LoggerInterface::ACTION_BLOCK_RENDERED;
    const ACTION_BLOCK_REMOVED = EcomDev_PHPUnit_Constraint_Layout_LoggerInterface::ACTION_BLOCK_REMOVED;

    /**
     * Block name for constraint
     *
     * @var string
     */
    protected $_blockName = null;

    /**
     * Constraint related to main layout block functionality
     *
     * @param string $blockName
     * @param string $type
     * @param string|null $expectedValue
     * @throws \PHPUnit\Framework\Exception
     */
    public function __construct($blockName, $type, $expectedValue = null)
    {
        if (empty($blockName) || !is_string($blockName)) {
            throw EcomDev_PHPUnit_Helper::createInvalidArgumentException(1, 'string', $blockName);
        }

        $this->_blockName = $blockName;

        $this->_expectedValueValidation += array(
            self::TYPE_TYPE => array(true, 'is_string', 'string'),
            self::TYPE_INSTANCE_OF => array(true, 'is_string', 'string'),
            self::TYPE_AFTER => array(true, 'is_string', 'string'),
            self::TYPE_BEFORE => array(true, 'is_string', 'string'),
            self::TYPE_PARENT_NAME => array(true, 'is_string', 'string'),
            self::TYPE_RENDERED_CONTENT => array(true)
        );

        $this->_typesWithDiff[] = self::TYPE_TYPE;
        $this->_typesWithDiff[] = self::TYPE_AFTER;
        $this->_typesWithDiff[] = self::TYPE_BEFORE;
        $this->_typesWithDiff[] = self::TYPE_PARENT_NAME;
        parent::__construct($type, $expectedValue);
    }

    /**
     * Evaluates that layout block was created
     *
     * @param EcomDev_PHPUnit_Constraint_Layout_LoggerInterface $other
     * @return boolean
     */
    protected function evaluateCreated($other)
    {
        return $other->findFirst(self::ACTION_BLOCK_CREATED, $this->_blockName);
    }

    /**
     * Text representation of block is created assertion
     *
     * @return string
     */
    protected function textCreated()
    {
        return sprintf('block "%s" is created', $this->_blockName);
    }

    /**
     * Evaluates that layout block was rendered
     *
     * @param EcomDev_PHPUnit_Constraint_Layout_LoggerInterface $other
     * @return boolean
     */
    protected function evaluateRendered($other)
    {
        return $other->findFirst(self::ACTION_BLOCK_RENDERED, $this->_blockName) !== false;
    }

    /**
     * Text representation of block is rendered assertion
     *
     * @return string
     */
    protected function textRendered()
    {
        return sprintf('block "%s" is rendered', $this->_blockName);
    }

    /**
     * Evaluates that layout block rendered is evaluated by expected constraint
     *
     * @param EcomDev_PHPUnit_Constraint_Layout_LoggerInterface $other
     * @return boolean
     */
    protected function evaluateRenderedContent($other)
    {
        $renderInfo = $other->findFirst(self::ACTION_BLOCK_RENDERED, $this->_blockName);

        if (!$renderInfo) {
            $this->setActualValue(false);
            return false;
        }

        $this->setActualValue($renderInfo['content']);
        return $this->_expectedValue->evaluate($renderInfo['content'], '', true);
    }

    /**
     * Text representation of block rendered is evaluated by expected constraint assertion
     *
     * @return string
     */
    protected function textRenderedContent()
    {
        return sprintf('block "%s" rendered content %s',
                       $this->_blockName, $this->_expectedValue->toString());
    }

    /**
     * Evaluates that layout block was removed
     *
     * @param EcomDev_PHPUnit_Constraint_Layout_LoggerInterface $other
     * @return boolean
     */
    protected function evaluateRemoved($other)
    {
        // Set possible block creation record for failure
        $this->setActualValue(
            $other->findFirst(self::ACTION_BLOCK_CREATED, $this->_blockName)
        );

        return $this->_actualValue === false
            && $other->findFirst(self::ACTION_BLOCK_REMOVED, $this->_blockName) !== false;
    }

    /**
     * Text representation of block is removed assertion
     *
     * @return string
     */
    protected function textRemoved()
    {
        return sprintf('block "%s" is removed', $this->_blockName);
    }

    /**
     * Evaluates that layout block was placed after expected one
     */
    protected function evaluateAfter(EcomDev_PHPUnit_Constraint_Layout_LoggerInterface $other): bool
    {
        // blockName = block to be placed after
        // expectedValue = block to be placed before
        $positionInfo = $other->getBlockPosition($this->_blockName);
        $this->setActualValue($positionInfo['before']);

        // assure that expected block is contained in the array of blocks positioned **before** the inspected block
        return in_array($this->_expectedValue, $this->_actualValue);
    }

    /**
     * Text representation of layout block is placed after another
     *
     * @return string
     */
    protected function textAfter()
    {
        return sprintf('block "%s" is placed after "%s"', $this->_blockName, $this->_expectedValue);
    }

    /**
     * Evaluates that layout block was placed before expected one
     */
    protected function evaluateBefore(EcomDev_PHPUnit_Constraint_Layout_LoggerInterface $other): bool
    {
        // blockName = block to be placed after
        // expectedValue = block to be placed before
        $positionInfo = $other->getBlockPosition($this->_blockName);
        $this->setActualValue($positionInfo['after']);

        // assure that expected block is contained in the array of blocks positioned **after** the inspected block
        return in_array($this->_expectedValue, $this->_actualValue);
    }

    /**
     * Text representation of layout block is placed before another
     *
     * @return string
     */
    protected function textBefore()
    {
        return sprintf('block "%s" is placed before "%s"', $this->_blockName, $this->_expectedValue);
    }

    /**
     * Evaluates that layout block is a type of expected
     *
     * @param EcomDev_PHPUnit_Constraint_Layout_LoggerInterface $other
     * @return boolean
     */
    protected function evaluateType($other)
    {
        $blockInfo = $other->findFirst(self::ACTION_BLOCK_CREATED, $this->_blockName);
        if ($blockInfo === false) {
            $this->setActualValue(false);
            return false;
        }

        $this->setActualValue($blockInfo['type']);
        return $blockInfo['type'] === $this->_expectedValue;
    }

    /**
     * Text represetation of block type constraint
     *
     * @return string
     */
    protected function textType()
    {
        return sprintf('block "%s" is a type of "%s"', $this->_blockName, $this->_expectedValue);
    }

    /**
     * Evaluates that layout block is an instance of expected class/interface
     *
     * @param EcomDev_PHPUnit_Constraint_Layout_LoggerInterface $other
     * @return boolean
     */
    protected function evaluateInstanceOf($other)
    {
        $blockInfo = $other->findFirst(self::ACTION_BLOCK_CREATED, $this->_blockName);
        if ($blockInfo === false) {
            $this->setActualValue(false);
            return false;
        }

        $this->setActualValue($blockInfo['class']);
        $actualReflection = EcomDev_Utils_Reflection::getReflection($this->_actualValue);
        return $this->_actualValue === $this->_expectedValue
               || $actualReflection->isSubclassOf($this->_expectedValue);
    }

    /**
     * Text represetation of block instance of constraint
     *
     * @return string
     */
    protected function textInstanceOf()
    {
        return sprintf('block "%s" is an instance of %s', $this->_blockName, $this->_expectedValue);
    }

    /**
     * Evaluates that layout block is a root level block
     *
     * @param EcomDev_PHPUnit_Constraint_Layout_LoggerInterface $other
     * @return boolean
     */
    protected function evaluateRootLevel($other)
    {
        $blockInfo = $other->findFirst(self::ACTION_BLOCK_CREATED, $this->_blockName);
        if ($blockInfo === false) {
            return false;
        }

        return $blockInfo['is_root'] === true;
    }

    /**
     * Text representation of a root level block assertion
     *
     * @return string
     */
    protected function textRootLevel()
    {
        return sprintf('block "%s" is a root level one', $this->_blockName);
    }

    /**
     * Evaluates that layout block is a child block of expected one
     *
     * @param EcomDev_PHPUnit_Constraint_Layout_LoggerInterface $other
     * @return boolean
     */
    protected function evaluateParentName($other)
    {
        $this->setActualValue(
            $other->getBlockParent($this->_blockName)
        );

        return $this->_actualValue === $this->_expectedValue;
    }

    /**
     * Text representation of a root level block assertion
     *
     * @return string
     */
    protected function textParentName()
    {
        return sprintf('block "%s" is a child of expected block', $this->_blockName);
    }
}
