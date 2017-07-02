<?php

class RemarksPosts {

	const POST_TITLE_MAX_LENGTH = 50;

  private $remarksPosts;
  private $remarksPostsTop;

  public function __construct() {
    $this->remarksPosts = array();
    $this->remarksPostsTop = array();

    $this->populatePostMatrix();
  }

  private function categoriesLinks($arrayOfCategoryIndices){
    $outputString = "";
    foreach ($arrayOfCategoryIndices as $categoryIndex){
	    $outputString .= "<a href='" . get_category_link($categoryIndex) . "'>" . get_cat_name($categoryIndex) .  "</a>, ";
    }  
    return substr($outputString, 0, strlen($outputString)-2);
  }

  private function renderPostMatrixRow($id){
    echo "<tr>\n";
    echo "\t<td><a href='".$this->remarksPosts[$id]['guid']."' >".$this->remarksPosts[$id]['title']. "</a></td>\n";
    echo "\t<td align='center'>". $this->remarksPosts[$id]['count']." comments</td>\n";
    echo "\t<td>".$this->categoriesLinks($this->remarksPosts[$id]['categories'])."</td>\n";
    echo "\t<td align='center'><a href = '".get_bloginfo('url')."/?author=" . $this->remarksPosts[$id]['author'] . "'>".$this->remarksPosts[$id]['author_name']."</a></td>\n";
    echo "</tr>\n";
  }


public function render(){
      echo "<div id='post_div' class='startHidden'>";
    echo "<table>";
    echo "<tr><td><strong>Post Name</strong></td><td><strong>Number of Comments</strong></td><td><strong>Category(s)</strong></td><td><strong>Author</strong></td></tr>\n";
	foreach ($this->remarksPosts as $eachPostIndex => $eachPost){
		$this->renderPostMatrixRow( $eachPostIndex);
	}
    echo "</table>\n\n";
    echo "<br/>";
    echo "</div>";
}


private function addPostMatrixRow($id, $title, $guid, $authorId, $authorName, $numComments ){

	$title_length = strlen($title);

	if ($title_length >= self::POST_TITLE_MAX_LENGTH) {
		$title = substr($title, 0, self::POST_TITLE_MAX_LENGTH);
	}

  $this->remarksPosts[$id] = array( 'title' => $title, 'guid' => $guid, 'categories' => wp_get_post_categories($id), 'author' => $authorId, 'author_name' => $authorName, 'count' => $numComments);
}


private function populatePostMatrix(){
  global $wpdb;

  $getCommentedPostsQuery = "SELECT $wpdb->posts.ID as post_ID, $wpdb->posts.post_title, $wpdb->posts.post_author, $wpdb->users.display_name AS 'author_name', $wpdb->posts.guid, count($wpdb->comments.comment_ID) AS 'count' 
    FROM $wpdb->posts LEFT JOIN $wpdb->comments ON $wpdb->posts.ID=$wpdb->comments.comment_post_id LEFT JOIN $wpdb->users ON $wpdb->posts.post_author = $wpdb->users.ID 
    WHERE post_status = 'publish' AND $wpdb->comments.comment_approved='1' 
    GROUP BY $wpdb->posts.ID 
    ORDER BY count($wpdb->comments.comment_ID) DESC";

  $getUncommentedPostsQuery = "SELECT posts.ID as post_ID, post_title, guid, post_author, $wpdb->users.display_name AS 'author_name' FROM $wpdb->posts AS posts LEFT JOIN $wpdb->users ON $wpdb->posts.post_author = $wpdb->users.ID WHERE post_status = 'publish'";

  //echo "about to call query: $query<br/>";
  $commented_posts = $wpdb->get_results($getCommentedPostsQuery , ARRAY_A);

  // TODO produce query of posts with no comments
  if ($commented_posts != FALSE){
    foreach($commented_posts as $eachPost){
      $getUncommentedPostsQuery = $getUncommentedPostsQuery." AND $wpdb->posts.ID != ".$eachPost['post_ID'];
      $this->addPostMatrixRow($eachPost['post_ID'], $eachPost['post_title'], $eachPost['guid'], $eachPost['post_author'], $eachPost['author_name'], $eachPost['count'] );
      RemarksSegment::remarks_handle_biggest_source($this->remarksPostsTop['label'], $this->remarksPostsTop['count'], $eachPost['post_title'], $eachPost['count']);
    }
  }

  $uncommented_posts = $wpdb->get_results($getUncommentedPostsQuery , ARRAY_A); 
  if ($uncommented_posts != FALSE){
    foreach($uncommented_posts as $eachPost){
      $this->addPostMatrixRow($eachPost['post_ID'], $eachPost['post_title'], $eachPost['guid'], $eachPost['post_author'], $eachPost['author_name'], '0' );
    }
  }

  usort($this->remarksPosts, 'RemarksSegment::reorder');

} // populatePostMatrix()

public function getHighestStat() {
	// This behaves slightly differently, as currently $this->remarksPosts is indexed by the post ID.
	$values = array_values($this->remarksPosts); // Resets the indices to 0, 1, 2 etc.
	return $values[0];
}

public function getPosts() {
  return $this->remarksPosts;
}

}
?>