<?php

class RemarksAuthors extends RemarksSegment {
  
    public function __construct() {
        $this->populateAuthorMatrix();
        parent::__construct('author');
    }
    
    private function populateAuthorMatrixRow($authorID, $authorName){
        global $wpdb;
        global $remarks_posts;

        $retrievePosts = "SELECT ID FROM $wpdb->posts WHERE post_author = $authorID AND post_status='publish'";
        $authors = $wpdb->get_results($retrievePosts, ARRAY_A);

        $numPosts = 0;
        $numComments = 0;

        foreach ($authors as $post){
                $numPosts +=1;
                $numComments +=$remarks_posts[$post['ID']]['count'];
        }

        $this->segmentData[] = array('numPosts' => $numPosts, 'count' => $numComments, 'name' => $authorName, 'id' => $authorID);

        remarks_handle_biggest_source($this->mostPopular, $this->highestNumber, $authorName, $numComments);
    }

    private function renderAuthorMatrixRow($authorIndex){
        echo "<tr><td><a href = '".get_bloginfo('url')."/?author=" . $this->segmentData[$authorIndex]['id'] . "'>".$this->segmentData[$authorIndex]['name']."</a></td><td>". $this->segmentData[$authorIndex]['count']." comments</td><td>".$this->segmentData[$authorIndex]['numPosts']." posts</td></tr>\n";
    }

    public function renderMatrix(){
        echo "<div id='author_table'>\n\n";
        echo "<table id='author_table' class='centralise'>";
        echo "<tr><td><strong>Post Author</strong></td><td><strong>Number of Comments</strong></td><td><strong>Number of Posts</strong></td></tr>\n";
        foreach($this->segmentData as $authorIndex => $eachAuthor){
            $this->renderAuthorMatrixRow($authorIndex);
        }
        echo "</table>\n\n";
        echo "</div>\n\n";
    }

    public function populateAuthorMatrix(){
        global $wpdb;

        $retrieveAuthors = "SELECT ID, display_name FROM $wpdb->users WHERE 1";
        $authors = $wpdb->get_results($retrieveAuthors, ARRAY_A);

        foreach ($authors as $eachAuthor){
                $this->populateAuthorMatrixRow($eachAuthor['ID'], $eachAuthor['display_name']);
        }
        usort($this->segmentData, 'self::reorder');
    }
}