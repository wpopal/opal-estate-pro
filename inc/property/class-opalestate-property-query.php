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

class Opalestate_Property_Query {
    /**
     * The args to pass to the give_get_donors() query
     *
     * @since  1.8.14
     * @access public
     *
     * @var    array
     */
    public $args = array();

    /**
     * The collection found based on the criteria set
     *
     * @since  1.8.14
     * @access public
     *
     * @var    array
     */

    public $count = 0;

    public $collection = array();


    public function insert() {

    }

    public function update() {

    }

    public function mapping_query() {

    }

    public function query($args) {
        $this->count      = '';
        $this->collection = '';
        $data             = '';
    }

    public function get_list($args) {
        return $collection;
    }

    public function count() {

    }


}