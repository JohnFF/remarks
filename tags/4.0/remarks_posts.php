<?php

function categoriesLinks($arrayOfCategoryIndices){
$outputString = "";
    foreach ($arrayOfCategoryIndices as $categoryIndex){
	$outputString .= "<a href='" . get_category_link($categoryIndex) . "'>" . get_cat_name($categoryIndex) .  "</a>, ";
    }  
    return substr($outputString, 0, strlen($outputString)-2);
}


function renderPostMatrixRow($id){
  global $remarks_posts;
  echo "<tr>\n";
  echo "\t<td><a href='".$remarks_posts[$id]['guid']."' >".$remarks_posts[$id]['title']. "</a></td>\n";
  echo "\t<td align='center'>". $remarks_posts[$id]['count']." comments</td>\n";
  echo "\t<td>".categoriesLinks($remarks_posts[$id]['categories'])."</td>\n";
  echo "\t<td align='center'><a href = '".get_bloginfo('url')."/?author=" . $remarks_posts[$id]['author'] . "'>".$remarks_posts[$id]['author_name']."</a></td>\n";
	echo "</tr>\n";
}


function renderPostMatrix(){
    global $remarks_posts;
    echo "<table>";
    echo "<tr><td><strong>Post Name</strong></td><td><strong>Number of Comments</strong></td><td><strong>Category(s)</strong></td><td><strong>Author</strong></td></tr>\n";
	foreach ($remarks_posts as $eachPostIndex => $eachPost){
		renderPostMatrixRow( $eachPostIndex);
	}
    echo "</table>\n\n";
}


function addPostMatrixRow($id, $title, $guid, $authorId, $authorName, $numComments ){
  global $remarks_posts;
  $remarks_posts[$id] = array( 'title' => $title, 'guid' => $guid, 'categories' => wp_get_post_categories($id), 'author' => $authorId, 'author_name' => $authorName, 'count' => $numComments);
}


function populatePostMatrix(){
  global $wpdb;
  global $remarks_posts_top;


  $getCommentedPostsQuery = "SELECT $wpdb->posts.ID as post_ID, $wpdb->posts.post_title, $wpdb->posts.post_author, $wpdb->users.display_name AS 'author_name', $wpdb->posts.guid, count($wpdb->comments.comment_ID) AS 'count' 
    FROM $wpdb->posts LEFT JOIN $wpdb->comments ON $wpdb->posts.ID=$wpdb->comments.comment_post_id LEFT JOIN $wpdb->users ON $wpdb->posts.post_author = $wpdb->users.ID 
    WHERE post_status = 'publish' AND $wpdb->comments.comment_approved='1' 
    GROUP BY $wpdb->posts.ID 
    ORDER BY count($wpdb->comments.comment_ID) DESC";

  $getUncommentedPostsQuery = "SELECT ID as post_ID, post_title, guid, post_author, $wpdb->users.display_name AS 'author_name' FROM $wpdb->posts LEFT JOIN $wpdb->users ON $wpdb->posts.post_author = $wpdb->users.ID WHERE post_status = 'publish'";

  //echo "about to call query: $query<br/>";
  $commented_posts = $wpdb->get_results($getCommentedPostsQuery , ARRAY_A);

  // TODO produce query of posts with no comments
  if ($commented_posts != FALSE){
    foreach($commented_posts as $eachPost){
      $getUncommentedPostsQuery = $getUncommentedPostsQuery." AND $wpdb->posts.ID != ".$eachPost['post_ID'];
      addPostMatrixRow($eachPost['post_ID'], $eachPost['post_title'], $eachPost['guid'], $eachPost['post_author'], $eachPost['author_name'], $eachPost['count'] );
      remarks_handle_biggest_source($remarks_posts_top['label'], $remarks_posts_top['count'], $eachPost['post_title'], $eachPost['count']);
    }
  }

  $uncommented_posts = $wpdb->get_results($getUncommentedPostsQuery , ARRAY_A); 
  if ($uncommented_posts != FALSE){
    foreach($uncommented_posts as $eachPost){
      addPostMatrixRow($eachPost['post_ID'], $eachPost['post_title'], $eachPost['guid'], $eachPost['post_author'], $eachPost['author_name'], '0' );
    }
  }

} // populatePostMatrix()
?>