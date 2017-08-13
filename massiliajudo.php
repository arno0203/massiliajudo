<?php
/*
Plugin Name: Massilia Judo
Plugin URI: http://massiliajudo-plugin.com
Description: PLugin de gestion d'abonnement de judokas
Version: 0.1
Author: Arnaud Dollois
Author URI: http://dollois.com
License: GPL2
*/

class MassiliaJudo_Plugin{

    public function __construct()
    {
        include_once plugin_dir_path( __FILE__ ).'/massiliajudo_judoka.php';

        new MassiliaJudo_Judoka();
        register_activation_hook(__FILE__, array('MassiliaJudo_Judoka', 'install'));
        register_uninstall_hook(__FILE__, array('MassiliaJudo_Judoka', 'uninstall'));

        include_once plugin_dir_path( __FILE__ ).'/massiliajudo_contact.php';
        include_once plugin_dir_path( __FILE__ ).'/massiliajudo_contact_list.php';

        new MassiliaJudo_Contact();
        register_activation_hook(__FILE__, array('MassiliaJudo_Contact', 'install'));
        register_uninstall_hook(__FILE__, array('MassiliaJudo_Contact', 'uninstall'));

        include_once plugin_dir_path( __FILE__ ).'/massiliajudo_myaccount.php';
        new MassiliaJudo_Myaccount();

        include_once plugin_dir_path( __FILE__ ).'/db/massiliajudo_judoka_db.php';

        include_once plugin_dir_path( __FILE__ ).'/db/massiliajudo_contact_db.php';

        include_once plugin_dir_path( __FILE__ ).'/db/massiliajudo_gender_db.php';

        include_once plugin_dir_path( __FILE__ ).'/db/massiliajudo_dojo_db.php';

        include_once plugin_dir_path( __FILE__ ).'/massiliajudo_form_builder.php';
        new MassiliaJudo_Form_Builder();

        //Administration
        add_action('admin_menu', array($this, 'add_admin_menu'));

        //Ajax
        wp_localize_script( 'script_handle', 'MasssiliaJudoAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php')));

    }

    public function add_admin_menu()
    {
        add_menu_page('Massilia Judo'
            , 'Massilia Judo'
            , 'manage_options'
            , 'massiliajudo'
            , array($this, 'menu_html')
        );
    }

    public function menu_html()
    {
        echo '<h1>'.get_admin_page_title().'</h1>';
        echo '<p>Bienvenue sur la page d\'accueil du plugin Massilia Judo</p>';
    }

}
new MassiliaJudo_Plugin();