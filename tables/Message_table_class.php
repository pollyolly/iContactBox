<?php
if (!class_exists('WP_List_Table')) {
    require_once (ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}
class Message_table_class extends WP_List_Table {
    function __construct(){
        global $status, $page;
        parent::__construct(array(
            'singular' => 'Message',
            'plural' => 'Messages',
        ));
    }
    function column_default($item, $column_name){
        return $item[$column_name];
    }
    // function column_status($item){ //Table column stat
    //     return '<em>' . $item['stat'] . '</em>'; 
    // }
    function column_email($item){ //Table column topic
        // links going to /admin.php?page=[your_plugin_page][&other_params]
        // notice how we used $_REQUEST['page'], so action will be done on curren page
        // also notice how we use $this->_args['singular'] so in this example it will
        // be something like &person=2
        $actions = array(
            // 'edit' => sprintf('<a href="?page=contact-topic-form&id=%s">%s</a>', $item['id'], 'Edit'),
            'delete' => sprintf('<a href="?page=%s&action=delete&id=%s">%s</a>', $_REQUEST['page'], $item['id'], 'Delete'),
        );
        return sprintf('%s %s',
            $item['email'],
            $this->row_actions($actions)
        );
    }
    function column_cb($item){
        return sprintf(
            '<input type="checkbox" name="id[]" value="%s" />',
            $item['id']
        );
    }
    function get_columns(){ //Table columns
        $columns = array(
            'cb' => '<input type="checkbox" />', //Render a checkbox instead of text
            'email' => 'Email', //Table column email
            'fullname' => 'Fullname', //Table column fullname
            'subject' => 'Subject', //Table column subject
            'message' => 'Message' //Table column message
        );
        return $columns;
    }
    function get_sortable_columns(){
        $sortable_columns = array(
            'email' => array('email', true), //Table column email
            'fullname' => array('fullname', true), //Table column topic
            'subject' => array('subject', true),
            'message' => array('message', false) //Table column message
        );
        return $sortable_columns;
    }
    function get_bulk_actions(){
        $actions = array(
            'delete' => 'Delete'
        );
        return $actions;
    }
    function process_bulk_action(){
        global $wpdb;
        $table_name = "{$wpdb->prefix}icb_messages";
        if ('delete' === $this->current_action()) {
            $ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : array();
            if (is_array($ids)) $ids = implode(',', $ids);

            if (!empty($ids)) {
                $wpdb->query("DELETE FROM $table_name WHERE id IN($ids)");
            }
        }
    }
    function prepare_items(){
        global $wpdb;
        $table_name = "{$wpdb->prefix}icb_messages";
        $per_page = 25; // constant, how much records will be shown per page
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        // here we configure table headers, defined in our methods
        $this->_column_headers = array($columns, $hidden, $sortable);
        // [OPTIONAL] process bulk action if any
        $this->process_bulk_action();
        // will be used in pagination settings
        $total_items = $wpdb->get_var("SELECT COUNT(id) FROM $table_name");
        // prepare query params, as usual current page, order by and order direction
        $paged = isset($_REQUEST['paged']) ? max(0, intval($_REQUEST['paged']) - 1) : 0;
        $orderby = (isset($_REQUEST['orderby']) && in_array($_REQUEST['orderby'], array_keys($this->get_sortable_columns()))) ? $_REQUEST['orderby'] : 'id';
        $order = (isset($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc'))) ? $_REQUEST['order'] : 'asc';
        // [REQUIRED] define $items array
        // notice that last argument is ARRAY_A, so we will retrieve array
        $this->items = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name ORDER BY $orderby $order LIMIT %d OFFSET %d", $per_page, $paged), ARRAY_A);
        // [REQUIRED] configure pagination
        $this->set_pagination_args(array(
            'total_items' => $total_items, // total items defined above
            'per_page' => $per_page, // per page constant defined at top of method
            'total_pages' => ceil($total_items / $per_page) // calculate pages count
        ));
    }
}

function icb_contact_message_page_handler(){
    global $wpdb;
    $table = new Message_table_class();
    $table->prepare_items();
    $message = '';
    if ('delete' === $table->current_action()) {
        $message = '<div class="updated below-h2" id="message"><p>' . sprintf(__('Items deleted: %d', 'icb'), count($_REQUEST['id'])) . '</p></div>';
    }
    ?>
<div class="wrap">
    <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
    <h2> Message 
        <!-- <a class="add-new-h2" href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=contact-message-form');?>">Add new</a> -->
    </h2>
    <?php echo $message; ?>
    <form id="message-table" method="GET">
        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
        <?php $table->display() ?>
    </form>
</div>
<?php
}