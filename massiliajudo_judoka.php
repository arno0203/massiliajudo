<?php

/**
 * Created by PhpStorm.
 * User: ado
 * Date: 04/07/17
 * Time: 22:08
 */
class MassiliaJudo_Judoka
{
    public function __construct()
    {
        add_action('admin_menu', array($this, 'add_admin_menu'), 20);
        add_action('admin_init', array($this, 'register_settings'));
    }

    public function add_admin_menu()
    {
        add_submenu_page('massiliajudo'
            , 'Judokas'
            , 'Judokas'
            , 'manage_options'
            , 'massiliajudo_judokas'
            , array($this, 'menu_html'));
    }

    public function register_settings()
    {
        register_setting('massiliajudo_judokas_settings', 'massiliajudo_judokas_sender');
        register_setting('massiliajudo_judokas_settings', 'massiliajudo_judokas_object');
        register_setting('massiliajudo_judokas_settings', 'massiliajudo_judokas_content');

        add_settings_section('massiliajudo_judokas_section', 'Ajouter un judoka', array($this, 'section_html'), 'massiliajudo_judokas_settings');
        add_settings_field('massiliajudo_judokas_sender', 'Expéditeur', array($this, 'sender_html'), 'massiliajudo_judokas_settings', 'massiliajudo_judokas_section');
        add_settings_field('massiliajudo_judokas_object', 'Objet', array($this, 'object_html'), 'massiliajudo_judokas_settings', 'massiliajudo_judokas_section');
        add_settings_field('massiliajudo_judokas_content', 'Contenu', array($this, 'content_html'), 'massiliajudo_judokas_settings', 'massiliajudo_judokas_section');
    }

    public function sender_html()
    {?>
        <input type="text" name="massiliajudo_judokas_sender" value="<?php echo get_option('massiliajudo_judokas_sender') ?>"/>
    <?php
    }

    public function object_html()
    {?>
        <input type="text" name="massiliajudo_judokas_object" value="<?php echo get_option('massiliajudo_judokas_object') ?>"/>
    <?php
    }

    public function content_html()
    {?>
        <textarea name="massiliajudo_judokas_content"><?php echo get_option('massiliajudo_judokas_content')?></textarea>
    <?php
    }

    public function menu_html()
    {?>
        <h1><?php echo get_admin_page_title()?></h1>
        <p>Liste des Judokas</p>
        <form method="post" action="options.php">
            <?php settings_fields("massiliajudo_judokas_settings")?>
            <?php do_settings_sections("massiliajudo_judokas_settings")?>
            <?php submit_button()?>
        </form>
    <?php
    }

    public static function install()
    {
        global $wpdb;

        $wpdb->query(
            "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}massiliajudo_judoka (id INT AUTO_INCREMENT PRIMARY KEY
, `lastname` VARCHAR(255) NOT NULL
, `firstname` VARCHAR(255) NOT NULL 
, `email` VARCHAR(255) NULL 
, `birthdayDate` DATETIME NOT NULL
, `userId` BIGINT(20) NOT NULL
, `genderId` INT(10) NOT NULL
, `dojoId` INT(10) NOT NULL
, `actif` SMALLINT NOT NULL DEFAULT 1);"
        );

        $wpdb->query(
                "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}massiliajudo_dojo (`id` INT AUTO_INCREMENT PRIMARY KEY
, `name` VARCHAR(255) NOT NULL
, `actif` SMALLINT(1) NOT NULL DEFAULT 1 
, `order` SMALLINT(1) NULL );"
        );
        $wpdb->query(
            "INSERT INTO {$wpdb->prefix}massiliajudo_dojo (`name`, `actif`, `order`) VALUES 
              ('Saint Jérôme', 1,1), ('Saint Barnabé', 1, 2), ('La Pauline', 1, 3), ('Pélabon',0,4), ('La Visitation', 0, 5)"
        );

        $wpdb->query(
            "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}massiliajudo_gender (`id` INT AUTO_INCREMENT PRIMARY KEY
, `name` VARCHAR(255) NOT NULL );"
        );
        $wpdb->query(
                "INSERT INTO {$wpdb->prefix}massiliajudo_gender (`name`) VALUES ('Masculin'), ('Feminin')"
        );

        $wpdb->query(
            "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}massiliajudo_status (`id` INT AUTO_INCREMENT PRIMARY KEY
, `name` VARCHAR(255) NOT NULL );"
        );
        $wpdb->query(
            "INSERT INTO {$wpdb->prefix}massiliajudo_status (`name`) VALUES ('Mère'), ('Père'), ('Autre')"
        );

    }

    public static function uninstall()
    {
        global $wpdb;

        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}massiliajudo_judoka;");
        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}massiliajudo_dojo;");
        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}massiliajudo_gender;");
    }

    /**
     * @param $datas
     * @return array
     */
    public static function formatDatasToDb($datas){
        $ret = [];

        $ret = array_merge($datas);
        if(!empty($datas['MassiliaJudo_Firstname']) ){
            $ret['MassiliaJudo_Firstname'] = ucwords($datas['MassiliaJudo_Firstname']);
        }
        if(!empty($datas['MassiliaJudo_Lastname']) ){
            $ret['MassiliaJudo_Lastname'] = mb_strtoupper(MassiliaJudo_Myaccount::wd_remove_accents($datas['MassiliaJudo_Lastname']));
        }
        $date = DateTime::createFromFormat('d/m/Y',  $datas['MassiliaJudo_Birthday']);
        $ret['MassiliaJudo_Birthday'] = $date->format('Y-m-d');
        return $ret;
    }


    /**
     * @param $data
     * @return mixed
     */
    public static function formatDataToForm($data){
        $ret = clone $data;
        $date = new DateTime($ret->birthdayDate);
        $ret->birthdayDate = $date->format('d/m/Y');

        return $ret;
    }
}