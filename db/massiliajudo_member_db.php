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

    /**
     * @param $memberId
     * @return array|null|object
     */
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

    /**
     * @param $memberId
     * @return array|null|object
     */
    public function getFullMemberById($memberId){
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
WHERE ID = $memberId
SQL;
        $data = [];
        $data =  $wpdb->get_results($sql, ARRAY_A);
        if(!empty($data)){
            $data = $data[0];
        }
        $details = MassiliaJudo_Member_DB::getDetailMemberById($memberId);
        if(!empty($details)){
            foreach ($details as $detail){
                if($detail['meta_key'] == 'billing_first_name'){
                    $data['firstname'] = $detail['meta_value'];
                }elseif ($detail['meta_key'] == 'billing_last_name'){
                    $data['lastname'] = $detail['meta_value'];
                }
            }
        }
        $contacts = MassiliaJudo_Contact_DB::getContactByUserId($memberId, ARRAY_A);
        if(!empty($contacts)){
            $data['contacts'] = $contacts;
        }
        $judokas = MassiliaJudo_Judoka_DB::getJudokasByUserId($memberId, ARRAY_A);
        if(!empty($judokas)){
            $data['judokas'] = $judokas;
        }
        return $data;
    }
}