<?php


class MassiliaJudo_Judoka_DB
{
    /**
     * @return array|null|object
     */
    public static function getJudokasById()
    {
        global $wpdb;
        $current_user = wp_get_current_user();

        $sql =<<<SQL
SELECT *
FROM {$wpdb->prefix}massiliajudo_judoka AS ju
WHERE userId= $current_user->ID
SQL;
        return $wpdb->get_results($sql);

    }
}