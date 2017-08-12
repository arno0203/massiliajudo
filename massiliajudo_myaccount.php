<?php

/**
 * Created by PhpStorm.
 * User: ado
 * Date: 18/07/17
 * Time: 14:49
 */
class MassiliaJudo_Myaccount
{
    protected $massilia_myaccount_page_url;
    protected $massilia_myaccount_page_id;

    public function __construct()
    {
        add_filter('woocommerce_account_menu_items', array($this, 'massilia_account_menu_items'), 10, 1);
        add_action('init', array($this, 'massilia_add_my_account_endpoint'));
        add_action('woocommerce_account_judokas_endpoint', array($this, 'massilia_judokas_endpoint_content'), 10, 1);

        add_action('init', array($this, 'massilia_add_edit_judoka_endpoint'));
        add_action('woocommerce_account_edit_judoka_endpoint', array($this, 'massilia_edit_judoka_endpoint_content'));

        add_action('init', array($this, 'massilia_del_judoka_endpoint'));
        add_action('woocommerce_account_del_judoka_endpoint', array($this, 'massilia_del_judoka_endpoint_content'));


        //Formulaire front edit judoka
        add_action('template_redirect', array($this, 'traitement_formulaire_edit_judoka'), 10, 1);
        add_action('template_redirect', array($this, 'traitement_formulaire_del_judoka'), 10, 1);

        //Css
        add_action('wp_enqueue_scripts', array($this, 'prefix_add_script_myaccount'));

        //Ajax
        add_action('wp_enqueue_scripts', array($this, 'MassiliaJudo_myaccount_scripts'));
        add_action('wp_ajax_edit_judoka_ajax_callback', array($this, 'check_data_edit_judoka_callback'));

    }

    public function MassiliaJudo_myaccount_scripts()
    {
        $js_directory = plugin_dir_url(__FILE__).'assets/js/';
        $ajax_directory = plugin_dir_url(__FILE__);

        wp_enqueue_script('script-name', $js_directory.'myaccount.js', array('jquery'), '1.0.0', true);
        wp_localize_script(
            'script-name',
            'MassiliaJudoAjax',
            array(
                // URL to wp-admin/admin-ajax.php to process the request
                'ajaxurl' => admin_url('admin-ajax.php'),
                // generate a nonce with a unique ID "myajax-post-comment-nonce"
                // so that you can check it later when an AJAX request is sent
                'security' => wp_create_nonce('MassiliaJudoAjax'),
            )
        );

    }

    /**
     * Account menu items
     *
     * @param arr $items
     * @return arr
     */
    public function massilia_account_menu_items($items)
    {
        $ret = [];
        foreach ($items as $key => $item) {
            $ret[$key] = $item;
            if ($key == 'dashboard') {
                $ret['judokas'] = __('Judokas', 'iconic');
            }
        }

        $this->massilia_myaccount_page_id = get_option('woocommerce_myaccount_page_id');
        if ($this->massilia_myaccount_page_id) {
            $this->massilia_myaccount_page_url = get_permalink($this->massilia_myaccount_page_id);
        }

        return $ret;
    }


    /**
     * Add endpoint
     */
    public function massilia_add_my_account_endpoint()
    {
        add_rewrite_endpoint('judokas', EP_PAGES);
    }

    /**
     * Add endpoint
     */
    public function massilia_add_edit_judoka_endpoint()
    {
        add_rewrite_endpoint('edit_judoka', EP_PAGES);
    }

    /**
     * Add endpoint
     */
    public function massilia_del_judoka_endpoint()
    {
        add_rewrite_endpoint('del_judoka', EP_PAGES);
    }

    /**
     * Information content
     */
    public function massilia_edit_judoka_endpoint_content($judokaId = null)
    {
        $current_user = wp_get_current_user();
        $genderId = $dojoId = $firstName = $lastName = $email = $birthday = '';

        if (!is_null($judokaId)) {
            if (MassiliaJudo_Judoka_DB::isMyJodoka($current_user->ID, $judokaId)) {
                $datas = MassiliaJudo_Judoka::formatDataToForm(MassiliaJudo_Judoka_DB::getJudokasById($judokaId));

                $genderId = intval($datas->genderId);
                $dojoId = intval($datas->dojoId);
                $firstName = $datas->firstname;
                $lastName = $datas->lastname;
                $email = $datas->email;
                $birthday = $datas->birthdayDate;
            }
        }

        $genderSelect = MassiliaJudo_Form_Builder::buildSelect(
            'MassiliaJudo_Gender_DB',
            'MassiliaJudo_Gender',
            $genderId,
            'MassiliaJudo_Gender',
            'Genre'
        );
        $dojoSelect = MassiliaJudo_Form_Builder::buildSelect(
            'MassiliaJudo_Dojo_DB',
            'MassiliaJudo_Dojo',
            $dojoId,
            'MassiliaJudo_Dojo',
            'Dojo'
        );
        $firstNameText = MassiliaJudo_Form_Builder::buildText(
            'MassiliaJudo_Firstname',
            $firstName,
            'MassiliaJudo_Firstname',
            'Prénom',
            'Rentrez le prénom du judoka',
            'error'
        );
        $lastNameText = MassiliaJudo_Form_Builder::buildText(
            'MassiliaJudo_Lastname',
            $lastName,
            'MassiliaJudo_Lastname',
            'Nom',
            'Rentrez le nom du judoka'
        );
        $emailText = MassiliaJudo_Form_Builder::buildText(
            'MassiliaJudo_Email',
            $email,
            'MassiliaJudo_Email',
            'Email',
            'Rentrez l\'email du judoka'
        );
        $birthdayDate = MassiliaJudo_Form_Builder::buildDate(
            'MassiliaJudo_Birthday',
            $birthday,
            'MassiliaJudo_Birthday',
            'Date d\'anniversaire',
            'Date de naissance'
        );
        $submit = MassiliaJudo_Form_Builder::buildSubmit('submit_edit_judoka', 'MassiliaJudo_Submit', 'Validez');
        $contactHidden = MassiliaJudo_Form_Builder::buildHidden(
            'MassiliaJudo_ContactId',
            'MassiliaJudo_ContactId',
            $current_user->ID
        );

        $judokaIdHidden = MassiliaJudo_Form_Builder::buildHidden(
            'MassiliaJudo_JudokaId',
            'MassiliaJudo_JudokaId',
            $judokaId
        );

        $html = '<form action="#" method="POST" class="">%s
    <p>
	    %s
	</p>
	<p>
	    %s
	</p>
	<p>
	    %s
	</p>
	<p>
	    %s
	</p>
	<p>
	    %s
	</p>
	<p>
	    %s
	</p>
	<p>
	    %s
	</p>
	<span>
	    %s%s
	</span>
</form>';

        echo sprintf(
            $html,
            wp_nonce_field('MassiliaJudo', 'massiliajudo_editjudoka', true, false),
            $genderSelect,
            $firstNameText,
            $lastNameText,
            $emailText,
            $dojoSelect,
            $birthdayDate,
            $submit,
            $contactHidden,
            $judokaIdHidden
        );

    }

    public function traitement_formulaire_edit_judoka()
    {
        if (isset($_POST['submit_edit_judoka']) && isset($_POST['massiliajudo_editjudoka'])) {

            if (wp_verify_nonce($_POST['massiliajudo_editjudoka'], 'MassiliaJudo')) {
                $errors = $this->chekData($_POST);
                if (!empty($errors)) {
                    var_dump("Error traitement_formulaire_edit_judoka");
                } else {
                    $datas = MassiliaJudo_Judoka::formatDatasToDb($_POST);
                    
                    if (is_null($_POST['MassiliaJudo_JudokaId'])) {
                        $judoka_id = MassiliaJudo_Judoka_DB::saveJudoka($datas);
                    } else {
                        MassiliaJudo_Judoka_DB::updateJudoka($datas);
                    }
                    wp_redirect( home_url('/my-account/judokas/') );
                    exit;
                }
            }
        }
    }

    /**
     * Information content
     */
    public function massilia_del_judoka_endpoint_content($judokaId = null)
    {
        $current_user = wp_get_current_user();

        if (!is_null($judokaId) && MassiliaJudo_Judoka_DB::isMyJodoka($current_user->ID, $judokaId)) {
            $judokaObj = MassiliaJudo_Judoka_DB::getJudokasById($judokaId);

            $html = <<<HTML
<h2>Suppression d'un Judoka</h2>
<p>Vous êtes sur le point du supprimer %s de votre compte.</p>
<p>Confirmez-vous cette suppression?</p>
<form action="#" method="POST" class="">%s%s
    <p>
	    %s
	</p>
	<p>
	    %s
	</p>
</form>
HTML;
            if ($judokaObj->genderID == 1) {
                $judokaString = 'le judoka '.$judokaObj->firstname.' '.$judokaObj->lastname;
            } else {
                $judokaString = 'la judokate '.$judokaObj->firstname.' '.$judokaObj->lastname;
            }
            $judokaIdHidden = MassiliaJudo_Form_Builder::buildHidden(
                'MassiliaJudo_JudokaId',
                'MassiliaJudo_JudokaId',
                $judokaId
            );
            $cancel = MassiliaJudo_Form_Builder::buildSubmit('cancel_del_judoka', 'MassiliaJudo_Cancel_Del', 'Annulez');
            $submit = MassiliaJudo_Form_Builder::buildSubmit(
                'submit_del_judoka',
                'MassiliaJudo_Submit_Del',
                'Supprimez'
            );
            echo sprintf(
                $html,
                $judokaString,
                wp_nonce_field('MassiliaJudo', 'massiliajudo_deljudoka', true, false),
                $judokaIdHidden,
                $cancel,
                $submit
            );
        }

    }

    public function traitement_formulaire_del_judoka()
    {

        if (isset($_POST['submit_del_judoka']) && isset($_POST['massiliajudo_deljudoka'])) {

            if (wp_verify_nonce($_POST['massiliajudo_deljudoka'], 'MassiliaJudo')) {
                if(MassiliaJudo_Judoka_DB::delJudoka($_POST['MassiliaJudo_JudokaId'])){
                    wp_redirect( home_url('/my-account/judokas/') );
                    exit;
                }
            }
        }
    }

    /**
     * @param $datas
     * @return array
     */
    private function chekData($datas)
    {
        $errors = [];
        if (intval($datas['MassiliaJudo_Gender']) === 0) {
            $errors['MassiliaJudo_Gender'] = 'Sélectionnez un genre';
        }
        if (empty($datas['MassiliaJudo_Firstname'])) {
            $errors['MassiliaJudo_Firstname'] = 'Le prénom est obligatoire';
        }
        if (empty($datas['MassiliaJudo_Lastname'])) {
            $errors['MassiliaJudo_Lastname'] = 'Le nom est obligatoire';
        }
        if (empty($datas['MassiliaJudo_Email'])) {
            $errors['MassiliaJudo_Email'] = "L'email est obligatoire";
        }
        if (intval($datas['MassiliaJudo_Dojo']) === 0) {
            $errors['MassiliaJudo_Dojo'] = "Sélectionnez un dojo";
        }
        if (empty($datas['MassiliaJudo_Birthday'])) {
            $errors['MassiliaJudo_Birthday'] = "La date de naissance est obligatoire";
        }
        if (empty($datas['MassiliaJudo_ContactId'])) {
            $errors['MassiliaJudo_ContactId'] = "Connectez vous";
        }

        return $errors;
    }

    /**
     * Information content
     */
    public function massilia_judokas_endpoint_content()
    {
        $judokas = MassiliaJudo_Judoka_DB::getJudokasByUserId();
        $nbrJudoka = count($judokas);
        if ($nbrJudoka == 0) {
            $string = <<<STRING
Vous n'avez inscrit aucun judoka chez Massilia Judo
STRING;
        } elseif ($nbrJudoka == 1) {
            $string = <<<STRING
Vous avez inscrit 1 judoka chez Massilia Judo
STRING;
        } else {
            $string = <<<STRING
Vous avez inscrit $nbrJudoka judokas ches Massilia Judo
STRING;
        }
        echo '<div class="woocommerce-message woocommerce-message--info woocommerce-Message woocommerce-Message--info woocommerce-info">
		<a class="woocommerce-Button button" href="'.$this->massilia_myaccount_page_url.'edit_judoka">
        Inscrire un Judoka		</a>
   '.$string.'</div>';

        $table = <<<STRING
        <div class="woocommerce-table-responsive">
<table class="table table-striped table-hover">
<tr>
    <th>Nom</th>
    <th>Prénom</th>    
    <th>Email</th>    
    <th>Dojo</th>
    <th>Année de pratique</th>        
    <th>Acions</th>        
</tr>
STRING;
        foreach ($judokas as $judoka) {

            $table .= <<<STRING
<tr>
<td>$judoka->lastname</td>
<td>$judoka->firstname</td>
<td>$judoka->email</td>
<td>$judoka->dojoId</td>
<td> </td>
<td><a href="$this->massilia_myaccount_page_url/edit_judoka/$judoka->id">Modifier</a> | <a href="$this->massilia_myaccount_page_url/del_judoka/$judoka->id">Supprimer</a></td>
</tr>
STRING;
        }
        $table .= <<<STRING
</table>
</div>
STRING;
        echo $table;

    }


    /**
     * Helper: is endpoint
     */
    public function massilia_is_endpoint($endpoint = false)
    {

        global $wp_query;

        if (!$wp_query) {
            return false;
        }

        return isset($wp_query->query[$endpoint]);

    }

    public function prefix_add_script_myaccount()
    {
        $css_directory = plugin_dir_url(__FILE__).'assets/css/';
        wp_enqueue_style('myaccount', $css_directory.'myaccount.css', false, 1.0, 'all');

    }

    public function check_data_edit_judoka_callback()
    {
        echo 'coucoucoucocu';

        die();
    }

    public static function wd_remove_accents($str, $charset = 'utf-8')
    {
        $str = htmlentities($str, ENT_NOQUOTES, $charset);

        $str = preg_replace('#&([A-za-z])(?:acute|cedil|caron|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $str);
        $str = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $str); // pour les ligatures e.g. '&oelig;'
        $str = preg_replace('#&[^;]+;#', '', $str); // supprime les autres caractères

        return $str;
    }

}