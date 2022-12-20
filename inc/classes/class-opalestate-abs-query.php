<?php
/**
 * $Desc$
 *
 * @version    $Id$
 * @package    opalestate
 * @author     Opal  Team <info@wpopal.com >
 * @copyright  Copyright (C) 2019 wpopal.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * @website  http://www.wpopal.com
 * @support  http://www.wpopal.com/support/forum.html
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * @class OpalEstate_Agent
 *
 * @version 1.0
 */
class OpalEstate_Abstract_Query {

    /**
     * Preserve args
     *
     * @since  $id
     * @access public
     *
     * @var    string
     */
    public $group;

    /**
     * Preserve args
     *
     * @since  $id
     * @access public
     *
     * @var    array
     */
    public $_args = array();

    /**
     * The args to pass to the give_get_payments() query
     *
     * @since  $id
     * @access public
     *
     * @var    array
     */
    public $args = array();

    /**
     * The collection found based on the criteria set
     *
     * @since  $id
     * @access public
     *
     * @var    array
     */
    public $collection = array();

    public function set_filters() {
    }

    public function unset_filters() {
    }


    public function get_list() {

    }

    public function status() {
    }

    public function page() {
    }

    /**
     * Posts Per Page
     *
     * @return void
     * @since  1.0
     * @access public
     *
     */
    public function per_page() {
    }

    /**
     * Order by
     *
     * @return void
     * @since  1.0
     * @access public
     *
     */
    public function orderby() {
    }

    public function get_by_user() {

    }

    public function search() {

    }

    /**
     * Set a query variable.
     *
     * @param $query_var
     * @param $value
     * @since  1.0
     * @access public
     *
     */
    public function __set($query_var, $value) {
        if (in_array($query_var, array('meta_query', 'tax_query'))) {
            $this->args[$query_var][] = $value;
        } else {
            $this->args[$query_var] = $value;
        }
    }

    /**
     * Unset a query variable.
     *
     * @param $query_var
     * @since  1.0
     * @access public
     *
     */
    public function __unset($query_var) {
        unset($this->args[$query_var]);
    }
}