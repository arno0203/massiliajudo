<?php

/**
 * Created by PhpStorm.
 * User: ado
 * Date: 04/07/17
 * Time: 22:08
 */
class MassiliaJudo_Member
{
    public function __construct()
    {
        add_action('admin_menu', array($this, 'add_admin_menu'), 20);

        wp_register_script('prefix_bootstrap', '//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js');
        wp_enqueue_script('prefix_bootstrap');

        wp_register_style('prefix_bootstrap', '//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css');
        wp_enqueue_style('prefix_bootstrap');
    }

    public function add_admin_menu()
    {
        add_submenu_page(
            'massiliajudo'
            ,'Membres'
            ,'Membre'
            ,'manage_options'
            ,'massiliajudo_members'
            , array($this, 'menu_html')
        );
    }

    public function menu_html(){
        echo '<h1>'.get_admin_page_title().'</h1>';

        $exampleListTable = new MassiliaJudo_Member_List();
       ?>
        <div class="wrap">
            <?php $exampleListTable->displayListMember(); ?>
        </div>
        <?php
    }

}