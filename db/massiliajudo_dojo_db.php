<?php


class MassiliaJudo_Dojo_DB
{
    /**
     * @return array|null|object
     */
    public static function getList()
    {
        global $wpdb;

        $sql =<<<SQL
SELECT dojo.id, dojo.name
FROM {$wpdb->prefix}massiliajudo_dojo AS dojo
WHERE dojo.actif = 1
ORDER BY dojo.order asc
SQL;
        return $wpdb->get_results($sql);

    }

    /**
     * @param $id
     * @return array|null|object|void
     */
    public static function getDojoById($id){
        global $wpdb;
        $sql =<<<SQL
SELECT id, value
FROM {$wpdb->prefix}massiliajudo_gender AS gr
WHERE gr.id = $id
SQL;
        return $wpdb->get_row($sql);
    }
}