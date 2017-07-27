<?php

/**
 * Created by PhpStorm.
 * User: ado
 * Date: 07/07/17
 * Time: 21:49
 */
if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class MassiliaJudo_Contact_List extends WP_List_Table
{
    /**
     * Prepare the items for the table to process
     *
     * @return Void
     */
    public function prepare_items()
    {
        $columns = $this->get_columns();
        $hidden = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();
        $data = $this->table_data();
        usort( $data, array( &$this, 'sort_data' ) );
        $perPage = 2;
        $currentPage = $this->get_pagenum();
        $totalItems = count($data);
        $this->set_pagination_args( array(
            'total_items' => $totalItems,
            'per_page'    => $perPage
        ) );
        $data = array_slice($data,(($currentPage-1)*$perPage),$perPage);
        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->items = $data;
    }
    /**
     * Override the parent columns method. Defines the columns to use in your listing table
     *
     * @return Array
     */
    public function get_columns()
    {
        $columns = array(
            'id'            => 'ID',
            'firstname'     => 'Prénom',
            'lastname'      => 'Nom',
            'email'         => 'Email',
            'phoneNumber'   => 'Téléphone',
            'nbrJudokas'    => 'Nbr Judokas'
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
        return array();
    }
    /**
     * Define the sortable columns
     *
     * @return Array
     */
    public function get_sortable_columns()
    {
        return array('title' => array('title', false));
    }
    /**
     * Get the table data
     *
     * @return Array
     */
    private function table_data()
    {
        $data = array();
        $data[] = array(
            'id'            => 1,
            'firstname'     => 'Caroline',
            'lastname'      => 'Dollois',
            'email'         => 'caroline@dollois.com',
            'phoneNumber'   => '+33 6 12 79 32 46',
            'statut'        => 'Mère',
            'nbrJudokas'    => '2'
        );
        $data[] = array(
            'id'            => 2,
            'firstname'     => 'Arnaud',
            'lastname'      => 'Dollois',
            'email'         => 'arnaud@dollois.com',
            'phoneNumber'   => '+33 6 12 79 32 46',
            'statut'        => 'Père',
            'nbrJudokas'    => '1'
        );

        return $data;
    }
    /**
     * Define what data to show on each column of the table
     *
     * @param  Array $item        Data
     * @param  String $column_name - Current column name
     *
     * @return Mixed
     */
    public function column_default( $item, $column_name )
    {
        switch( $column_name ) {
            case 'id':
            case 'firstname':
            case 'lastname':
            case 'email':
            case 'phoneNumber':
            case 'statut':
            case 'nbrJudokas':
                return $item[ $column_name ];
            default:
                return print_r( $item, true ) ;
        }
    }
    /**
     * Allows you to sort the data by the variables set in the $_GET
     *
     * @return Mixed
     */
    private function sort_data( $a, $b )
    {
        // Set defaults
        $orderby = 'title';
        $order = 'asc';
        // If orderby is set, use this as the sort column
        if(!empty($_GET['orderby']))
        {
            $orderby = $_GET['orderby'];
        }
        // If order is set use this as the order
        if(!empty($_GET['order']))
        {
            $order = $_GET['order'];
        }
        $result = strcmp( $a[$orderby], $b[$orderby] );
        if($order === 'asc')
        {
            return $result;
        }
        return -$result;
    }

}