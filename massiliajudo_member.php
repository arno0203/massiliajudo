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
    }

    public function add_admin_menu()
    {
        add_submenu_page(
            'massiliajudo'
            ,
            'Membres'
            ,
            'Membre'
            ,
            'manage_options'
            ,
            'massiliajudo_members'
            ,
            array($this, 'menu_html')
        );
    }

    public function menu_html(){
        echo '<h1>'.get_admin_page_title().'</h1>';
        echo '<p>Liste des Membres</p>';

        $exampleListTable = new MassiliaJudo_Member_List();
        $exampleListTable->prepare_items();

        ?>
        <div class="wrap">
            <?php $exampleListTable->display(); ?>
        </div>
        <?php
    }
}