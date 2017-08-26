<?php


class MassiliaJudo_Gender_DB
{
    /**
     * @return array|null|object
     */
    public static function getList()
    {
        global $wpdb;

        $sql =<<<SQL
SELECT id, name
FROM {$wpdb->prefix}massiliajudo_gender AS gr
SQL;
        return $wpdb->get_results($sql);

    }

    /**
     * @param $id
     * @return array|null|object|void
     */
    public static function getGenderById($id){
        global $wpdb;
        $sql =<<<SQL
SELECT id, name
FROM {$wpdb->prefix}massiliajudo_gender AS gr
WHERE gr.id = $id
SQL;
        return $wpdb->get_row($sql);
    }
}