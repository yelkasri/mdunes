<?php
/**
 * --------------------------------------------------------------------------------
 * Payment Plugin - Paymill
 * --------------------------------------------------------------------------------
 * @package     Joomla 2.5 -  3.x
 * @subpackage  J2Store
 * @author      Paymill
 * @copyright   Copyright (c) Paymill
 * @license     GNU/GPL license: http://www.gnu.org/licenses/gpl-2.0.html
 * --------------------------------------------------------------------------------
 *
 * */


namespace Paymill\Models\Request;
// No direct access
defined('_JEXEC') or die('Restricted access');
/**
 * Offer Model
 * An offer is a recurring plan which a user can subscribe to.
 * You can create different offers with different plan attributes e.g. a monthly or a yearly based paid offer/plan.
 * @tutorial https://paymill.com/de-de/dokumentation/referenz/api-referenz/#document-offers
 */
class Offer extends Base
{
    /**
     * @var string
     */
    private $_name;
    /**
     * @var integer
     */
    private $_amount;
    /**
     * @var string
     */
    private $_currency;

    /**
     * @var string
     */
    private $_interval;

    /**
     * @var integer
     */
    private $_trialPeriodDays;

    /**
     * Creates an instance of the offer request model
     */
    public function __construct()
    {
        $this->_serviceResource = 'Offers/';
    }

    /**
     * Returns Your name for this offer
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Sets Your name for this offer
     * @param string $name
     * @return \Paymill\Models\Request\Offer
     */
    public function setName($name)
    {
        $this->_name = $name;
        return $this;
    }

    /**
     * Returns the amount as an integer
     * @return integer
     */
    public function getAmount()
    {
        return $this->_amount;
    }

    /**
     * Sets the amount.
     * Every interval the specified amount will be charged. Only integer values are allowed (e.g. 42.00 = 4200)
     * @param integer $amount
     * @return \Paymill\Models\Request\Offer
     */
    public function setAmount($amount)
    {
        $this->_amount = (int) $amount;
        return $this;
    }

    /**
     * Returns the interval defining how often the client should be charged.
     * @return string
     */
    public function getInterval()
    {
        return $this->_interval;
    }

    /**
     * Sets the interval defining how often the client should be charged.
     * @example Format: number DAY | WEEK | MONTH | YEAR Example: 2 DAY
     * @param string $interval
     * @return \Paymill\Models\Request\Offer
     */
    public function setInterval($interval)
    {
        $this->_interval = $interval;
        return $this;
    }

    /**
     * Returns the number of days to try
     * @return integer
     */
    public function getTrialPeriodDays()
    {
        return $this->_trialPeriodDays;
    }

    /**
     * Sets the number of days to try
     * @param integer $trialPeriodDays
     * @return \Paymill\Models\Request\Offer
     */
    public function setTrialPeriodDays($trialPeriodDays)
    {
        $this->_trialPeriodDays = $trialPeriodDays;
        return $this;
    }

    /**
     * Returns the currency
     * @return string
     */
    public function getCurrency()
    {
        return $this->_currency;
    }

    /**
     * Sets the currency
     * @param string $currency
     * @return \Paymill\Models\Request\Offer
     */
    public function setCurrency($currency)
    {
        $this->_currency = $currency;
        return $this;
    }

    /**
     * Returns an array of parameters customized for the argumented methodname
     * @param string $method
     * @return array
     */
    public function parameterize($method)
    {
        $parameterArray = array();
        switch ($method) {
            case 'create':
                $parameterArray['amount'] = $this->getAmount();
                $parameterArray['currency'] = $this->getCurrency();
                $parameterArray['interval'] = $this->getInterval();
                $parameterArray['name'] = $this->getName();
                $parameterArray['trial_period_days'] = $this->getTrialPeriodDays();
                break;
            case 'update':
                $parameterArray['name'] = $this->getName();
                break;
            case 'getOne':
                $parameterArray['count'] = 1;
                $parameterArray['offset'] = 0;
                break;
            case 'getAll':
            $parameterArray = $this->getFilter();
                break;
            case 'delete':
                break;
        }

        return $parameterArray;
    }
}
