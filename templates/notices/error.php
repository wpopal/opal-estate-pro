<?php
/**
 * $Desc$
 *
 * @version    $Id$
 * @package    opalestate
 * @author     Opal  Team <info@wpopal.com >
 * @copyright  Copyright (C) 2016 wpopal.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * @website  http://www.wpopal.com
 * @support  http://www.wpopal.com/support/forum.html
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
if ( ! $messages ) return;

?>
<ul class="opalestate-notice opalestate-notice-error">
	<?php foreach ( $messages as $message ) : ?>
		<li>
			<?php printf( '%s', $message ) ?>
		</li>
	<?php endforeach; ?>
</ul>