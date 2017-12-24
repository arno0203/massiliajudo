<?php


class MassiliaJudo_Registration_DB
{
    /**
     * @return array|null|object
     */
    public static function addRegistration($datas)
    {
        global $wpdb;

        $current_user = wp_get_current_user();

        $wpdb->insert(
            $wpdb->prefix.'massiliajudo_registration',
            array(
                yearId => intval($datas['yearId'])
            ,
                lessonId => intval($datas['lessonId'])
            ,
                judokaId => intval($datas['judokaId']),
            )
        );

        return $wpdb->insert_id;

    }

    public static function deleteRegistrationCurrentYear($judokaId, $yearId)
    {
        global $wpdb;

        $wpdb->delete(
            $wpdb->prefix.'massiliajudo_registration',
            array(
                'judokaId' => $judokaId,
                'yearId' => $yearId,
            ),
            array(
                '%d',
                '%d',
            )
        );

    }

}