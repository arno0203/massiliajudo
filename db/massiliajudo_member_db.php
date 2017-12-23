<?php

/**
 * Created by PhpStorm.
 * User: ado
 * Date: 10/12/17
 * Time: 17:31
 */
class MassiliaJudo_Member_DB
{
    /**
     * @return array|null|object
     */
    public static function getList()
    {
        global $wpdb;

        $sql = <<<SQL
SELECT 
  ID
  , user_login AS login
  , user_nicename AS nicename
  , user_email AS email
  , display_name AS display_name
  , '' AS billing_first_name
  , '' AS billing_last_name 
FROM {$wpdb->prefix}users AS u 
SQL;

        return $wpdb->get_results($sql, ARRAY_A);

    }

    public static function getDetailMemberById($memberId)
    {
        global $wpdb;
        $memberId = intval($memberId);
        $sql = <<<SQL
SELECT * 
FROM {$wpdb->prefix}usermeta AS um
WHERE um.user_id = $memberId
AND meta_key in ('billing_first_name', 'billing_last_name');
SQL;

        return $wpdb->get_results($sql, ARRAY_A);

    }
}