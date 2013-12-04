<?php
namespace WordPress\Admin;

/**
 * Post Comments List Table class.
 *
 * @package WordPress
 * @subpackage List_Table
 * @since 3.1.0
 * @access private
 *
 * @see WP_Comments_Table
 */
class WPPostCommentsListTable extends WPCommentsListTable
{

    function get_column_info()
    {
        $this->_column_headers = array(
            array(
                'author' => __('Author'),
                'comment' => _x('Comment', 'column name'),
            ),
            array(),
            array(),
        );

        return $this->_column_headers;
    }

    function get_table_classes()
    {
        $classes = parent::get_table_classes();
        $classes[] = 'comments-box';
        return $classes;
    }

    function display($output_empty = false)
    {
        extract($this->_args);

        wp_nonce_field("fetch-list-" . get_class($this), '_ajax_fetch_list_nonce');
        ?>
        <table class="<?php echo implode(' ', $this->get_table_classes()); ?>" cellspacing="0" style="display:none;">
            <tbody id="the-comment-list"<?php if ($singular) {
                echo " data-wp-lists='list:$singular'";
            } ?>>
            <?php if (!$output_empty) {
                $this->display_rows_or_placeholder();
            } ?>
            </tbody>
        </table>
    <?php
    }

    function get_per_page($comment_status = false)
    {
        return 10;
    }
}