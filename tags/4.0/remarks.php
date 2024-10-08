<?php
/*
	Plugin Name: Remarks
	Plugin URI: http://www.civifirst.com
	Description: Analyse the number of comments you get by post, category and author. Uses graphs!
	Version: 4.0
	Author: CiviFirst John
	Author URI: http://www.civifirst.com
	License: GPL2
*/

/*  Copyright 2013  CiviFirst John  (email : john@civifirst.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

include dirname(__FILE__)."/remarks_globals.php";
include dirname(__FILE__)."/remarks_pieces.php";
include dirname(__FILE__)."/remarks_posts.php";
include dirname(__FILE__)."/remarks_segment.php";
include dirname(__FILE__)."/remarks_overview.php";
include dirname(__FILE__)."/remarks_categories.php";
include dirname(__FILE__)."/remarks_authors.php";
include dirname(__FILE__)."/remarks_globe.php";
include dirname(__FILE__)."/remarks_navigation.php";

register_activation_hook(__FILE__,'globe_Initialise');

// TODO: fix up language things below
//if(!load_plugin_textdomain('remarks','/wp-content/languages/'))
//load_plugin_textdomain('remarks','/wp-content/plugins/remarks/mo-po-files/');

add_action('admin_menu', 'wrapper');
add_action('wp_set_comment_status', 'comment_changes', 10, 2 );


/* include css and javascript */
// register jquery and style on initialization
    wp_register_script( 'remarks_jquery', plugins_url('/js/functionality.js', __FILE__), array('jquery'), '2.5.1' );

    wp_register_style( 'remarks_css', plugins_url('/css/style.css', __FILE__), false, '1.0.0', 'all');

    wp_enqueue_script('remarks_jquery');

    wp_enqueue_style( 'remarks_css' );
/* end include css and javascript */


function wrapper(){
	add_comments_page('Remarks', 'Remarks', 'manage_options', 'remarks', 'remarks_main');
}

function comment_changes($commentID, $status){

    global $wpdb;
	$wpdb->remarks_comments = $wpdb->prefix . 'remarks_comments';

	if ($status === 'spam' || $status === 'trash' || $status === 'hold'){
		onPostDeletion($commentID);
	} elseif ($status === 'approve'){
		onPostCreation($commentID);
	}
}

function remarks_main(){
    global $wpdb;
    global $remarks_total_comments;
    global $remarks_posts;

    populatePostMatrix();
    populateCityByComments();
    
    $wpdb->remarks_comments = $wpdb->prefix . 'remarks_comments';

    $getApprovedCommentCountQuery = "SELECT count(comment_approved) comments_count FROM $wpdb->comments where comment_approved = '1' group by comment_approved";

    $query_results = $wpdb->get_row($getApprovedCommentCountQuery, ARRAY_A);

    if($query_results != FALSE){
      $remarks_total_comments = $query_results['comments_count'];
    } else {
      $remarks_total_comments = 0;
    }

    $remarksCategories = new RemarksCategories($remarks_posts);
    $remarksAuthors = new RemarksAuthors();    

    if ($remarks_total_comments > 0){
       include dirname(__FILE__)."/remarks_navigation_render.php";
    } else {
       include dirname(__FILE__)."/remarks_navigation_nocomments_render.php";
    }
    echo "<div id='display'>";
       $remarksOverview = new RemarksOverview($remarks_total_comments, $remarksCategories->getHighestStat(), $remarksAuthors->getHighestStat());
       $remarksOverview->render();

    echo "<div id='post_div' class='startHidden'>";
        renderPostMatrix();
    echo "<br/>";
    echo "</div>";
     
    $remarksCategories->render();

    $remarksAuthors->render();

    include dirname(__FILE__)."/remarks_globe_render.php";

    include dirname(__FILE__)."/remarks_about.html";

    echo "</div><!-- end display div #-->";

    echo "<br/><br/>";

    globe_Initialise();
}
?>
