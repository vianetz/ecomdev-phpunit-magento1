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
 * Base for controller test case
 *
 */
abstract class EcomDev_PHPUnit_Test_Case_Controller extends EcomDev_PHPUnit_Test_Case
{
    /**
     * Cookies container
     *
     * @var Zend_Http_CookieJar
     */
    protected static $_cookies = null;

    /**
     * Returns cookies container,
     * that processes them
     *
     * @return Zend_Http_CookieJar
     */
    protected static function getCookies()
    {
        if (self::$_cookies === null) {
            self::$_cookies = new Zend_Http_CookieJar();
        }

        return self::$_cookies;
    }

    /**
     * Returns request object
     *
     * @return EcomDev_PHPUnit_Controller_Request_Http
     */
    protected static function getRequest()
    {
        return self::app()->getRequest();
    }

	/**
     * Returns response object
     *
     * @return EcomDev_PHPUnit_Controller_Response_Http
     */
    protected static function getResponse()
    {
        return self::app()->getResponse();
    }

    /**
     * Returns layout model
     *
     * @return EcomDev_PHPUnit_Model_Layout
     */
    protected static function getLayout()
    {
        return self::app()->getLayout();
    }

    /**
     * Layout main functions constraint
     *
     * @param string $type
     * @return EcomDev_PHPUnit_Constraint_Layout
     */
    public static function layout($type)
    {
        return new EcomDev_PHPUnit_Constraint_Layout($type);
    }

    /**
     * Layout handle functionality constraint
     *
     * @param string $handle handle name
     * @param string $type
     * @param string|null $position another handle for position check
     * @return EcomDev_PHPUnit_Constraint_Layout_Handle
     */
    public static function layoutHandle($handle, $type, $position = null)
    {
        return new EcomDev_PHPUnit_Constraint_Layout_Handle($handle, $type, $position);
    }

    /**
     * Layout block functionality constraint
     *
     * @param string $blockName
     * @param string $type
     * @param string|null $expectedValue
     * @return EcomDev_PHPUnit_Constraint_Layout_Block
     */
    public static function layoutBlock($blockName, $type, $expectedValue = null)
    {
        return new EcomDev_PHPUnit_Constraint_Layout_Block($blockName, $type, $expectedValue);
    }

    /**
     * Layout block action calls functionality constraint
     *
     * @param string $blockName
     * @param string $method
     * @param string $type
     * @param int|null $invocationCount
     * @param array|null $arguments
     * @param string $searchType
     * @return EcomDev_PHPUnit_Constraint_Layout_Block_Action
     */
    public static function layoutBlockAction($blockName, $method, $type, $invocationCount = null,
        array $arguments = null, $searchType = EcomDev_PHPUnit_Constraint_Layout_Block_Action::SEARCH_TYPE_AND)
    {
        return new EcomDev_PHPUnit_Constraint_Layout_Block_Action(
            $blockName, $method, $type, $invocationCount, $arguments, $searchType
        );
    }

    /**
     * Layout block property constraint creation
     *
     * @param string $blockName
     * @param string $propertyName
     * @param \PHPUnit\Framework\Constraint\Constraint $constraint
     * @return EcomDev_PHPUnit_Constraint_Layout_Block_Property
     */
    public static function layoutBlockProperty($blockName, $propertyName, \PHPUnit\Framework\Constraint\Constraint $constraint)
    {
        return new EcomDev_PHPUnit_Constraint_Layout_Block_Property($blockName, $propertyName, $constraint);
    }

    /**
     * Controller request constraint creation
     *
     *
     * @param string $type
     * @param string|null $expectedValue
     * @return EcomDev_PHPUnit_Constraint_Controller_Request
     */
    public static function request($type, $expectedValue = null)
    {
        return new EcomDev_PHPUnit_Constraint_Controller_Request($type, $expectedValue);
    }

    /**
     * Controller response header constraint creation
     *
     * @param string $type
     * @param string $headerName
     * @param \PHPUnit\Framework\Constraint\Constraint|null $constraint
     * @return EcomDev_PHPUnit_Constraint_Controller_Response_Header
     */
    public static function responseHeader($headerName, $type, \PHPUnit\Framework\Constraint\Constraint $constraint = null)
    {
        return new EcomDev_PHPUnit_Constraint_Controller_Response_Header($headerName, $type, $constraint);
    }

    /**
     * Controller response body constraint creation
     *
     * @param \PHPUnit\Framework\Constraint\Constraint $constraint
     * @return EcomDev_PHPUnit_Constraint_Controller_Response_Body
     */
    public static function responseBody(\PHPUnit\Framework\Constraint\Constraint $constraint)
    {
        return new EcomDev_PHPUnit_Constraint_Controller_Response_Body($constraint);
    }

    /**
     * Assert that controller request matches assertion type
     *
     * @param string $type type of assertion
     * @param string|null $expectedValue
     * @param string $message
     */
    public static function assertRequest($type, $expectedValue = null, $message = '')
    {
        self::assertThat(
            self::getRequest(),
            self::request($type, $expectedValue),
            $message
        );
    }

    /**
     * Assert that controller request does not matches assertion type
     *
     * @param string $type type of assertion
     * @param string|null $expectedValue
     * @param string $message
     */
    public static function assertRequestNot($type, $expectedValue = null, $message = '')
    {
        self::assertThat(
            self::getRequest(),
            self::logicalNot(
                self::request($type, $expectedValue)
            ),
            $message
        );
    }

    /**
     * Assert that controller request is dispatched
     *
     * @param string $message
     */
    public static function assertRequestDispatched($message = '')
    {
        self::assertRequest(
            EcomDev_PHPUnit_Constraint_Controller_Request::TYPE_DISPATCHED,
            null, $message
        );
    }

    /**
     * Assert that controller request is not dispatched
     *
     * @param string $message
     */
    public static function assertRequestNotDispatched($message = '')
    {
        self::assertRequestNot(
            EcomDev_PHPUnit_Constraint_Controller_Request::TYPE_DISPATCHED,
            null, $message
        );
    }

    /**
     * Assert that controller request is forwarded
     *
     * @param string $message
     */
    public static function assertRequestForwarded($message = '')
    {
        self::assertRequest(
            EcomDev_PHPUnit_Constraint_Controller_Request::TYPE_FORWARDED,
            null, $message
        );
    }

    /**
     * Assert that controller request is not forwarded
     *
     * @param string $message
     */
    public static function assertRequestNotForwarded($message = '')
    {
        self::assertRequestNot(
            EcomDev_PHPUnit_Constraint_Controller_Request::TYPE_FORWARDED,
            null, $message
        );
    }

    /**
     * Asserts that current request route is matched expected one
     *
     * @param string $expectedRoute
     * @param string $message
     */
    public static function assertRequestRoute($expectedRoute, $message = '')
    {
        self::assertRequest(
            EcomDev_PHPUnit_Constraint_Controller_Request::TYPE_ROUTE,
            $expectedRoute, $message
        );
    }

    /**
     * Asserts that current request route is not matched expected one
     *
     * @param string $expectedRoute
     * @param string $message
     */
    public static function assertRequestRouteNot($expectedRoute, $message = '')
    {
        self::assertRequestNot(
            EcomDev_PHPUnit_Constraint_Controller_Request::TYPE_ROUTE,
            $expectedRoute, $message
        );
    }

    /**
     * Asserts that current request route name is the same as expected
     *
     * @param string $expectedRouteName
     * @param string $message
     */
    public static function assertRequestRouteName($expectedRouteName, $message = '')
    {
        self::assertRequest(
            EcomDev_PHPUnit_Constraint_Controller_Request::TYPE_ROUTE_NAME,
            $expectedRouteName, $message
        );
    }

    /**
     * Asserts that current request route name is not the same as expected
     *
     * @param string $expectedRouteName
     * @param string $message
     */
    public static function assertRequestRouteNameNot($expectedRouteName, $message = '')
    {
        self::assertRequestNot(
            EcomDev_PHPUnit_Constraint_Controller_Request::TYPE_ROUTE_NAME,
            $expectedRouteName, $message
        );
    }

    /**
     * Asserts that current request controller name is the same as expected
     *
     * @param string $expectedControllerName
     * @param string $message
     */
    public static function assertRequestControllerName($expectedControllerName, $message = '')
    {
        self::assertRequest(
            EcomDev_PHPUnit_Constraint_Controller_Request::TYPE_CONTROLLER_NAME,
            $expectedControllerName, $message
        );
    }

    /**
     * Asserts that current request controller name is not the same as expected
     *
     * @param string $expectedControllerName
     * @param string $message
     */
    public static function assertRequestControllerNameNot($expectedControllerName, $message = '')
    {
        self::assertRequestNot(
            EcomDev_PHPUnit_Constraint_Controller_Request::TYPE_CONTROLLER_NAME,
            $expectedControllerName, $message
        );
    }

    /**
     * Asserts that current request controller module is the same as expected
     *
     * @param string $expectedControllerModule
     * @param string $message
     */
    public static function assertRequestControllerModule($expectedControllerModule, $message = '')
    {
        self::assertRequest(
            EcomDev_PHPUnit_Constraint_Controller_Request::TYPE_CONTROLLER_MODULE,
            $expectedControllerModule, $message
        );
    }

    /**
     * Asserts that current request controller name is not the same as expected
     *
     * @param string $expectedControllerModule
     * @param string $message
     */
    public static function assertRequestControllerModuleNot($expectedControllerModule, $message = '')
    {
        self::assertRequestNot(
            EcomDev_PHPUnit_Constraint_Controller_Request::TYPE_CONTROLLER_MODULE,
            $expectedControllerModule, $message
        );
    }

    /**
     * Asserts that current request action name is the same as expected
     *
     * @param string $expectedActionName
     * @param string $message
     */
    public static function assertRequestActionName($expectedActionName, $message = '')
    {
        self::assertRequest(
            EcomDev_PHPUnit_Constraint_Controller_Request::TYPE_ACTION_NAME,
            $expectedActionName, $message
        );
    }

    /**
     * Asserts that current request action name is not the same as expected
     *
     * @param string $expectedActionName
     * @param string $message
     */
    public static function assertRequestActionNameNot($expectedActionName, $message = '')
    {
        self::assertRequestNot(
            EcomDev_PHPUnit_Constraint_Controller_Request::TYPE_ACTION_NAME,
            $expectedActionName, $message
        );
    }

    /**
     * Asserts that current request before forwarded route is matched expected
     *
     * @param string $expectedBeforeForwardedRoute
     * @param string $message
     */
    public static function assertRequestBeforeForwardedRoute($expectedBeforeForwardedRoute, $message = '')
    {
        self::assertRequest(
            EcomDev_PHPUnit_Constraint_Controller_Request::TYPE_BEFORE_FORWARD_ROUTE,
            $expectedBeforeForwardedRoute, $message
        );
    }

    /**
     * Asserts that current request before forwarded route is not matched expected
     *
     * @param string $expectedBeforeForwardedRoute
     * @param string $message
     */
    public static function assertRequestBeforeForwardedRouteNot($expectedBeforeForwardedRoute, $message = '')
    {
        self::assertRequestNot(
            EcomDev_PHPUnit_Constraint_Controller_Request::TYPE_BEFORE_FORWARD_ROUTE,
            $expectedBeforeForwardedRoute, $message
        );
    }

    /**
     * Assert shortcut for response assertions
     *
     * @param \PHPUnit\Framework\Constraint\Constraint $constraint
     * @param string $message
     */
    public static function assertThatResponse(\PHPUnit\Framework\Constraint\Constraint $constraint, $message)
    {
        self::assertThat(self::getResponse(), $constraint, $message);
    }

    /**
     * Assert that response header is sent
     *
     * @param string $headerName
     * @param string $message
     */
    public static function assertResponseHeaderSent($headerName, $message = '')
    {
        self::assertThatResponse(
            self::responseHeader(
                $headerName,
                EcomDev_PHPUnit_Constraint_Controller_Response_Header::TYPE_SENT
            ),
            $message
        );
    }

    /**
     * Assert that response header is not sent
     *
     * @param string $headerName
     * @param string $message
     */
    public static function assertResponseHeaderNotSent($headerName, $message = '')
    {
        self::assertThatResponse(
            self::logicalNot(
                self::responseHeader(
                    $headerName,
                    EcomDev_PHPUnit_Constraint_Controller_Response_Header::TYPE_SENT
                )
            ),
            $message
        );
    }

    /**
     * Assert that response header is evaluated by a specified constraint
     *
     * @param string $headerName
     * @param \PHPUnit\Framework\Constraint\Constraint $constraint
     * @param string $message
     */
    public static function assertResponseHeader($headerName, \PHPUnit\Framework\Constraint\Constraint $constraint, $message = '')
    {
        self::assertThatResponse(
            self::responseHeader(
                $headerName,
                EcomDev_PHPUnit_Constraint_Controller_Response_Header::TYPE_CONSTRAINT,
                $constraint
            ),
            $message
        );
    }

    /**
     * Assert that response header is not evaluated by a specified constraint
     *
     * @param string $headerName
     * @param \PHPUnit\Framework\Constraint\Constraint $constraint
     * @param string $message
     */
    public static function assertResponseHeaderNot($headerName, \PHPUnit\Framework\Constraint\Constraint $constraint, $message = '')
    {
        self::assertThatResponse(
            self::responseHeader(
                $headerName,
                EcomDev_PHPUnit_Constraint_Controller_Response_Header::TYPE_CONSTRAINT,
                self::logicalNot($constraint)
            ),
            $message
        );
    }

    /**
     * Assert that response header is equal to expected value
     *
     * @param string $headerName
     * @param mixed  $expectedValue
     * @param string $message
     * @param int    $delta
     * @param int    $maxDepth
     * @param bool   $canonicalize
     * @param bool   $ignoreCase
     */
    public static function assertResponseHeaderEquals($headerName, $expectedValue, $message = '',
        $delta = 0, $maxDepth = 10, $canonicalize = false, $ignoreCase = false)
    {
        self::assertResponseHeader(
            $headerName,
            self::equalTo($expectedValue, $delta, $maxDepth, $canonicalize, $ignoreCase),
            $message
        );
    }

    /**
     * Assert that response header is not equal to expected value
     *
     * @param string $headerName
     * @param mixed  $expectedValue
     * @param string $message
     * @param int    $delta
     * @param int    $maxDepth
     * @param bool   $canonicalize
     * @param bool   $ignoreCase
     */
    public static function assertResponseHeaderNotEquals($headerName, $expectedValue, $message = '',
        $delta = 0, $maxDepth = 10, $canonicalize = false, $ignoreCase = false)
    {
        self::assertResponseHeaderNot(
            $headerName,
            self::equalTo($expectedValue, $delta, $maxDepth, $canonicalize, $ignoreCase),
            $message
        );
    }

    /**
     * Assert that response header is the same as expected value
     *
     * @param string $headerName
     * @param mixed $expectedValue
     * @param string $message
     */
    public static function assertResponseHeaderSame($headerName, $expectedValue, $message = '')
    {
        self::assertResponseHeader(
            $headerName,
            self::identicalTo($expectedValue),
            $message
        );
    }

    /**
     * Assert that response header is not the same as expected value
     *
     * @param string $headerName
     * @param mixed $expectedValue
     * @param string $message
     */
    public static function assertResponseHeaderNotSame($headerName, $expectedValue, $message = '')
    {
        self::assertResponseHeaderNot(
            $headerName,
            self::identicalTo($expectedValue),
            $message
        );
    }

    /**
     * Assert that response header contains expected string value
     *
     * @param string $headerName
     * @param string $expectedValue
     * @param string $message
     * @param boolean $ignoreCase
     */
    public static function assertResponseHeaderContains($headerName, $expectedValue, $message = '', $ignoreCase = true)
    {
        self::assertResponseHeader(
            $headerName,
            self::stringContains($expectedValue, $ignoreCase),
            $message
        );
    }

    /**
     * Assert that response header doesn't contain expected string value
     *
     * @param string $headerName
     * @param string $expectedValue
     * @param string $message
     * @param boolean $ignoreCase
     */
    public static function assertResponseHeaderNotContains($headerName, $expectedValue, $message = '', $ignoreCase = true)
    {
        self::assertResponseHeaderNot(
            $headerName,
            self::stringContains($expectedValue, $ignoreCase),
            $message
        );
    }

    /**
     * Assert that response header matches specified PCRE pattern
     *
     * @param string $headerName
     * @param string $pcrePattern
     * @param string $message
     */
    public static function assertResponseHeaderRegExp($headerName, $pcrePattern, $message = '')
    {
        self::assertResponseHeader(
            $headerName,
            self::matchesRegularExpression($pcrePattern),
            $message
        );
    }

    /**
     * Assert that response header doesn't match specified PCRE pattern
     *
     * @param string $headerName
     * @param string $pcrePattern
     * @param string $message
     */
    public static function assertResponseHeaderNotRegExp($headerName, $pcrePattern, $message = '')
    {
        self::assertResponseHeaderNot(
            $headerName,
            self::matchesRegularExpression($pcrePattern),
            $message
        );
    }

    /**
     * Assert that response body is evaluated by the constraint
     *
     * @param \PHPUnit\Framework\Constraint\Constraint $constraint
     * @param string $message
     */
    public static function assertResponseBody(\PHPUnit\Framework\Constraint\Constraint $constraint, $message = '')
    {
        self::assertThatResponse(
            self::responseBody($constraint),
            $message
        );
    }

    /**
     * Assert that response body is not evaluated by the constraint
     *
     * @param \PHPUnit\Framework\Constraint\Constraint $constraint
     * @param string $message
     */
    public static function assertResponseBodyNot(\PHPUnit\Framework\Constraint\Constraint $constraint, $message = '')
    {
        self::assertThatResponse(
            self::logicalNot(
                self::responseBody($constraint)
            ),
            $message
        );
    }

    /**
     * Assert that response body contains expected string
     *
     * @param string $expectedValue
     * @param string $message
     * @param boolean $ignoreCase
     */
    public static function assertResponseBodyContains($expectedValue, $message = '', $ignoreCase = true)
    {
        self::assertResponseBody(
            self::stringContains($expectedValue, $ignoreCase),
            $message
       );
    }

    /**
     * Assert that response body doen't contain expected string
     *
     * @param string $expectedValue
     * @param string $message
     * @param boolean $ignoreCase
     */
    public static function assertResponseBodyNotContains($expectedValue, $message = '', $ignoreCase = true)
    {
        self::assertResponseBodyNot(
            self::stringContains($expectedValue, $ignoreCase),
            $message
       );
    }

    /**
     * Assert that response body is matched by PCRE pattern
     *
     * @param string $pcrePattern
     * @param string $message
     */
    public static function assertResponseBodyRegExp($pcrePattern, $message = '')
    {
        self::assertResponseBody(
            self::matchesRegularExpression($pcrePattern),
            $message
       );
    }

     /**
     * Assert that response body is not matched by PCRE pattern
     *
     * @param string $pcrePattern
     * @param string $message
     */
    public static function assertResponseBodyNotRegExp($pcrePattern, $message = '')
    {
        self::assertResponseBodyNot(
            self::matchesRegularExpression($pcrePattern),
            $message
       );
    }

    /**
     * Assert that response body is valid JSON string
     *
     * @param string $message
     */
    public static function assertResponseBodyJson($message = '')
    {
        self::assertResponseBody(
            self::isJson(),
            $message
        );
    }

    /**
     * Assert that response body is not valid JSON string
     *
     * @param string $message
     */
    public static function assertResponseBodyNotJson($message = '')
    {
        self::assertResponseBody(
            self::logicalNot(self::isJson()),
            $message
        );
    }

    /**
     * Assert that response body is JSON and matches expected value,
     * Can accept different match type for matching logic.
     *
     * @param array $expectedValue
     * @param string $message
     * @param string $matchType
     */
    public static function assertResponseBodyJsonMatch(array $expectedValue, $message = '',
        $matchType = EcomDev_PHPUnit_Constraint_Json::MATCH_AND)
    {
        self::assertResponseBodyJson($message);
        self::assertResponseBody(
            self::matchesJson($expectedValue, $matchType),
            $message
        );
    }

    /**
     * Assert that response body is JSON and not matches expected value,
     * Can accept different match type for matching logic.
     *
     * @param array $expectedValue
     * @param string $message
     * @param string $matchType
     */
    public static function assertResponseBodyJsonNotMatch(array $expectedValue, $message = '',
        $matchType = EcomDev_PHPUnit_Constraint_Json::MATCH_AND)
    {
        self::assertResponseBodyJson($message);
        self::assertResponseBodyNot(
            self::matchesJson($expectedValue, $matchType),
            $message
        );
    }

    /**
     * Assert HTTP response code value
     *
     * @param int $code
     * @param string $message
     */
    public static function assertResponseHttpCode($code, $message = '')
    {
        self::assertEquals(
            $code,
            self::getResponse()->getHttpResponseCode(),
            $message
            . sprintf("\nFailed asserting that response code is equal to %d", $code)
        );
    }

    /**
     * Assert that controller response is redirect
     *
     * @param string $message
     * @param int|null $responseCode
     * @internal param int|null $code
     */
    public static function assertRedirect($message = '', $responseCode = null)
    {
        self::assertTrue(
            self::getResponse()->isRedirect(),
            $message . "\nFailed asserting that response is redirect"
        );

        if ($responseCode !== null) {
            self::assertResponseHttpCode($responseCode, $message);
        }
    }

    /**
     * Assert that controller response is not redirect
     *
     * @param string $message
     */
    public static function assertNotRedirect($message = '')
    {
        self::assertFalse(
            self::getResponse()->isRedirect(),
            $message . "\nFailed asserting that response is not redirect"
        );
    }

    /**
     * Assert that constroller response is a redirect
     * to a specific url
     *
     * @param string $route route path
     * @param array $params route params
     * @param string $message
     */
    public static function assertRedirectTo($route, array $params = array(), $message = '')
    {
        if (!isset($params['_store']) && strpos($route, EcomDev_PHPUnit_Model_App::AREA_ADMINHTML) !== false) {
            $params['_store'] = EcomDev_PHPUnit_Model_App::ADMIN_STORE_CODE;
        }

        if (isset($params['_store']) && $params['_store'] === EcomDev_PHPUnit_Model_App::ADMIN_STORE_CODE) {
            $urlModel = Mage::getModel('adminhtml/url');
        } else {
            $urlModel = Mage::getModel('core/url');
        }

        $url = $urlModel->getUrl($route, $params);
        self::assertRedirectToUrl($url, $message);
    }

    /**
     * Assert that constroller response redirect is equal
     * to a specific url
     *
     * @param string $url
     * @param string $message
     */
    public static function assertRedirectToUrl($url, $message = '')
    {
        self::assertRedirect($message);
        self::assertResponseHeaderEquals('Location', $url, $message);
    }

    /**
     * Assert that constroller response redirect url
     * starts with expected url part
     *
     * @param string $urlPart
     * @param string $message
     */
    public static function assertRedirectToUrlStartsWith($urlPart, $message = '')
    {
        self::assertRedirect($message);
        self::assertResponseHeader('Location',
            self::stringStartsWith($urlPart),
            $message
        );
    }

    /**
     * Assert that constroller response redirect url
     * contains expected url part
     *
     * @param string $urlPart
     * @param string $message
     */
    public static function assertRedirectToUrlContains($urlPart, $message = '')
    {
        self::assertRedirect($message);
        self::assertResponseHeaderContains('Location', $urlPart, $message);
    }

    /**
     * Assert that constroller response redirect url matches PRCE pattern
     *
     * @param string $pcrePattern route path
     * @param string $message
     */
    public static function assertRedirectToUrlRegExp($pcrePattern, $message = '')
    {
        self::assertRedirect($message);
        self::assertResponseHeaderRegExp('Location', $pcrePattern, $message);
    }


    /**
     * Assert shortcut for layout constaints
     *
     * @param \PHPUnit\Framework\Constraint\Constraint $constraint
     * @param string $message
     * @internal param \EcomDev_PHPUnit_Constraint_AbstractLayout|\PHPUnit\Framework\Constraint\Constraint $constaint
     */
    public static function assertThatLayout(\PHPUnit\Framework\Constraint\Constraint $constraint, $message)
    {
        self::assertThat(self::getLayout(), $constraint, $message);
    }

    /**
     * Assert that layout is loaded
     *
     * @param string $message
     */
    public static function assertLayoutLoaded($message = '')
    {
        self::assertThatLayout(
            self::layout(EcomDev_PHPUnit_Constraint_Layout::TYPE_LOADED),
            $message
        );
    }

    /**
     * Assert that layout is not loaded
     *
     * @param string $message
     */
    public static function assertLayoutNotLoaded($message = '')
    {
        self::assertThatLayout(
            self::logicalNot(
                self::layout(EcomDev_PHPUnit_Constraint_Layout::TYPE_LOADED)
            ),
            $message
        );
    }

    /**
     * Assert that layout is rendered
     *
     * @param string $message
     */
    public static function assertLayoutRendered($message = '')
    {
        self::assertThatLayout(
            self::layout(EcomDev_PHPUnit_Constraint_Layout::TYPE_RENDERED),
            $message
        );
    }

    /**
     * Assert that layout is not rendered
     *
     * @param string $message
     */
    public static function assertLayoutNotRendered($message = '')
    {
        self::assertThatLayout(
            self::logicalNot(
                self::layout(EcomDev_PHPUnit_Constraint_Layout::TYPE_RENDERED)
            ),
            $message
        );
    }

    /**
     * Assert that layout handle is loaded into layout updates
     *
     *
     * @param string $handle
     * @param string $message
     */
    public static function assertLayoutHandleLoaded($handle, $message = '')
    {
        self::assertThatLayout(
            self::layoutHandle(
                $handle, EcomDev_PHPUnit_Constraint_Layout_Handle::TYPE_LOADED
            ),
            $message
        );
    }

    /**
     * Assert that layout handle is not loaded into layout updates
     *
     *
     * @param string $handle
     * @param string $message
     */
    public static function assertLayoutHandleNotLoaded($handle, $message = '')
    {
        self::assertThatLayout(
            self::logicalNot(
                self::layoutHandle(
                    $handle, EcomDev_PHPUnit_Constraint_Layout_Handle::TYPE_LOADED
                )
            ),
            $message
        );
    }

    /**
     * Assert that layout handle is loaded into layout updates after expected one
     *
     *
     * @param string $handle
     * @param string $after
     * @param string $message
     */
    public static function assertLayoutHandleLoadedAfter($handle, $after, $message = '')
    {
        self::assertLayoutHandleLoaded($handle);
        self::assertThatLayout(
            self::layoutHandle(
                $handle, EcomDev_PHPUnit_Constraint_Layout_Handle::TYPE_LOADED_AFTER,
                $after
            ),
            $message
        );
    }

    /**
     * Assert that layout handle is loaded into layout updates after expected one
     *
     *
     * @param string $handle
     * @param string $before
     * @param string $message
     */
    public static function assertLayoutHandleLoadedBefore($handle, $before, $message = '')
    {
        self::assertLayoutHandleLoaded($handle);
        self::assertThatLayout(
            self::layoutHandle(
                $handle, EcomDev_PHPUnit_Constraint_Layout_Handle::TYPE_LOADED_BEFORE,
                $before
            ),
            $message
        );
    }

    /**
     * Assert that layout block is created via layout file
     *
     *
     * @param string $blockName
     * @param string $message
     */
    public static function assertLayoutBlockCreated($blockName, $message = '')
    {
        self::assertThatLayout(
            self::layoutBlock(
                $blockName, EcomDev_PHPUnit_Constraint_Layout_Block::TYPE_CREATED
            ),
            $message
        );
    }

    /**
     * Assert that layout block is removed via layout file
     *
     * @param string $blockName
     * @param string $message
     */
    public static function assertLayoutBlockRemoved($blockName, $message = '')
    {
        self::assertThatLayout(
            self::layoutBlock(
                $blockName, EcomDev_PHPUnit_Constraint_Layout_Block::TYPE_REMOVED
            ),
            $message
        );
    }

    /**
     * Assert that layout block is rendered
     *
     * @param string $blockName
     * @param string $message
     */
    public static function assertLayoutBlockRendered($blockName, $message = '')
    {
        self::assertThatLayout(
            self::layoutBlock(
                $blockName, EcomDev_PHPUnit_Constraint_Layout_Block::TYPE_RENDERED
            ),
            $message
        );
    }

    /**
     * Assert that layout block is not rendered
     *
     * @param string $blockName
     * @param string $message
     */
    public static function assertLayoutBlockNotRendered($blockName, $message = '')
    {
        self::assertThatLayout(
            self::logicalNot(
                self::layoutBlock(
                    $blockName, EcomDev_PHPUnit_Constraint_Layout_Block::TYPE_RENDERED
                )
            ),
            $message
        );
    }

    /**
     * Assert that layout block rendered content is evaluated by constraint
     *
     * @param string $blockName
     * @param \PHPUnit\Framework\Constraint\Constraint $constraint
     * @param string $message
     */
    public static function assertLayoutBlockRenderedContent($blockName,
    \PHPUnit\Framework\Constraint\Constraint $constraint, $message = '')
    {
        self::assertThatLayout(
            self::layoutBlock(
                $blockName,
                EcomDev_PHPUnit_Constraint_Layout_Block::TYPE_RENDERED,
                $constraint
            ),
            $message
        );
    }

    /**
     * Assert that layout block rendered content is not evaluated by constraint
     *
     * @param string $blockName
     * @param \PHPUnit\Framework\Constraint\Constraint $constraint
     * @param string $message
     */
    public static function assertLayoutBlockRenderedContentNot($blockName,
    \PHPUnit\Framework\Constraint\Constraint $constraint, $message = '')
    {
        self::assertThatLayout(
            self::layoutBlock(
                $blockName,
                EcomDev_PHPUnit_Constraint_Layout_Block::TYPE_RENDERED,
                self::logicalNot($constraint)
            ),
            $message
        );
    }

    /**
     * Assert that layout block type is a type of expected class alias
     *
     * @param string $blockName
     * @param string $classAlias
     * @param string $message
     */
    public static function assertLayoutBlockTypeOf($blockName, $classAlias, $message = '')
    {
        self::assertThatLayout(
            self::layoutBlock(
                $blockName,
                EcomDev_PHPUnit_Constraint_Layout_Block::TYPE_TYPE,
                $classAlias
            ),
            $message
        );
    }

    /**
     * Assert that layout block type is not a type of expected class alias
     *
     * @param string $blockName
     * @param string $classAlias
     * @param string $message
     */
    public static function assertLayoutBlockNotTypeOf($blockName, $classAlias, $message = '')
    {
        self::assertThatLayout(
            self::logicalNot(
                self::layoutBlock(
                    $blockName,
                    EcomDev_PHPUnit_Constraint_Layout_Block::TYPE_TYPE,
                    $classAlias
                )
            ),
            $message
        );
    }

    /**
     * Assert that layout block type is an instance of expected class
     *
     * @param string $blockName
     * @param string $className
     * @param string $message
     */
    public static function assertLayoutBlockInstanceOf($blockName, $className, $message = '')
    {
        self::assertThatLayout(
            self::layoutBlock(
                $blockName,
                EcomDev_PHPUnit_Constraint_Layout_Block::TYPE_INSTANCE_OF,
                $className
            ),
            $message
        );
    }

    /**
     * Assert that layout block type is an instance of expected class
     *
     * @param string $blockName
     * @param string $className
     * @param string $message
     */
    public static function assertLayoutBlockNotInstanceOf($blockName, $className, $message = '')
    {
        self::assertThatLayout(
            self::logicalNot(
                self::layoutBlock(
                    $blockName,
                    EcomDev_PHPUnit_Constraint_Layout_Block::TYPE_INSTANCE_OF,
                    $className
                )
            ),
            $message
        );
    }

    /**
     * Assert that layout block parent is equal to expected
     *
     * @param string $blockName
     * @param string $parentBlockName
     * @param string $message
     */
    public static function assertLayoutBlockParentEquals($blockName, $parentBlockName, $message = '')
    {
        self::assertThatLayout(
            self::layoutBlock(
                $blockName,
                EcomDev_PHPUnit_Constraint_Layout_Block::TYPE_PARENT_NAME,
                $parentBlockName
            ),
            $message
        );
    }

    /**
     * Assert that layout block parent is not equal to expected
     *
     * @param string $blockName
     * @param string $parentBlockName
     * @param string $message
     */
    public static function assertLayoutBlockParentNotEquals($blockName, $parentBlockName, $message = '')
    {
        self::assertThatLayout(
            self::logicalNot(
                self::layoutBlock(
                    $blockName,
                    EcomDev_PHPUnit_Constraint_Layout_Block::TYPE_PARENT_NAME,
                    $parentBlockName
                )
            ),
            $message
        );
    }

    /**
     * Assert that layout block is placed after expected
     *
     * @param string $blockName
     * @param string $after
     * @param string $message
     */
    public static function assertLayoutBlockAfter($blockName, $after, $message = '')
    {
        self::assertThatLayout(
           self::layoutBlock(
                $blockName,
                EcomDev_PHPUnit_Constraint_Layout_Block::TYPE_AFTER,
                $after
            ),
            $message
        );
    }

    /**
     * Assert that layout block is placed before expected
     *
     * @param string $blockName
     * @param string $before
     * @param string $message
     */
    public static function assertLayoutBlockBefore($blockName, $before, $message = '')
    {
        self::assertThatLayout(
           self::layoutBlock(
                $blockName,
                EcomDev_PHPUnit_Constraint_Layout_Block::TYPE_BEFORE,
                $before
            ),
            $message
        );
    }

    public static function assertLayoutBlockAfterAll(string $blockName, array $after, string $message = '')
    {
        $constraints = [];
        foreach ($after as $value) {
            $constraints[] = self::layoutBlock(
                $blockName, EcomDev_PHPUnit_Constraint_Layout_Block::TYPE_AFTER, $value
            );
        }

        self::assertThatLayout(self::logicalAnd(...$constraints), $message);
    }

    public static function assertLayoutBlockBeforeAll(string $blockName, array $before, string $message = '')
    {
        $constraints = [];
        foreach ($before as $value) {
            $constraints[] = self::layoutBlock(
                $blockName, EcomDev_PHPUnit_Constraint_Layout_Block::TYPE_BEFORE, $value
            );
        }

        self::assertThatLayout(self::logicalAnd(...$constraints), $message);
    }

    /**
     * Assert that layout block type is on the root rendering level
     *
     * @param string $blockName
     * @param string $message
     */
    public static function assertLayoutBlockRootLevel($blockName, $message = '')
    {
        self::assertThatLayout(
            self::layoutBlock(
                $blockName,
                EcomDev_PHPUnit_Constraint_Layout_Block::TYPE_ROOT_LEVEL
            ),
            $message
        );
    }

     /**
     * Assert that layout block type is not on the root rendering level
     *
     * @param string $blockName
     * @param string $message
     */
    public static function assertLayoutBlockNotRootLevel($blockName, $message = '')
    {
        self::assertThatLayout(
            self::logicalNot(
                self::layoutBlock(
                    $blockName,
                    EcomDev_PHPUnit_Constraint_Layout_Block::TYPE_ROOT_LEVEL
                )
            ),
            $message
        );
    }

    /**
     * Assert that layout block action was invoked
     *
     *
     * @param string $blockName
     * @param string $method
     * @param string $message
     * @param array $arguments
     * @param string $searchType
     * @internal param array|null $params
     */
    public static function assertLayoutBlockActionInvoked($blockName, $method, $message = '',
        array $arguments = null, $searchType = EcomDev_PHPUnit_Constraint_Layout_Block_Action::SEARCH_TYPE_AND)
    {
        self::assertThatLayout(
            self::layoutBlockAction(
                $blockName, $method,
                EcomDev_PHPUnit_Constraint_Layout_Block_Action::TYPE_INVOKED, null,
                $arguments, $searchType
            ),
            $message
        );
    }

    /**
     * Assert that layout block action was not invoked
     *
     *
     * @param string $blockName
     * @param string $method
     * @param string $message
     * @param array|null $arguments
     * @param string $searchType
     */
    public static function assertLayoutBlockActionNotInvoked($blockName, $method, $message = '',
        array $arguments = null, $searchType = EcomDev_PHPUnit_Constraint_Layout_Block_Action::SEARCH_TYPE_AND)
    {
        self::assertThatLayout(
            self::logicalNot(
                self::layoutBlockAction(
                    $blockName, $method,
                    EcomDev_PHPUnit_Constraint_Layout_Block_Action::TYPE_INVOKED, null,
                    $arguments, $searchType
                )
            ),
            $message
        );
    }

    /**
     * Assert that layout block action was invoked at least expected number of times
     *
     *
     * @param string $blockName
     * @param string $method
     * @param int $invocationCount
     * @param string $message
     * @param array $arguments
     * @param string $searchType
     * @internal param array|null $params
     */
    public static function assertLayoutBlockActionInvokedAtLeast($blockName, $method, $invocationCount,
        $message = '', array $arguments = null,
        $searchType = EcomDev_PHPUnit_Constraint_Layout_Block_Action::SEARCH_TYPE_AND)
    {
        self::assertThatLayout(
            self::layoutBlockAction(
                $blockName, $method,
                EcomDev_PHPUnit_Constraint_Layout_Block_Action::TYPE_INVOKED_AT_LEAST, $invocationCount,
                $arguments, $searchType
            ),
            $message
        );
    }

    /**
     * Assert that layout block action was invoked exactly expected number of times
     *
     *
     * @param string $blockName
     * @param string $method
     * @param int $invocationCount
     * @param string $message
     * @param array $arguments
     * @param string $searchType
     * @internal param array|null $params
     */
    public static function assertLayoutBlockActionInvokedExactly($blockName, $method, $invocationCount,
        $message = '', array $arguments = null,
        $searchType = EcomDev_PHPUnit_Constraint_Layout_Block_Action::SEARCH_TYPE_AND)
    {
        self::assertThatLayout(
            self::layoutBlockAction(
                $blockName, $method,
                EcomDev_PHPUnit_Constraint_Layout_Block_Action::TYPE_INVOKED_EXACTLY, $invocationCount,
                $arguments, $searchType
            ),
            $message
        );
    }

    /**
     * Assert that layout block property is matched constraint conditions
     *
     * @param string $blockName
     * @param string $propertyName
     * @param \PHPUnit\Framework\Constraint\Constraint $constraint
     * @param string $message
     */
    public static function assertLayoutBlockProperty($blockName, $propertyName,
        \PHPUnit\Framework\Constraint\Constraint $constraint, $message = '')
    {
        self::assertThatLayout(
            self::layoutBlockProperty($blockName, $propertyName, $constraint),
            $message
        );
    }

    /**
     * Assert that layout block property is not matched constraint conditions
     *
     * @param string $blockName
     * @param string $propertyName
     * @param \PHPUnit\Framework\Constraint\Constraint $constraint
     * @param string $message
     */
    public static function assertLayoutBlockPropertyNot($blockName, $propertyName,
        \PHPUnit\Framework\Constraint\Constraint $constraint, $message = '')
    {
        self::assertThatLayout(
            self::logicalNot(
                self::layoutBlockProperty($blockName, $propertyName, $constraint)
            ),
            $message
        );
    }

    /**
     * Assert that layout block property is equal to expected value
     *
     * @param string $blockName
     * @param string $propertyName
     * @param mixed $expectedValue
     * @param string $message
     * @param float|int $delta
     * @param integer $maxDepth
     * @param boolean $canonicalize
     * @param boolean $ignoreCase
     */
    public static function assertLayoutBlockPropertyEquals($blockName, $propertyName,
        $expectedValue, $message = '', $delta = 0, $maxDepth = 10, $canonicalize = false,
        $ignoreCase = false)
    {
        self::assertLayoutBlockProperty(
            $blockName, $propertyName,
            self::equalTo($expectedValue, $delta, $maxDepth, $canonicalize, $ignoreCase),
            $message
       );
    }

    /**
     * Assert that layout block property is not equal to expected value
     *
     * @param string $blockName
     * @param string $propertyName
     * @param mixed $expectedValue
     * @param string $message
     * @param float|int $delta
     * @param integer $maxDepth
     * @param boolean $canonicalize
     * @param boolean $ignoreCase
     */
    public static function assertLayoutBlockPropertyNotEquals($blockName, $propertyName,
        $expectedValue, $message = '', $delta = 0, $maxDepth = 10, $canonicalize = false,
        $ignoreCase = false)
    {
        self::assertLayoutBlockPropertyNot(
            $blockName, $propertyName,
            self::equalTo($expectedValue, $delta, $maxDepth, $canonicalize, $ignoreCase),
            $message
       );
    }


    /**
     * Assert that layout block property is the same as expected value
     *
     * @param string $blockName
     * @param string $propertyName
     * @param mixed $expectedValue
     * @param string $message
     */
    public static function assertLayoutBlockPropertySame($blockName, $propertyName,
        $expectedValue, $message = '')
    {
        self::assertLayoutBlockProperty(
            $blockName, $propertyName,
            self::identicalTo($expectedValue),
            $message
       );
    }

    /**
     * Assert that layout block property is not the same as expected value
     *
     * @param string $blockName
     * @param string $propertyName
     * @param mixed $expectedValue
     * @param string $message
     */
    public static function assertLayoutBlockPropertyNotSame($blockName, $propertyName,
        $expectedValue, $message = '')
    {
        self::assertLayoutBlockPropertyNot(
            $blockName, $propertyName,
            self::identicalTo($expectedValue),
            $message
       );
    }

    /**
     * Assert that layout block property is equal to expected php internal type
     *
     * @param string $blockName
     * @param string $propertyName
     * @param string $type
     * @param string $message
     */
    public static function assertLayoutBlockPropertyType($blockName, $propertyName,
        $type, $message = '')
    {
        self::assertLayoutBlockProperty(
            $blockName, $propertyName,
            self::isType($type),
            $message
       );
    }

    /**
     * Assert that layout block property is not equal to expected php internal type
     *
     * @param string $blockName
     * @param string $propertyName
     * @param string $type
     * @param string $message
     */
    public static function assertLayoutBlockPropertyNotType($blockName, $propertyName,
        $type, $message = '')
    {
        self::assertLayoutBlockPropertyNot(
            $blockName, $propertyName,
            self::isType($type),
            $message
       );
    }


    /**
     * Assert that layout block property is an instance of expected class name
     *
     * @param string $blockName
     * @param string $propertyName
     * @param string $expectedClassName
     * @param string $message
     */
    public static function assertLayoutBlockPropertyInstanceOf($blockName, $propertyName,
        $expectedClassName, $message = '')
    {
        self::assertLayoutBlockProperty(
            $blockName, $propertyName,
            self::isInstanceOf($expectedClassName),
            $message
       );
    }

    /**
     * Assert that layout block property is not an instance of expected class name
     *
     * @param string $blockName
     * @param string $propertyName
     * @param string $expectedClassName
     * @param string $message
     */
    public static function assertLayoutBlockPropertyNotInstanceOf($blockName, $propertyName,
        $expectedClassName, $message = '')
    {
        self::assertLayoutBlockPropertyNot(
            $blockName, $propertyName,
            self::isInstanceOf($expectedClassName),
            $message
       );
    }

    /**
     * Assert that layout block property is empty
     *
     * @param string $blockName
     * @param string $propertyName
     * @param string $message
     */
    public static function assertLayoutBlockPropertyEmpty($blockName, $propertyName, $message = '')
    {
        self::assertLayoutBlockProperty(
            $blockName, $propertyName,
            self::isEmpty(),
            $message
       );
    }

    /**
     * Assert that layout block property is not empty
     *
     * @param string $blockName
     * @param string $propertyName
     * @param string $message
     */
    public static function assertLayoutBlockPropertyNotEmpty($blockName, $propertyName, $message = '')
    {
        self::assertLayoutBlockPropertyNot(
            $blockName, $propertyName,
            self::isEmpty(),
            $message
       );
    }

    /**
     * Assert that layout block property is null
     *
     * @param string $blockName
     * @param string $propertyName
     * @param string $message
     */
    public static function assertLayoutBlockPropertyNull($blockName, $propertyName, $message = '')
    {
        self::assertLayoutBlockProperty(
            $blockName, $propertyName,
            self::isNull(),
            $message
       );
    }

    /**
     * Assert that layout block property is not null
     *
     * @param string $blockName
     * @param string $propertyName
     * @param string $message
     */
    public static function assertLayoutBlockPropertyNotNull($blockName, $propertyName, $message = '')
    {
        self::assertLayoutBlockPropertyNot(
            $blockName, $propertyName,
            self::isEmpty(),
            $message
       );
    }


    /**
     * Dispatch a request.
     *
     * @param $baseUrl
     * @param $requestUri
     * @param $urlModel
     *
     * @return $this
     */
    protected function _dispatch($baseUrl, $requestUri, $urlModel)
    {
        $this->getRequest()->resetInternalProperties();

        $this->getRequest()->setBaseUrl($baseUrl)
        ->setRequestUri($requestUri)
        ->setPathInfo();

        $customCookies = $this->getRequest()->getCookie();

        $autoCookies = $this->getCookies()->getMatchingCookies($requestUri);

        /* @var $cookie Zend_Http_Cookie */
        foreach ($autoCookies as $cookie)
        {
            $this->getRequest()->setCookie(
                $cookie->getName(),
                $cookie->getValue()
            );
        }

        if ($urlModel instanceof Mage_Adminhtml_Model_Url)
        {
            // Workaround for secret key in admin
            $this->getRequest()->setParam(
                Mage_Adminhtml_Model_Url::SECRET_KEY_PARAM_NAME,
                $urlModel->getSecretKey()
            );
        }

        if (!$this->getRequest()->getMethod())
        {
            $this->getRequest()->setMethod('GET');
        }

        // Workaround for form key
        if ($this->getRequest()->isPost())
        {
            $this->getRequest()->setPost(
                'form_key',
                Mage::getSingleton('core/session')->getFormKey()
            );
        }

        $this->getLayout()->reset();
        $this->getResponse()->reset();

        $this->app()->getFrontController()->dispatch();

        // Unset changed cookies
        $this->getRequest()->resetCookies();
        $this->getRequest()->setCookies($customCookies);

        return $this;
    }


    /**
     * Set up controller params
     * (non-PHPdoc)
     * @see EcomDev_PHPUnit_Test_Case::setUp()
     */
    protected function setUp():void
    {
        parent::setUp();

        $this->reset();
        $this->registerCookieStub();
        $this->getCookies()->reset();
        $this->app()->getFrontController()->init();
    }

    /**
     * Registers cookie stub
     *
     * @return EcomDev_PHPUnit_Test_Case_Controller
     */
    protected function registerCookieStub()
    {
        $cookie = $this->getModelMock('core/cookie', array('set', 'delete'));

        $cookie->expects($this->any())
            ->method('set')
            ->will($this->returnCallback(
                array($this, 'setCookieCallback')
            ));

        $cookie->expects($this->any())
            ->method('delete')
            ->will($this->returnCallback(
                array($this, 'deleteCookieCallback')
            ));

        $this->replaceByMock('model', 'core/cookie', $cookie);
        return $this;
    }

    /**
     * A callback that is invoked when a cookie is set
     * Emulates cookies processing by browser
     * Uses Zend_Http_CookieJar component
     *
     * @param string $name
     * @param string $value
     * @param int|boolean|null $period
     * @param string|null $path
     * @param string|null $domain
     * @param boolean|null $secure
     * @param boolean|null $httponly
     * @return EcomDev_PHPUnit_Test_Case_Controller
     */
    public function setCookieCallback($name, $value, $period = null, $path = null, $domain = null, $secure = null, $httponly = null)
    {
        /* @var $coookieStub Mage_Core_Model_Cookie */
        $cookieStub = Mage::getSingleton('core/cookie');

        $cookie = urlencode($name) . '=' . urlencode($value);

        if ($period === true) {
            $period = 3600;
        } elseif ($period === null) {
            $period = $cookieStub->getLifetime();
        }

        if ($path === null) {
            $path = $cookieStub->getPath();
        }

        if ($domain === null) {
            $domain = $cookieStub->getDomain();
        }

        if ($period === false) {
            $expire = 0;
        } elseif ($period === 0) {
            $expire = null;
        } else {
            $expire = time() + $period;
        }

        if ($domain !== null) {
            $cookie .= '; Domain=.' . $domain;
        }

        if ($path !== null) {
            $cookie .= '; Path=' . urlencode($path);
        }

        if ($expire !== null) {
            $cookie .= '; Expires='. date('r', $expire);
        }

        if ($secure || $cookieStub->isSecure()) {
            $cookie .= '; Secure';
        }

        if ($httponly || $cookieStub->getHttponly()) {
            $cookie .= '; HttpOnly';
        }

        self::getCookies()->addCookie($cookie);

        return $this;
    }

    /**
     * A callback that is invoked when a cookie is deleted
     *
     * @param string $name
     * @param string $path
     * @param string $domain
     * @param int $secure
     * @param int $httponly
     * @return EcomDev_PHPUnit_Test_Case_Controller
     */
    public function deleteCookieCallback($name, $path = null, $domain = null, $secure = null, $httponly = null)
    {
        $this->setCookieCallback($name, null, false, $path, $domain, $secure, $httponly);
        return $this;
    }

    /**
     * Resets controller test case
     *
     * @return EcomDev_PHPUnit_Test_Case_Controller
     */
    protected function reset()
    {
        $_SESSION = array();

        // Init request for any url that using sessions
        $initialUrlParams = array();
        $urlModel = $this->getUrlModel(null, $initialUrlParams);
        $baseUrl = $urlModel->getBaseUrl($initialUrlParams);

        $this->getRequest()->reset();
        $this->getRequest()->setBaseUrl($baseUrl);

        $this->getResponse()->reset();
        $this->getLayout()->reset();

        return $this;
    }

    /**
     * Returns URL model for request
     *
     *
     * @param string|null $route
     * @param array $params
     * @return Mage_Core_Model_Url
     */
    protected function getUrlModel($route = null, array &$params)
    {
        if (!isset($params['_store'])) {
            if (strpos((string)$route, EcomDev_PHPUnit_Model_App::AREA_ADMINHTML) !== false) {
                $params['_store'] = EcomDev_PHPUnit_Model_App::ADMIN_STORE_CODE;
            } else {
                $params['_store'] = $this->app()->getAnyStoreView()->getCode();
            }
        }

        if ($params['_store'] !== EcomDev_PHPUnit_Model_App::ADMIN_STORE_CODE) {
            $this->setCurrentStore($params['_store']);
            $urlModel = Mage::getModel('core/url');
        } else {
            $urlModel = Mage::getModel('adminhtml/url');
        }

        return $urlModel;
    }


    /**
     * Dispatch an URL to magento.
     *
     * @param string $requestUri
     * @param array  $params
     *
     * @return $this
     */
    public function dispatchUrl($requestUri, $params = array())
    {
        $urlModel = $this->getUrlModel(null, $params);
        $baseUrl  = $urlModel->getBaseUrl($params);

        return $this->_dispatch($baseUrl, $requestUri, $urlModel);
    }


    /**
     * Dispatches a route.
     *
     *
     * @param string $route
     * @param array  $params
     *
     * @return $this
     */
    public function dispatch($route = null, array $params = array())
    {
        $urlModel = $this->getUrlModel($route, $params);

        $this->app()->resetAreas();

        $requestUri = $urlModel->getUrl($route, $params);
        $baseUrl = $urlModel->getBaseUrl($params);

        return $this->_dispatch($baseUrl, $requestUri, $urlModel);
    }

    /**
     * Creates admin user session stub for testing adminhtml controllers
     *
     * @param array<string>|null $aclResources list of allowed ACL resources for user, if null then it is super admin
     * @param int $userId fake id of the admin user, you can use different one if it is required for your tests
     */
    protected function mockAdminUserSession(array|null $aclResources = null, int $userId = 1): EcomDev_PHPUnit_Test_Case_Controller
    {
        $adminSessionMock = $this->getModelMock(
            'admin/session',
            ['init', 'getUser', 'isLoggedIn', 'isAllowed'],
        );

        $adminUserMock = $this->getModelMock(
            'admin/user',
            ['login', 'getId', 'save', 'authenticate', 'getRole'],
        );

        $adminRoleMock = $this->getModelMock(
            'admin/roles',
            ['getGwsIsAll', 'getGwsStores', 'getGwsStoreGroups', 'getGwsRelevantWebsites'],
        );

        $adminRoleMock->expects($this->any())
            ->method('getGwsStores')
            ->will($this->returnValue(array_keys(Mage::app()->getStores(true))));

        $adminRoleMock->expects($this->any())
            ->method('getGwsStoreGroups')
            ->will($this->returnValue(array_keys(Mage::app()->getGroups(true))));

        $adminRoleMock->expects($this->any())
            ->method('getGwsRelevantWebsites')
            ->will($this->returnValue(array_keys(Mage::app()->getWebsites(true))));

        $adminRoleMock->expects($this->any())
            ->method('getGwsIsAll')
            ->will($this->returnValue(true));

        $adminUserMock->expects($this->any())
            ->method('getRole')
            ->will($this->returnValue($adminRoleMock));

        $adminUserMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue($userId));

        $adminSessionMock->expects($this->any())
            ->method('getUser')
            ->will($this->returnValue($adminUserMock));

        $adminSessionMock->expects($this->any())
            ->method('isLoggedIn')
            ->will($this->returnValue(true));

        // Simple isAllowed implementation
        $adminSessionMock->expects($this->any())
            ->method('isAllowed')
            ->will($this->returnCallback(static function ($resource) use ($aclResources) {
                if ($aclResources === null) {
                    return true;
                }

                if (str_starts_with($resource, 'admin/')) {
                    $resource = substr($resource, strlen('admin/'));
                }

                return in_array($resource, $aclResources);
            }));

        $this->replaceByMock('model', 'admin/session', $adminSessionMock);

        $this->getRequest()->setParam(
            Mage_Adminhtml_Model_Url::SECRET_KEY_PARAM_NAME,
            Mage::getSingleton('adminhtml/url')->getSecretKey(),
        );

        return $this;
    }
}
