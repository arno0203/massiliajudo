<?php


class MassiliaJudo_Judoka_DB
{
    /**
     * @return array|null|object
     */
    public static function getJudokasByUserId($userId, $output_type= OBJECT)
    {
        global $wpdb;

        $sql = <<<SQL
SELECT ju.*, ge.name AS gender, do.name AS dojo, cat.name AS category, ye.year AS year
FROM {$wpdb->prefix}massiliajudo_judoka AS ju
INNER JOIN {$wpdb->prefix}massiliajudo_gender AS ge ON ge.id = ju.genderId
INNER JOIN {$wpdb->prefix}massiliajudo_dojo AS do ON do.id = ju.dojoId
LEFT JOIN {$wpdb->prefix}massiliajudo_registration AS re ON re.judokaId = ju.id
LEFT JOIN {$wpdb->prefix}massiliajudo_lessons AS le ON le.id = re.lessonId
LEFT JOIN {$wpdb->prefix}massiliajudo_categories AS cat ON cat.id = le.categorieId
LEFT JOIN {$wpdb->prefix}massiliajudo_years AS ye ON ye.id = re.yearId
WHERE ju.userId= $userId
AND ju.actif = 1
SQL;
       return $wpdb->get_results($sql, $output_type);

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