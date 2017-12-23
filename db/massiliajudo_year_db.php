<?php


class MassiliaJudo_Year_DB
{
    /**
     * @return array|null|object
     */
    public static function getActiveYear()
    {
        global $wpdb;

        $sql =<<<SQL
SELECT *
FROM {$wpdb->prefix}massiliajudo_years AS y 
WHERE y.actif = 1
SQL;

        return $wpdb->get_row($sql);

    }

}