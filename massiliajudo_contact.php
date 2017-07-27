<?php

/**
 * Created by PhpStorm.
 * User: ado
 * Date: 05/07/17
 * Time: 22:16
 */
class MassiliaJudo_Contact
{

    public function __construct()
    {
        add_action('admin_menu', array($this, 'add_admin_menu'), 20);
    }

    public function add_admin_menu()
    {
        add_submenu_page('massiliajudo'
            , 'Contacts'
            , 'Contacts'
            , 'manage_options'
            , 'massiliajudo_contacts'
            , array($this, 'menu_html'));
    }

    public function menu_html(){
        echo '<h1>'.get_admin_page_title().'</h1>';
        echo '<p>Liste des Contacts</p>';
        $exampleListTable = new MassiliaJudo_Contact_List();
        $exampleListTable->prepare_items();
        ?>
        <div class="wrap">
            <?php $exampleListTable->display(); ?>
        </div>
        <?php
    }

    public static function install()
    {
        global $wpdb;

        $wpdb->query(
            "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}massiliajudo_contact (id INT AUTO_INCREMENT PRIMARY KEY
, lastname VARCHAR(255) NOT NULL
, firstname VARCHAR(255) NOT NULL 
, email VARCHAR(255) NULL 
, phoneNumber VARCHAR(15)
, address VARCHAR(255) NOT NULL
, city VARCHAR(255) NOT NULL
, cp VARCHAR(5) NOT NULL 
, status VARCHAR(15) NOT NULL
, genderId INT(10) NOT NULL);"
        );
    }

    public static function uninstall()
    {
        global $wpdb;

        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}massiliajudo_contact;");
    }
}