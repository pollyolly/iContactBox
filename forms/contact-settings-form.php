<?php
function icb_email_settings_form_handler(){
    global $wpdb;
    $table_name = "{$wpdb->prefix}icb_email_setting";
    $message = '';
    $notice = '';
    // this is default $item which will be used for new records
    $default = array(
        'id' => 0,
        'host' => '',
        'port' => '',
        'username' => '',
        'userpass' => '',
        'security' => '',
        'fromname' => ''
    );
    // here we are verifying does this request is post back and have correct nonce
    if ( isset($_REQUEST['nonce']) && wp_verify_nonce($_REQUEST['nonce'], basename(__FILE__))) {
        // combine our default item with request params
        $item = shortcode_atts($default, $_REQUEST);
        // validate data, and if all ok save item to database
        // if id is zero insert otherwise update
        // var_dump($item); exit;
        $item_valid = icb_validate_settings_form($item);
        if ($item_valid === true) {
            if ($item['id'] == 0) {
                $result = $wpdb->insert($table_name, $item);
                $item['id'] = $wpdb->insert_id;
                if ($result) {
                    $message = 'Item was successfully saved';
                } else {
                    $notice = 'There was an error while saving item';
                }
            } else {
                $result = $wpdb->update($table_name, $item, array('id' => $item['id']));
                if ($result) {
                    $message = 'Item was successfully updated';
                } else {
                    $notice = 'There was an error while updating item';
                }
            }
        } else {
            // if $item_valid not true it contains error message(s)
            $notice = $item_valid;
        }
    }
    else {
        // if this is not post back we load item to edit or give new one to create
        $item = $default;
        if (isset($_REQUEST['id'])) {
            $item = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $_REQUEST['id']), ARRAY_A);
            if (!$item) {
                $item = $default;
                $notice = 'Item not found';
            }
        }
    }
    // here we adding our custom meta box
    add_meta_box('email_settings_form_meta_box', 'Email Settings', 'icb_email_settings_form_meta_box_handler', 'emailsettings', 'normal', 'default');
    ?>
<div class="wrap">
    <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
    <h2>Topic 
        <a class="add-new-h2" href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=contact-box-settings');?>">back to list</a>
    </h2>
    <?php if (!empty($notice)): ?>
    <div id="notice" class="error"><p><?php echo $notice ?></p></div>
    <?php endif;?>
    <?php if (!empty($message)): ?>
    <div id="message" class="updated"><p><?php echo $message ?></p></div>
    <?php endif;?>
    <form id="form" method="POST">
        <input type="hidden" name="nonce" value="<?php echo wp_create_nonce(basename(__FILE__))?>"/>
        <?php /* NOTICE: here we storing id to determine will be item added or updated */ ?>
        <input type="hidden" name="id" value="<?php echo $item['id'] ?>"/>
        <div class="metabox-holder" id="poststuff">
            <div id="post-body">
                <div id="post-body-content">
                    <?php /* And here we call our custom meta box */ ?>
                    <?php do_meta_boxes('emailsettings', 'normal', $item); ?>
                    <input type="submit" value="Save" id="submit" class="button-primary" name="submit">
                </div>
            </div>
        </div>
    </form>
</div>
<?php
}
function icb_email_settings_form_meta_box_handler($item){
    ?>
    <table cellspacing="2" cellpadding="5" style="width: 100%;" class="form-table">
        <tbody>
        <tr class="form-field">
            <th valign="top" scope="row">
                <label for="host">Email Setting</label>
            </th>
            <td>
                <input id="host" name="host" type="text" style="width: 95%" value="<?php echo esc_attr($item['host'])?>"
                    size="50" class="code" placeholder="Host" required>
            </td>
            <td>
                <input id="port" name="port" type="number" style="width: 95%" value="<?php echo esc_attr($item['port'])?>"
                    size="50" class="code" placeholder="Port" required>
            </td>
            <td>
                <input id="username" name="username" type="text" style="width: 95%" value="<?php echo esc_attr($item['username'])?>"
                    size="50" class="code" placeholder="Username" required>
            </td>
            <td>
                <input id="userpass" name="userpass" type="password" style="width: 95%" value="<?php echo esc_attr($item['userpass'])?>"
                    size="50" class="code" placeholder="Password" required>
            </td>
            <td>
                <input id="security" name="security" type="text" style="width: 95%" value="<?php echo esc_attr($item['security'])?>"
                    size="50" class="code" placeholder="Security" required>
            </td>
            <td>
                <input id="fromname" name="fromname" type="text" style="width: 95%" value="<?php echo esc_attr($item['fromname'])?>"
                    size="50" class="code" placeholder="From Name" required>
            </td>
        </tr>
        </tbody>
    </table>
<?php
}

function icb_validate_settings_form($item)
{
    $messages = array();

    if (empty($item['host'])) $messages[] = 'Host is required';
    if (empty($item['port']))  $messages[] = 'Port is required';
    if (empty($item['username']))  $messages[] = 'Username is required';
    if (empty($item['userpass'])) $messages[] = 'Password is required';
    if (empty($item['security']))  $messages[] = 'Security is required';
    if (empty($item['fromname']))  $messages[] = 'From Name is required';

    if (empty($messages)) return true;
    return implode('<br />', $messages);
}