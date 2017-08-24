<?php


class MassiliaJudo_Contact_DB
{
    /**
     * @return array|null|object
     */
    public static function getContactByUserId()
    {
        global $wpdb;
        $current_user = wp_get_current_user();

        $sql = <<<SQL
SELECT *
FROM {$wpdb->prefix}massiliajudo_contact AS ju
WHERE userId= $current_user->ID
AND actif = 1
SQL;

        return $wpdb->get_results($sql);

    }

    /**
     * @return array|null|object
     */
    public static function getContactsById($contactId = null)
    {
        if (!is_null($contactId)) {

            global $wpdb;
            $current_user = wp_get_current_user();

            $sql = <<<SQL
SELECT *
FROM {$wpdb->prefix}massiliajudo_contact AS ju
WHERE id= $contactId 
AND actif = 1
SQL;

            return $wpdb->get_row($sql);
        }

        return null;
    }

    /**
     * @param $datas
     * @return int
     */
    public static function saveContact($datas)
    {
        global $wpdb;

        $current_user = wp_get_current_user();

        $wpdb->insert(
            $wpdb->prefix.'massiliajudo_contact',
            array(
                lastname => $datas['MassiliaJudo_Lastname']
            ,
                firstname => $datas['MassiliaJudo_Firstname']
            ,
                email => $datas['MassiliaJudo_Email']
            ,
                phoneNumber => $datas['MassiliaJudo_PhoneNumber']
            ,
                address => $datas['MassiliaJudo_Address']
            ,
                city => $datas['MassiliaJudo_City']
            ,
                cp => $datas['MassiliaJudo_Cp']
            ,
                statusId => $datas['MassiliaJudo_Status']
            ,
                userId => $current_user->ID
            ,
                genderId => $datas['MassiliaJudo_Gender'],
            )
        );

        return $wpdb->insert_id;
    }

    /**
     * @param $datas
     * @return bool
     */
    public static function updateContact($datas)
    {
        global $wpdb;

        $current_user = wp_get_current_user();

        $ret = $wpdb->update(
            $wpdb->prefix.'massiliajudo_contact',
            array(
                lastname => $datas['MassiliaJudo_Lastname']
            ,
                firstname => $datas['MassiliaJudo_Firstname']
            ,
                email => $datas['MassiliaJudo_Email']
            ,
                phoneNumber => $datas['MassiliaJudo_PhoneNumber']
            ,
                address => $datas['MassiliaJudo_Address']
            ,
                city => $datas['MassiliaJudo_City']
            ,
                cp => $datas['MassiliaJudo_Cp']
            ,
                statusId => $datas['MassiliaJudo_Status']
            ,
                userId => $current_user->ID

            ,
                genderId => $datas['MassiliaJudo_Gender'],
            ),
            array('id' => $datas['MassiliaJudo_ContactId'], 'userid' => $current_user->ID)
        );
        if ($ret == 1) {
            return true;
        }

        return false;

    }

    public static function delContact($contactId = null)
    {
        global $wpdb;

        $current_user = wp_get_current_user();

        $ret = $wpdb->update(
            $wpdb->prefix.'massiliajudo_contact',
            array(
                actif => 0,
            ),
            array('id' => $contactId, 'userid' => $current_user->ID, 'actif' => 1)
        );

        if ($ret == 1) {
            return true;
        }

        return false;
    }

    /**
     * @param null $id
     * @param null $contactId
     * @return bool
     */
    public static function isMyContact($id = null, $contactId = null)
    {
        if (!is_null($id) && !is_null($contactId)) {
            global $wpdb;

            $sql = <<<SQL
SELECT id 
FROM  {$wpdb->prefix}massiliajudo_contact AS ju
WHERE userid = $id AND id = $contactId
SQL;
            $ret = $wpdb->get_results($sql);

            if (is_null($ret)) {
                return false;
            } elseif (intval($ret[0]->id) === intval($contactId)) {
                return true;
            }

            return false;
        }
    }

}