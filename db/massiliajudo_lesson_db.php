<?php


class MassiliaJudo_Lesson_DB
{
    /**
     * @param $dojoId
     * @return array|null|object
     */
    public static function getListByDojoId($dojoId)
    {
        global $wpdb;

        $sql =<<<SQL
SELECT le.id, le.categorieId, cat.name, cat.dateMin, cat.dateMax, le.nbrPlaceMax, le.nbrPlaceLeft
FROM {$wpdb->prefix}massiliajudo_lessons AS le 
INNER JOIN {$wpdb->prefix}massiliajudo_categories AS cat ON cat.id = le.categorieId AND cat.actif = 1
WHERE le.dojoId = $dojoId
AND le.actif = 1
ORDER BY cat.dateMin DESC
SQL;
       return $wpdb->get_results($sql);

    }

    /**
     * @param $lessonId
     * @return bool
     */
    public static function updateNbrPlace($lessonId, $nbrPlace){
        global $wpdb;

        $ret = $wpdb->update(
            $wpdb->prefix.'massiliajudo_lessons',
            array(
                nbrPlaceleft => $nbrPlace
            ),
            array('id'=> $lessonId)
        );
        if($ret == 1) {
            return true;
        }
        return false;
    }

}