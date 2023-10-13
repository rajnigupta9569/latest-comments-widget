<?php
/*
Plugin Name: Latest Comments Widget
Description: Displays the lastest comments made across the website.
Version: 1.0
Author: Rajani
*/

//Enqueue style file
function widget_enqueue_styles()
{
    wp_enqueue_style('recent-comments-styles', plugin_dir_url(__FILE__) . 'style.css');
}
add_action('wp_enqueue_scripts', 'widget_enqueue_styles');

// Creating the widget class for latest comments
class Latest_Comments_Widget extends WP_Widget
{
    public function __construct()
    {
        parent::__construct(
            'latest_comments_widget',
            'Show Latest Comments Widget',
            array('description' => 'Displays the latest comments.')
        );
    }
    // Widget frontend
    public function widget($args, $instance)
    {
        $title = apply_filters('widget_title', $instance['title']);
        $num_comments = empty($instance['num_comments']) ? 5 : intval($instance['num_comments']);
        $comments = get_comments(array(
            'number' => $num_comments,
            'status' => 'approve',
        ));
        //Starts Widget Html
        echo $args['before_widget'];
        echo '<div class="latest-comments-sec">';
        if (!empty($title))
            echo $args['before_title'] . $title . $args['after_title'];

        if (!empty($comments)) {
            foreach ($comments as $comment) {
                echo '<div class="comment-item">';
                echo '<h4 class="author-name"><a href="' . get_comment_link($comment) . '">' . get_comment_author($comment->comment_ID) . '</a></h4>';
                if ($comment->comment_content) {
                    echo '<p>' . substr($comment->comment_content, 0, 50) . '... </p>';
                }
                echo '</div>';
            }
        } else {
            echo 'No comments yet.';
        }
        echo '</div>';
        echo $args['after_widget'];
        //End Widget Html
    }
    // Widget Backend
    public function form($instance)
    {
        $title = !empty($instance['title']) ? esc_attr($instance['title']) : "";
        $num_comments = !empty($instance['num_comments']) ? esc_attr($instance['num_comments']) : 5; ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Widget Title:'); ?></label>
            <input class="widget_title" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('num_comments'); ?>"><?php _e('Number of Comments to Display:'); ?></label>
            <input class="comments_count" id="<?php echo $this->get_field_id('num_comments'); ?>" name="<?php echo $this->get_field_name('num_comments'); ?>" type="number" value="<?php echo $num_comments; ?>">
        </p>
<?php
    }

    // Updating widget data
    public function update($new_instance, $old_instance)
    {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        $instance['num_comments'] = intval($new_instance['num_comments']);
        return $instance;
    }
}
// Register  the widget
function register_recent_comments_widget()
{
    register_widget('Latest_Comments_Widget');
}
add_action('widgets_init', 'register_recent_comments_widget');
