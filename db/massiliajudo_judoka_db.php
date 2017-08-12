<?php


class MassiliaJudo_Judoka_DB
{
    /**
     * @return array|null|object
     */
    public static function getJudokasByUserId()
    {
        global $wpdb;
        $current_user = wp_get_current_user();

        $sql = <<<SQL
SELECT *
FROM {$wpdb->prefix}massiliajudo_judoka AS ju
WHERE userId= $current_user->ID
AND actif = 1
SQL;

        return $wpdb->get_results($sql);

    }

    /**
     * @return array|null|object
     */
    public static function getJudokasById($judokaId = null)
    {
        if (!is_null($judokaId)) {

            global $wpdb;
            $current_user = wp_get_current_user();

            $sql = <<<SQL
SELECT *
FROM {$wpdb->prefix}massiliajudo_judoka AS ju
WHERE id= $judokaId 
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
    public static function saveJudoka($datas)
    {
        global $wpdb;

        $current_user = wp_get_current_user();

        $wpdb->insert(
            $wpdb->prefix.'massiliajudo_judoka',
            array(
                lastname => $datas['MassiliaJudo_Lastname']
            ,
                firstname => $datas['MassiliaJudo_Firstname']
            ,
                email => $datas['MassiliaJudo_Email']
            ,
                birthdayDate => $datas['MassiliaJudo_Birthday']
            ,
                userId => $current_user->ID
            ,
                dojoId => $datas['MassiliaJudo_Dojo']
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
    public static function updateJudoka($datas)
    {
        global $wpdb;

        $current_user = wp_get_current_user();

        $ret = $wpdb->update(
            $wpdb->prefix.'massiliajudo_judoka',
            array(
                lastname => $datas['MassiliaJudo_Lastname']
            ,
                firstname => $datas['MassiliaJudo_Firstname']
            ,
                email => $datas['MassiliaJudo_Email']
            ,
                birthdayDate => $datas['MassiliaJudo_Birthday']
            ,
                userId => $current_user->ID
            ,
                dojoId => $datas['MassiliaJudo_Dojo']
            ,
                genderId => $datas['MassiliaJudo_Gender'],
            ),
            array('id'=> $datas['MassiliaJudo_JudokaId'], 'userid' => $current_user->ID)
        );
        var_dump($wpdb->last_query);
        if($ret == 1) {
            return true;
        }
        return false;

    }

    public static function delJudoka($judokaId = null){
        global $wpdb;

        $current_user = wp_get_current_user();

        $ret = $wpdb->update(
            $wpdb->prefix.'massiliajudo_judoka',
            array(
                actif => 0
            ),
            array('id'=> $judokaId, 'userid' => $current_user->ID, 'actif' => 1)
        );

        if($ret == 1) {
            return true;
        }
        return false;
    }

    /**
     * @param null $id
     * @param null $judokaId
     * @return bool
     */
    public static function isMyJodoka($id = null, $judokaId = null)
    {
        if (!is_null($id) && !is_null($judokaId)) {
            global $wpdb;

            $sql = <<<SQL
SELECT id 
FROM  {$wpdb->prefix}massiliajudo_judoka AS ju
WHERE userid = $id AND id = $judokaId
SQL;
            $ret = $wpdb->get_results($sql);

            if (is_null($ret)) {
                return false;
            } elseif (intval($ret[0]->id) === intval($judokaId)) {
                return true;
            }

            return false;
        }
    }

}