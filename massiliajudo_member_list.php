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
        $perPage = 10;
        $columns = $this->get_columns();
        $hidden = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);

//        $this->process_bulk_action();

        $data = $this->table_data();
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
    public function get_columns()
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
    public function get_hidden_columns()
    {
        return array('ID','nicename', 'display_name', 'contacts_list', 'judokas_list');
    }

    /**
     * Define the sortable columns
     *
     * @return Array
     */
    public function get_sortable_columns()
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
     * REQUIRED if displaying checkboxes or using bulk actions! The 'cb' column
     * is given special treatment when columns are processed. It ALWAYS needs to
     * have it's own method.
     *
     * @param object $item A singular item (one full row's worth of data).
     * @return string Text to be placed inside the column <td>.
     */
    protected function column_cb($item)
    {
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            $this->_args['singular'],  // Let's simply repurpose the table's singular label ("movie").
            $item['ID']                // The value of the checkbox should be the record's ID.
        );
    }

    /**
     * Get title column value.
     *
     * Recommended. This is a custom column method and is responsible for what
     * is rendered in any column with a name/slug of 'title'. Every time the class
     * needs to render a column, it first looks for a method named
     * column_{$column_title} - if it exists, that method is run. If it doesn't
     * exist, column_default() is called instead.
     *
     * This example also illustrates how to implement rollover actions. Actions
     * should be an associative array formatted as 'slug'=>'link html' - and you
     * will need to generate the URLs yourself. You could even ensure the links are
     * secured with wp_nonce_url(), as an expected security measure.
     *
     * @param object $item A singular item (one full row's worth of data).
     * @return string Text to be placed inside the column <td>.
     */
    protected function column_login($item)
    {
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
            _x('Edit', 'List table row action', 'wp-list-table-example')
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
            _x('Delete', 'List table row action', 'wp-list-table-example')
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
    private function table_data()
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
    public function buildDetails(&$data, $key, $details)
    {
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
    public function buildContacts(&$data, $key, $contacts)
    {
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
    public function buildJudokas(&$data, $key, $judokas)
    {
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
    public function column_default($item, $column_name)
    {
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
    private function sort_data($a, $b)
    {

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
    private function compareInt($a, $b, $order)
    {
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
    private function compareString($a, $b, $order)
    {
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

        return $actions;
    }

    /**
     *
     */
    function process_bulk_action()
    {

        if ('delete' === $this->current_action()) {
            //  wp_die('Items deleted (or they would be if we had items to delete)!');
            foreach ($_GET['id'] as $id) {
                //$id will be a string containing the ID of the video
                //i.e. $id = "123";
                delete_this_video($id);
            }
        }

    }
}