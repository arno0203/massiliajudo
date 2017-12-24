<?php

/**
 * Created by PhpStorm.
 * User: ado
 * Date: 07/07/17
 * Time: 21:49
 */
if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH.'wp-admin/includes/class-wp-list-table.php');
}

class MassiliaJudo_Member_List extends WP_List_Table
{
    function __construct()
    {
        global $status, $page;

        //Set parent defaults
        parent::__construct(
            array(
                'singular' => 'member',     //singular name of the listed records
                'plural' => 'members',    //plural name of the listed records
                'ajax' => false        //does this table support ajax?
            )
        );
    }

    /**
     * Prepare the items for the table to process
     *
     * @return Void
     */
    public function prepare_items()
    {
        // check if a search was performed.
        $member_search_key = isset($_REQUEST['s']) ? wp_unslash(trim($_REQUEST['s'])) : '';

        $perPage = 10;
        $columns = $this->get_columns();
        $hidden = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);

        $this->process_bulk_action();

        $data = $this->table_data();
        // filter the data in case of a search.
        if ($member_search_key) {
            $data = $this->filter_table_data($data, $member_search_key);
        }
        usort($data, array(&$this, 'sort_data'));

        $totalItems = count($data);
        $currentPage = $this->get_pagenum();
        $data = array_slice($data, (($currentPage - 1) * $perPage), $perPage);
        $this->items = $data;

        $this->set_pagination_args(
            array(
                'total_items' => $totalItems,
                'per_page' => $perPage,
            )
        );
    }

    /*
	 * Filter the table data based on the user search key
	 *
	 * @since 1.0.0
	 *
	 * @param array $table_data
	 * @param string $search_key
	 * @returns array
	 */
    public function filter_table_data($data, $search_key)
    {
        $filtered_table_data = array_values(
            array_filter(
                $data,
                function ($row) use ($search_key) {
                    foreach ($row as $row_val) {
                        if (!is_array($row_val)) {
                            if (stripos($row_val, $search_key) !== false) {
                                return true;
                            }
                        }
                    }
                }
            )
        );

        return $filtered_table_data;

    }

    public function process_action()
    {

        if (!isset($_REQUEST['post'])) {
            return;
        }
        if (false == ($current_action = $this->current_action())) {
            return;
        }
        if ((!is_array($_REQUEST['post']) && !filter_var($_REQUEST['post'], FILTER_VALIDATE_INT)) || (is_array(
                    $_REQUEST['post']
                ) && !wp_verify_nonce($_REQUEST['_wpnonce'], 'bulk-'.$this->_args['plural'])) || (!is_array(
                    $_REQUEST['post']
                ) && $current_action == 'delete' && !wp_verify_nonce(
                    $_REQUEST[$this->deletenoncename],
                    $this->deletenonceaction
                )) || (filter_var(
                    $_REQUEST['post'],
                    FILTER_VALIDATE_INT
                ) && $current_action == 'publish' && !wp_verify_nonce(
                    $_REQUEST[$this->publishnoncename],
                    $this->publishnonceaction
                ))
        ) {
            wp_die('Vous n’avez pas les droits suffisants pour continuer sur cette page !');
        }
        if ('delete' == $current_action) {
            $this->action_delete($_REQUEST['post']);
        }
        if ('publish' == $current_action) {
            $this->action_publish($_REQUEST['post']);
        }
    }

    /**
     * Override the parent columns method. Defines the columns to use in your listing table
     *
     * @return Array
     */
    public
    function get_columns()
    {
        $columns = array(
            'cb' => '<input type="checkbox" />', // Render a checkbox instead of text.
            'ID' => 'ID',
            'login' => 'Login',
            'billing_first_name' => 'Prénom',
            'billing_last_name' => 'Nom',
            'email' => 'Email',
            'contact' => 'Contacts',
            'judokas' => 'Judokas',
        );

        return $columns;
    }

    /**
     * Define which columns are hidden
     *
     * @return Array
     */
    public
    function get_hidden_columns()
    {
        return array('ID', 'nicename', 'display_name', 'contacts_list', 'judokas_list');
    }

    /**
     * Define the sortable columns
     *
     * @return Array
     */
    public
    function get_sortable_columns()
    {
        return array(
            'billing_last_name' => array('billing_last_name', false)
        ,
            'login' => array('login', false),
        );
    }

    /**
     * Get value for checkbox column.
     *
     * @param object $item A singular item (one full row's worth of data).
     * @return string Text to be placed inside the column <td>.
     */
    protected
    function column_cb(
        $item
    ) {
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            $this->_args['singular'],  // Let's simply repurpose the table's singular label ("movie").
            $item['ID']                // The value of the checkbox should be the record's ID.
        );
    }

    /**
     * Get title column value.
     *
     * @param object $item A singular item (one full row's worth of data).
     * @return string Text to be placed inside the column <td>.
     */
    protected
    function column_login(
        $item
    ) {
        $page = wp_unslash($_REQUEST['page']); // WPCS: Input var ok.
        // Build edit row action.
        $edit_query_args = array(
            'page' => $page,
            'action' => 'edit',
            'member' => $item['ID'],
        );
        $actions['edit'] = sprintf(
            '<a href="%1$s">%2$s</a>',
            esc_url(wp_nonce_url(add_query_arg($edit_query_args, 'admin.php'), 'editmember_'.$item['ID'])),
            _x('Edit', 'List member row action', 'massiliajudo_member_list')
        );
        // Build delete row action.
        $delete_query_args = array(
            'page' => $page,
            'action' => 'delete',
            'member' => $item['ID'],
        );
        $actions['delete'] = sprintf(
            '<a href="%1$s">%2$s</a>',
            esc_url(wp_nonce_url(add_query_arg($delete_query_args, 'admin.php'), 'deletemember_'.$item['ID'])),
            _x('Delete', 'List member row action', 'massiliajudo_member_list')
        );

        // Return the title contents.
        return sprintf(
            '%1$s <span style="color:silver;">(id:%2$s)</span>%3$s',
            $item['login'],
            $item['ID'],
            $this->row_actions($actions)
        );
    }

    /**
     * Get the table data
     *
     * @return Array
     */
    private
    function table_data()
    {
        $data = array();
        $data = MassiliaJudo_Member_DB::getList();
        if (!empty($data)) {
            foreach ($data as $key => $user) {
                $details = MassiliaJudo_Member_DB::getDetailMemberById($user['ID']);
                if (!empty($details)) {
                    $this->buildDetails($data, $key, $details);
                }

                $contacts = MassiliaJudo_Contact_DB::getContactByUserId($user['ID'], ARRAY_A);
                if (!empty($contacts)) {
                    $this->buildContacts($data, $key, $contacts);
                    $data[$key]['contacts_list'] = $contacts;
                } else {
                    $data[$key]['contacts'] = '';
                    $data[$key]['contacts_list'] = [];
                }
                $judokas = MassiliaJudo_Judoka_DB::getJudokasByUserId($user['ID'], ARRAY_A);
                if (!empty($judokas)) {
                    $this->buildJudokas($data, $key, $judokas);
                    $data[$key]['judokas_list'] = $judokas;
                } else {
                    $data[$key]['judokas'] = '';
                    $data[$key]['judokas_list'] = [];
                }
            }
        }

        return $data;
    }

    /**
     * @param $data
     * @param $key
     * @param $details
     * @return mixed
     */
    public
    function buildDetails(
        &$data,
        $key,
        $details
    ) {
        if (!empty($details)) {
            foreach ($details as $detail) {
                if ($detail['meta_key'] == 'billing_first_name') {
                    $data[$key]['billing_first_name'] = $detail['meta_value'];
                } elseif ($detail['meta_key'] == 'billing_last_name') {
                    $data[$key]['billing_last_name'] = $detail['meta_value'];
                }
            }
        }

        return $data;
    }

    /**
     * @param $data
     * @param $key
     * @param $contacts
     */
    public
    function buildContacts(
        &$data,
        $key,
        $contacts
    ) {
        $concat = '';
        if (!empty($contacts)) {
            foreach ($contacts as $contact) {
                $delimiter = ' - ';
                if ($concat == '') {
                    $delimiter = '';
                }
                $concat .= $delimiter.$contact['firstname'].' '.$contact['lastname'].' ('.$contact['phoneNumber'].')';
            }
        }
        $data[$key]['contact'] = $concat;
    }

    /**
     * @param $data
     * @param $key
     * @param $contacts
     */
    public
    function buildJudokas(
        &$data,
        $key,
        $judokas
    ) {
        $concat = '';
        if (!empty($judokas)) {
            foreach ($judokas as $judoka) {
                $delimiter = ' - ';
                if ($concat == '') {
                    $delimiter = '';
                }
                $concat .= $delimiter.$judoka['firstname'].' '.$judoka['lastname'];
            }
        }
        $data[$key]['judokas'] = $concat;
    }

    /**
     * Define what data to show on each column of the table
     *
     * @param  Array $item Data
     * @param  String $column_name - Current column name
     *
     * @return Mixed
     */
    public
    function column_default(
        $item,
        $column_name
    ) {
        switch ($column_name) {
            case 'ID':
            case 'login':
            case 'billing_first_name':
            case 'billing_last_name':
            case 'email':
            case 'contact':
            case 'judokas':
                return $item[$column_name];
            default:
                return print_r($item, true);
        }
    }

    /**
     * @param $item
     * @return string
     */
    function column_name($item)
    {

        $actions = array(
            'edit' => sprintf('<a href="?page=%s&action=%s&plugin=%s">Edit</a>', 'kvcodes', 'edit', $item['ID']),
            'delete' => sprintf('<a href="?page=%s&action=%s&plugin=%s">Delete</a>', 'kvcodes', 'delete', $item['ID']),
        );

        return sprintf(
            '%1$s <span style="color:silver ; display : none;">(id:%2$s)</span>%3$s',
            /*$1%s*/
            $item['name'],
            /*$2%s*/
            $item['ID'],
            /*$3%s*/
            $this->row_actions($actions)
        );
    }


    /**
     * Allows you to sort the data by the variables set in the $_GET
     *
     * @return Mixed
     */
    private
    function sort_data(
        $a,
        $b
    ) {

        // Set defaults
        $orderby = 'ID';
        $order = 'asc';
        // If orderby is set, use this as the sort column
        if (!empty($_GET['orderby'])) {
            $orderby = $_GET['orderby'];
        }
        // If order is set use this as the order
        if (!empty($_GET['order'])) {
            $order = $_GET['order'];
        }

        if ($orderby == 'ID') {
            return $this->compareInt($a[$orderby], $b[$orderby], $order);
        } else {
            return $this->compareString($a[$orderby], $b[$orderby], $order);
        }
    }

    /**
     * @param $a
     * @param $b
     * @param $order
     * @return int
     */
    private
    function compareInt(
        $a,
        $b,
        $order
    ) {
        if ($a == $b) {
            return 0;
        }

        if ($order === 'asc') {
            return ($a < $b) ? -1 : 1;

            return $result;
        }

        return ($a > $b) ? -1 : 1;
    }

    /**
     * @param $a
     * @param $b
     * @param $order
     * @return int
     */
    private
    function compareString(
        $a,
        $b,
        $order
    ) {
        $result = strcmp(strtolower($a), strtolower($b));

        if ($order === 'asc') {
            return $result;
        }

        return -$result;
    }

    /**
     * @return array
     */
    function get_bulk_actions()
    {
        $actions = array(
            'delete' => 'Delete',
        );

    }

    /**
     *
     */
    function process_bulk_action()
    {
        if ('edit' === $this->current_action()) {
            $nonce = wp_unslash($_REQUEST['_wpnonce']);
            // verify the nonce.
            if (!wp_verify_nonce($nonce, 'editmember_'.$_REQUEST['member'])) {
                $this->invalid_nonce_redirect();
            } else {
                $this->edit_member();
                $this->graceful_exit();
            }

        }

    }

    public
    function edit_member()
    {
        include_once('views/edit_member.php');
    }

    /**
     * Stop execution and exit
     *
     * @since    1.0.0
     * @return void
     */
    public
    function graceful_exit()
    {
        exit;
    }

    /**
     * Die when the nonce check fails.
     *
     * @since    1.0.0
     * @return void
     */
    public
    function invalid_nonce_redirect()
    {
        wp_die(
            __('Invalid Nonce', $this->plugin_text_domain),
            __('Error', $this->plugin_text_domain),
            array(
                'response' => 403,
                'back_link' => esc_url(
                    add_query_arg(array('page' => wp_unslash($_REQUEST['page'])), admin_url('users.php'))
                ),
            )
        );
    }

    public
    function displayListMember()
    {
        $this->prepare_items();
        include_once('views/list_member.php');
    }

}