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
        //Judokas
        add_action('woocommerce_account_judokas_endpoint', array($this, 'massilia_judokas_endpoint_content'), 10, 1);

        add_action('init', array($this, 'massilia_add_edit_judoka_endpoint'));
        add_action('woocommerce_account_edit_judoka_endpoint', array($this, 'massilia_edit_judoka_endpoint_content'));

        add_action('init', array($this, 'massilia_del_judoka_endpoint'));
        add_action('woocommerce_account_del_judoka_endpoint', array($this, 'massilia_del_judoka_endpoint_content'));

        //Formulaire front edit judoka
        add_action('template_redirect', array($this, 'traitement_formulaire_edit_judoka'), 10, 1);
        add_action('template_redirect', array($this, 'traitement_formulaire_del_judoka'), 10, 1);

        //Contacts
        add_action('woocommerce_account_contacts_endpoint', array($this, 'massilia_contacts_endpoint_content'), 10, 1);

        add_action('init', array($this, 'massilia_add_edit_contact_endpoint'));
        add_action('woocommerce_account_edit_contact_endpoint', array($this, 'massilia_edit_contact_endpoint_content'));

        add_action('init', array($this, 'massilia_del_contact_endpoint'));
        add_action('woocommerce_account_del_contact_endpoint', array($this, 'massilia_del_contact_endpoint_content'));

        //Formulaire front edit contact
        add_action('template_redirect', array($this, 'traitement_formulaire_edit_contact'), 10, 1);
        add_action('template_redirect', array($this, 'traitement_formulaire_del_contact'), 10, 1);

        //Css
        add_action('wp_enqueue_scripts', array($this, 'prefix_add_script_myaccount'));

        //Ajax
        add_action('wp_enqueue_scripts', array($this, 'MassiliaJudo_myaccount_scripts'));
        add_action('wp_ajax_edit_judoka_ajax_callback', array($this, 'check_data_edit_judoka_callback'));
        add_action('wp_ajax_edit_judoka_ajax_callback', array($this, 'check_data_edit_contact_callback'));
        $this->errors = [];
        $this->errors['MassiliaJudo_Gender_Required'] = 'Sélectionnez un genre';
        $this->errors['MassiliaJudo_Firstname_Required'] = 'Le prénom est obligatoire';
        $this->errors['MassiliaJudo_Lastname_Required'] = 'Le nom est obligatoire';
        $this->errors['MassiliaJudo_Email_Required'] = "L'email est obligatoire";
        $this->errors['MassiliaJudo_Email_Formatted'] = "L'email n'est pas correcte";
        $this->errors['MassiliaJudo_Dojo_Required'] = "Le dojo est obligatoire";
        $this->errors['MassiliaJudo_Birthday_Required'] = "La date de naissance est obligatoire";
        $this->errors['MassiliaJudo_ContactId_Required'] = "Connectez-vous";
        $this->errors['MassiliaJudo_PhoneNumber_Required'] = "Saisissez un numéro de téléphone";
        $this->errors['MassiliaJudo_PhoneNumber_Formatted'] = "Le numéro de téléphone n'est pas formater convenablement (ex: +33 6 01 02 03 04)";$this->errors['MassiliaJudo_PhoneNumber_Required'] = "Saisissez un numéro de téléphone";
        $this->errors['MassiliaJudo_Birthday_Required'] = "La date de naissance est obigatoire";
        $this->errors['MassiliaJudo_Birthday_Formatted'] = "La date de naissance n'est pas au bon format (ex: jj/mm/aaaa)";
        $this->errors['MassiliaJudo_Address_Required'] = "Une adresse postale est obligatoire";
        $this->errors['MassiliaJudo_City_Required'] = "Une ville postale est obligatoire";
        $this->errors['MassiliaJudo_Cp_Required'] = "Un code postal est obligatoire";
        $this->errors['MassiliaJudo_Status_Required'] = "Le status est obligatoire";



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
                $ret['contacts'] = __('Contacts', 'iconic');
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
        add_rewrite_endpoint('contacts', EP_PAGES);
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
     * Add endpoint
     */
    public function massilia_add_edit_contact_endpoint()
    {
        add_rewrite_endpoint('edit_contact', EP_PAGES);
    }

    /**
     * Add endpoint
     */
    public function massilia_del_contact_endpoint()
    {
        add_rewrite_endpoint('del_contact', EP_PAGES);
    }

    /**
     * Information content
     */
    public function massilia_edit_judoka_endpoint_content($judokaId = null)
    {
        $current_user = wp_get_current_user();
        $genderId = $dojoId = $firstName = $lastName = $email = $birthday = '';

        $MassiliaJudo_Gender_Error = $MassiliaJudo_Firstname_Error = $MassiliaJudo_Lastname_Error = '';
        $MassiliaJudo_Email_Error = $MassiliaJudo_Birthday_Error = $MassiliaJudo_Dojo_Error = '';

        if (isset($_POST['submit_edit_judoka']) && isset($_POST['massiliajudo_editjudoka'])) {
            if (wp_verify_nonce($_POST['massiliajudo_editjudoka'], 'MassiliaJudo')) {
                $errors = $this->chekDataJudoka($_POST);
                if (!empty($errors)) {
                    foreach ($errors as $error) {
                        if (strstr($error, "MassiliaJudo_Gender")) {
                            $MassiliaJudo_Gender_Error = $this->errors[$error];
                        }
                        if (strstr($error, "MassiliaJudo_Firstname")) {
                            $MassiliaJudo_Firstname_Error = $this->errors[$error];
                        }
                        if (strstr($error, "MassiliaJudo_Lastname")) {
                            $MassiliaJudo_Lastname_Error = $this->errors[$error];
                        }
                        if (strstr($error, "MassiliaJudo_Email")) {
                            $MassiliaJudo_Email_Error = $this->errors[$error];
                        }
                        if (strstr($error, "MassiliaJudo_Birthday")) {
                            $MassiliaJudo_Birthday_Error = $this->errors[$error];
                        }
                        if (strstr($error, "MassiliaJudo_Dojo")) {
                            $MassiliaJudo_Dojo_Error = $this->errors[$error];
                        }
                    }
                }
                $datas = $_POST;

                if (!empty($datas)) {
                    foreach ($datas as $data) {

                        if (!empty($datas["MassiliaJudo_Gender"])) {
                            $genderId = $datas["MassiliaJudo_Gender"];
                        }
                        if (!empty($datas["MassiliaJudo_Firstname"])) {
                            $firstName = $datas["MassiliaJudo_Firstname"];
                        }
                        if (!empty($datas["MassiliaJudo_Lastname"])) {
                            $lastName = $datas["MassiliaJudo_Lastname"];
                        }
                        if (!empty($datas["MassiliaJudo_Email"])) {
                            $email = $datas["MassiliaJudo_Email"];
                        }
                        if (!empty($datas["MassiliaJudo_Birthday"])) {
                            $birthday = $datas["MassiliaJudo_Birthday"];
                        }
                        if (!empty($datas["MassiliaJudo_Dojo"])) {
                            $dojoId = $datas["MassiliaJudo_Dojo"];
                        }
                    }
                }
            }
        }

        if (!empty($judokaId)) {
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
            'Genre',
            'woocommerce-Input select select2',
            true,
            $MassiliaJudo_Gender_Error
        );
        $dojoSelect = MassiliaJudo_Form_Builder::buildSelect(
            'MassiliaJudo_Dojo_DB',
            'MassiliaJudo_Dojo',
            $dojoId,
            'MassiliaJudo_Dojo',
            'Dojo',
            'woocommerce-Input select select2',
            true,
            $MassiliaJudo_Dojo_Error
        );
        $firstNameText = MassiliaJudo_Form_Builder::buildText(
            'MassiliaJudo_Firstname',
            $firstName,
            'MassiliaJudo_Firstname',
            'Prénom',
            'Rentrez le prénom du judoka',
            'woocommerce-Input woocommerce-Input--text input-text',
            true,
            $MassiliaJudo_Firstname_Error
        );
        $lastNameText = MassiliaJudo_Form_Builder::buildText(
            'MassiliaJudo_Lastname',
            $lastName,
            'MassiliaJudo_Lastname',
            'Nom',
            'Rentrez le nom du judoka',
            'woocommerce-Input woocommerce-Input--text input-text',
            true,
            $MassiliaJudo_Lastname_Error
        );
        $emailText = MassiliaJudo_Form_Builder::buildText(
            'MassiliaJudo_Email',
            $email,
            'MassiliaJudo_Email',
            'Email',
            'Rentrez l\'email du judoka',
            'woocommerce-Input woocommerce-Input--text input-text',
            true,
            $MassiliaJudo_Email_Error
        );
        $birthdayDate = MassiliaJudo_Form_Builder::buildDate(
            'MassiliaJudo_Birthday',
            $birthday,
            'MassiliaJudo_Birthday',
            'Date de naissance',
            'woocommerce-Input woocommerce-Input--text input-text',
            true,
            $MassiliaJudo_Birthday_Error
        );
        $submit = MassiliaJudo_Form_Builder::buildSubmit(
            'submit_edit_judoka',
            'MassiliaJudo_Submit',
            'Enregistrez',
            'woocommerce-Button button'
        );

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

        $html = '<form action="#" method="POST" class="woocommerce-EditAccountForm edit-account">%s
<!-- gender -->
<div class="woocommerce-form-row woocommerce-form-row--first form-row form-row-first">%s</div>
<!-- dojo -->
<div class="woocommerce-form-row woocommerce-form-row--last form-row form-row-last">%s</div>
<!-- firstname -->
<div class="woocommerce-form-row woocommerce-form-row--first form-row form-row-first">%s</div>
<!-- lastname -->
<div class="woocommerce-form-row woocommerce-form-row--last form-row form-row-last">%s</div>
<!-- email -->
<div class="woocommerce-form-row woocommerce-form-row--first form-row form-row-first">%s</div>
<!-- birthdat -->
<div class="woocommerce-form-row woocommerce-form-row--last form-row form-row-last">%s</div>
<!-- submit -->
<p class="pull-right">%s</p>
<!-- hidden contactId / judokaId -->
<span>%s%s</span>
</form>';

        echo sprintf(
            $html,
            wp_nonce_field('MassiliaJudo', 'massiliajudo_editjudoka', true, false),
            $genderSelect,
            $dojoSelect,
            $firstNameText,
            $lastNameText,
            $emailText,
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
                $errors = $this->chekDataJudoka($_POST);
                if (empty($errors)) {
                    $datas = MassiliaJudo_Judoka::formatDatasToDb($_POST);

                    if ($_POST['MassiliaJudo_JudokaId'] == '') {
                        $judoka_id = MassiliaJudo_Judoka_DB::saveJudoka($datas);
                        $this->sendEmail($datas);
                    } else {
                        MassiliaJudo_Judoka_DB::updateJudoka($datas);
                    }
                    wp_redirect(get_permalink(get_option('woocommerce_myaccount_page_id')).'/judokas/');
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
<div class="woocommerce-form-row woocommerce-form-row--first form-row form-row-first">%s</div>
<!-- dojo -->
<div class="woocommerce-form-row woocommerce-form-row--last form-row form-row-last">%s</div>
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
            $cancel = MassiliaJudo_Form_Builder::buildSubmit(
                'cancel_del_judoka',
                'MassiliaJudo_Cancel_Del',
                'Annulez',
                'woocommerce-Button button',
                'onclick="javascript:history.back();"'
            );
            $submit = MassiliaJudo_Form_Builder::buildSubmit(
                'submit_del_judoka',
                'MassiliaJudo_Submit_Del',
                'Supprimez',
                'woocommerce-Button button'
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
                if (MassiliaJudo_Judoka_DB::delJudoka($_POST['MassiliaJudo_JudokaId'])) {
                    wp_redirect(get_permalink(get_option('woocommerce_myaccount_page_id')).'/judokas/');
                    exit;
                }
            }
        }
    }

    /**
     * @param $datas
     * @return array
     */
    private function chekDataJudoka($datas)
    {
        $errors = [];

        if ($datas['MassiliaJudo_Gender'] == '') {
            $errors[] = 'MassiliaJudo_Gender_Required';
        }
        if (empty($datas['MassiliaJudo_Firstname'])) {
            $errors[] = 'MassiliaJudo_Firstname_Required';
        }
        if (empty($datas['MassiliaJudo_Lastname'])) {
            $errors[] = 'MassiliaJudo_Lastname_Required';
        }
        if (empty($datas['MassiliaJudo_Email'])) {
            $errors[] = 'MassiliaJudo_Email_Required';
        }elseif(!filter_var($datas['MassiliaJudo_Email'], FILTER_VALIDATE_EMAIL)){
            $errors[] = 'MassiliaJudo_Email_Formatted';
        }

        if (intval($datas['MassiliaJudo_Dojo']) === 0) {
            $errors[] = 'MassiliaJudo_Dojo_Required';
        }
        if (empty($datas['MassiliaJudo_Birthday'])) {
            $errors[] = 'MassiliaJudo_Birthday_Required';
        }elseif(!preg_match('/^(0[1-9]|1[0-9]|2[0-9]|3[0-1])\/(0[1-9]|1[0-2])\/[1-2][09][0-9]{2}$/', $datas['MassiliaJudo_Birthday'])){
            $errors[] = 'MassiliaJudo_Birthday_Formatted';
        }

        if (empty($datas['MassiliaJudo_ContactId'])) {
            $errors[] = 'MassiliaJudo_ContactId_Required';
        }

        return $errors;
    }

    /**
     * End Point of judoka
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
<td>$judoka->dojo</td>
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
     * End Point of contact
     */
    public function massilia_contacts_endpoint_content()
    {
        $contacts = MassiliaJudo_Contact_DB::getContactByUserId();
        $nbrContact = count($contacts);
        if ($nbrContact == 0) {
            $string = <<<STRING
Vous n'avez inscrit aucun contact à prévenir en plus de vous
STRING;
        } elseif ($nbrContact == 1) {
            $string = <<<STRING
Vous avez inscrit 1 contact à prévenir en plus de vous
STRING;
        } else {
            $string = <<<STRING
Vous avez inscrit $nbrContact contacts à prévenir en plus de vous
STRING;
        }
        echo '<div class="woocommerce-message woocommerce-message--info woocommerce-Message woocommerce-Message--info woocommerce-info">
		<a class="woocommerce-Button button" href="'.$this->massilia_myaccount_page_url.'edit_contact">
        Ajouter un Contact		</a>
   '.$string.'</div>';

        $table = '';
        if ($nbrContact > 0) {
            $table = <<<STRING
        <div class="woocommerce-table-responsive">
<table class="table table-striped table-hover">
<tr>
    <th>Nom</th>
    <th>Prénom</th>    
    <th>Email</th>    
    <th>Téléphone</th>
    <th>Actions</th>        
</tr>
STRING;
            foreach ($contacts as $contact) {

                $table .= <<<STRING
<tr>
<td>$contact->lastname</td>
<td>$contact->firstname</td>
<td>$contact->email</td>
<td>$contact->phoneNumber</td>
<td><a href="$this->massilia_myaccount_page_url/edit_contact/$contact->id">Modifier</a> | <a href="$this->massilia_myaccount_page_url/del_contact/$contact->id">Supprimer</a></td>
</tr>
STRING;
            }
            $table .= <<<STRING
</table>
</div>
STRING;
        }
        echo $table;

    }

    /**
     * Information content
     */
    public function massilia_edit_contact_endpoint_content($contactId = null)
    {
        $current_user = wp_get_current_user();
        $genderId = $firstName = $lastName = $email = $phoneNumber = $address = $city = $cp = $statusId = '';

        $MassiliaJudo_Gender_Error = $MassiliaJudo_Status_Error = $MassiliaJudo_Firstname_Error = '';
        $MassiliaJudo_Lastname_Error = $MassiliaJudo_Email_Error = $MassiliaJudo_PhoneNumber_Error = '';
        $MassiliaJudo_Address_Error = $MassiliaJudo_City_Error = $MassiliaJudo_Cp_Error = '';
        if (isset($_POST['submit_edit_contact']) && isset($_POST['massiliajudo_editcontact'])) {
            if (wp_verify_nonce($_POST['massiliajudo_editcontact'], 'MassiliaJudo')) {
                $errors = $this->chekDataContact($_POST);
                if (!empty($errors)) {
                    foreach ($errors as $error) {
                        if (strstr($error, "MassiliaJudo_Gender")) {
                            $MassiliaJudo_Gender_Error = $this->errors[$error];
                        }
                        if (strstr($error, "MassiliaJudo_Status")) {
                            $MassiliaJudo_Status_Error = $this->errors[$error];
                        }
                        if (strstr($error, "MassiliaJudo_Firstname")) {
                            $MassiliaJudo_Firstname_Error = $this->errors[$error];
                        }
                        if (strstr($error, "MassiliaJudo_Lastname")) {
                            $MassiliaJudo_Lastname_Error = $this->errors[$error];
                        }
                        if (strstr($error, "MassiliaJudo_Email")) {
                            $MassiliaJudo_Email_Error = $this->errors[$error];
                        }
                        if (strstr($error, "MassiliaJudo_PhoneNumber")) {
                            $MassiliaJudo_PhoneNumber_Error = $this->errors[$error];
                        }
                        if (strstr($error, "MassiliaJudo_Address")) {
                            $MassiliaJudo_Address_Error = $this->errors[$error];
                        }
                        if (strstr($error, "MassiliaJudo_City")) {
                            $MassiliaJudo_City_Error = $this->errors[$error];
                        }
                        if (strstr($error, "MassiliaJudo_Cp")) {
                            $MassiliaJudo_Cp_Error = $this->errors[$error];
                        }
                    }
                }
                $datas = $_POST;

                if (!empty($datas)) {
                    foreach ($datas as $data) {

                        if (!empty($datas["MassiliaJudo_Gender"])) {
                            $genderId = $datas["MassiliaJudo_Gender"];
                        }
                        if (!empty($datas["MassiliaJudo_Status"])) {
                            $statusId = $datas["MassiliaJudo_Status"];
                        }
                        if (!empty($datas["MassiliaJudo_Firstname"])) {
                            $firstName = $datas["MassiliaJudo_Firstname"];
                        }
                        if (!empty($datas["MassiliaJudo_Lastname"])) {
                            $lastName = $datas["MassiliaJudo_Lastname"];
                        }
                        if (!empty($datas["MassiliaJudo_Email"])) {
                            $email = $datas["MassiliaJudo_Email"];
                        }
                        if (!empty($datas["MassiliaJudo_PhoneNumber"])) {
                            $phoneNumber = $datas["MassiliaJudo_PhoneNumber"];
                        }
                        if (!empty($datas["MassiliaJudo_Address"])) {
                            $address = $datas["MassiliaJudo_Address"];
                        }
                        if (!empty($datas["MassiliaJudo_City"])) {
                            $city = $datas["MassiliaJudo_City"];
                        }
                        if (!empty($datas["MassiliaJudo_Cp"])) {
                            $cp = $datas["MassiliaJudo_Cp"];
                        }
                    }

                }
            }
        }


        if (!empty($contactId)) {
            if (MassiliaJudo_Contact_DB::isMyContact($current_user->ID, $contactId)) {
                $datas = MassiliaJudo_Contact::formatDataToForm(MassiliaJudo_Contact_DB::getContactsById($contactId));
                $genderId = intval($datas->genderId);
                $firstName = $datas->firstname;
                $lastName = $datas->lastname;
                $email = $datas->email;
                $phoneNumber = $datas->phoneNumber;
                $address = $datas->address;
                $city = $datas->city;
                $cp = $datas->cp;
                $status = $datas->statusId;

            }
        }

        $genderSelect = MassiliaJudo_Form_Builder::buildSelect(
            'MassiliaJudo_Gender_DB',
            'MassiliaJudo_Gender',
            $genderId,
            'MassiliaJudo_Gender',
            'Genre',
            true,
            $MassiliaJudo_Gender_Error
        );

        $statusSelect = MassiliaJudo_Form_Builder::buildSelect(
            'MassiliaJudo_Status_DB',
            'MassiliaJudo_Status',
            $status,
            'MassiliaJudo_Status',
            'Statut',
            true,
            $MassiliaJudo_Status_Error
        );

        $firstNameText = MassiliaJudo_Form_Builder::buildText(
            'MassiliaJudo_Firstname',
            $firstName,
            'MassiliaJudo_Firstname',
            'Prénom',
            'Rentrez le prénom du contact',
            'woocommerce-Input woocommerce-Input--text input-text',
            true,
            $MassiliaJudo_Firstname_Error
        );
        $lastNameText = MassiliaJudo_Form_Builder::buildText(
            'MassiliaJudo_Lastname',
            $lastName,
            'MassiliaJudo_Lastname',
            'Nom',
            'Rentrez le nom du contact',
            'woocommerce-Input woocommerce-Input--text input-text',
            true,
            $MassiliaJudo_Lastname_Error
        );
        $emailText = MassiliaJudo_Form_Builder::buildText(
            'MassiliaJudo_Email',
            $email,
            'MassiliaJudo_Email',
            'Email',
            'Rentrez l\'email du contact',
            'woocommerce-Input woocommerce-Input--text input-text',
            true,
            $MassiliaJudo_Email_Error
        );
        $phoneNumberText = MassiliaJudo_Form_Builder::buildPhoneNumber(
            'MassiliaJudo_PhoneNumber',
            $phoneNumber,
            'MassiliaJudo_PhoneNumber',
            'Numéro de téléphone portable',
            'woocommerce-Input woocommerce-Input--text input-text',
            true,
            $MassiliaJudo_PhoneNumber_Error
        );
        $addressText = MassiliaJudo_Form_Builder::buildText(
            'MassiliaJudo_Address',
            $address,
            'MassiliaJudo_Address',
            'Adresse',
            'Saisissez l\'adresse postale',
            'woocommerce-Input woocommerce-Input--text input-text',
            true,
            $MassiliaJudo_Address_Error
        );
        $cityText = MassiliaJudo_Form_Builder::buildText(
            'MassiliaJudo_City',
            $city,
            'MassiliaJudo_City',
            'Ville',
            'Saisissez la ville',
            'woocommerce-Input woocommerce-Input--text input-text',
            true,
            $MassiliaJudo_City_Error
        );
        $cpText = MassiliaJudo_Form_Builder::buildText(
            'MassiliaJudo_Cp',
            $cp,
            'MassiliaJudo_Cp',
            'Code postal',
            'Saisissez le code postal',
            'woocommerce-Input woocommerce-Input--text input-text',
            true,
            $MassiliaJudo_Cp_Error
        );

        $submit = MassiliaJudo_Form_Builder::buildSubmit(
            'submit_edit_contact',
            'MassiliaJudo_Submit',
            'Enregistrez',
            'woocommerce-Button button'
        );

        $userIdHidden = MassiliaJudo_Form_Builder::buildHidden(
            'MassiliaJudo_UserId',
            'MassiliaJudo_UserId',
            $current_user->ID
        );

        $contactIdHidden = MassiliaJudo_Form_Builder::buildHidden(
            'MassiliaJudo_ContactId',
            'MassiliaJudo_ContactId',
            $contactId
        );

        $html = '<form action="#" method="POST"  class="woocommerce-EditAccountForm edit-account">%s
<!-- gender -->
<div class="woocommerce-form-row woocommerce-form-row--first form-row form-row-first">%s</div>
<!-- status -->
<div class="woocommerce-form-row woocommerce-form-row--last form-row form-row-last">%s</div>
<!-- firstname -->
<div class="woocommerce-form-row woocommerce-form-row--first form-row form-row-first">%s</div>
<!-- lastname -->
<div class="woocommerce-form-row woocommerce-form-row--last form-row form-row-last">%s</div>
<!-- email -->
<div class="woocommerce-form-row woocommerce-form-row--first form-row form-row-first">%s</div>
<!-- phone number -->
<div class="woocommerce-form-row woocommerce-form-row--last form-row form-row-last has-error">%s</div>
<!-- address -->
<div class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">%s</div>
<!-- cp -->
<div class="woocommerce-form-row woocommerce-form-row--first form-row form-row-first">%s</div>
<!-- city -->
<div class="woocommerce-form-row woocommerce-form-row--last form-row form-row-last">%s</div>
<!-- submit -->
<p class="pull-right">%s</p>
<!-- hidden userId / judokaId -->
<span>%s%s</span>
</form>';

        echo sprintf(
            $html,
            wp_nonce_field('MassiliaJudo', 'massiliajudo_editcontact', true, false),
            $genderSelect,
            $statusSelect,
            $firstNameText,
            $lastNameText,
            $emailText,
            $phoneNumberText,
            $addressText,
            $cityText,
            $cpText,
            $submit,
            $userIdHidden,
            $contactIdHidden
        );


    }

    /**
     * Traitement des données du formulaire de contact
     */
    public function traitement_formulaire_edit_contact()
    {
        if (isset($_POST['submit_edit_contact']) && isset($_POST['massiliajudo_editcontact'])) {
            if (wp_verify_nonce($_POST['massiliajudo_editcontact'], 'MassiliaJudo')) {
                $errors = $this->chekDataContact($_POST);

                if (empty($errors)) {
                    $datas = MassiliaJudo_Contact::formatDatasToDb($_POST);

                    if ($_POST['MassiliaJudo_ContactId'] == '') {
                        $contact_id = MassiliaJudo_Contact_DB::saveContact($datas);
                    } else {
                        MassiliaJudo_Contact_DB::updateContact($datas);
                    }
                    wp_redirect(get_permalink(get_option('woocommerce_myaccount_page_id')).'/contacts/');
                    exit;
                }
            }
        }
    }

    /**
     * Information content
     */
    public function massilia_del_contact_endpoint_content($contactId = null)
    {
        $current_user = wp_get_current_user();

        if (!is_null($contactId) && MassiliaJudo_Contact_DB::isMyContact($current_user->ID, $contactId)) {
            $contactObj = MassiliaJudo_Contact_DB::getContactsById($contactId);

            $html = <<<HTML
<h2>Suppression d'un Contact</h2>
<p>Vous êtes sur le point du supprimer %s de votre compte.</p>
<p>Confirmez-vous cette suppression?</p>
<form action="#" method="POST" class="">%s%s
<div class="woocommerce-form-row woocommerce-form-row--first form-row form-row-first">%s</div>
<!-- dojo -->
<div class="woocommerce-form-row woocommerce-form-row--last form-row form-row-last">%s</div>
</form>
HTML;
            $contactString = 'le contact '.$contactObj->firstname.' '.$contactObj->lastname;

            $contactIdHidden = MassiliaJudo_Form_Builder::buildHidden(
                'MassiliaJudo_ContactId',
                'MassiliaJudo_ContactId',
                $contactId
            );
            $cancel = MassiliaJudo_Form_Builder::buildSubmit(
                'cancel_del_contact',
                'MassiliaJudo_Cancel_Del',
                'Annulez',
                'woocommerce-Button button',
                'onclick="javascript:history.back();"'
            );
            $submit = MassiliaJudo_Form_Builder::buildSubmit(
                'submit_del_contact',
                'MassiliaJudo_Submit_Del',
                'Supprimez',
                'woocommerce-Button button'
            );
            echo sprintf(
                $html,
                $contactString,
                wp_nonce_field('MassiliaJudo', 'massiliajudo_delcontact', true, false),
                $contactIdHidden,
                $cancel,
                $submit
            );
        }

    }

    public function traitement_formulaire_del_contact()
    {

        if (isset($_POST['submit_del_contact']) && isset($_POST['massiliajudo_delcontact'])) {

            if (wp_verify_nonce($_POST['massiliajudo_delcontact'], 'MassiliaJudo')) {
                if (MassiliaJudo_Contact_DB::delContact($_POST['MassiliaJudo_ContactId'])) {
                    wp_redirect(get_permalink(get_option('woocommerce_myaccount_page_id')).'/contacts/');
                    exit;
                }
            }
        }
    }

    /**
     * @param $datas
     * @return array
     */
    private function chekDataContact($datas)
    {
        $errors = [];
        if (intval($datas['MassiliaJudo_Gender']) === 0) {
            $errors[] = 'MassiliaJudo_Gender_Required';
        }
        if (empty($datas['MassiliaJudo_Firstname'])) {
            $errors[] = 'MassiliaJudo_Firstname_Required';
        }
        if (empty($datas['MassiliaJudo_Lastname'])) {
            $errors [] = 'MassiliaJudo_Lastname_Required';
        }
        if (empty($datas['MassiliaJudo_Email'])) {
            $errors[] = 'MassiliaJudo_Email_Required';
        }elseif(!filter_var($datas['MassiliaJudo_Email'], FILTER_VALIDATE_EMAIL)){
            $errors[] = 'MassiliaJudo_Email_Formatted';
        }
        if (intval($datas['MassiliaJudo_PhoneNumber']) === 0) {
            $errors[] = 'MassiliaJudo_PhoneNumber_Required';
        }elseif(!preg_match('/^(\+33)[[:blank:]](6|7)([[:blank:]]([0-9]{2})){4}$/', $datas['MassiliaJudo_PhoneNumber'])){
            $errors[] = 'MassiliaJudo_PhoneNumber_Formatted';
        }
        if (empty($datas['MassiliaJudo_Address'])) {
            $errors[] = 'MassiliaJudo_Address_Required';
        }
        if (empty($datas['MassiliaJudo_City'])) {
            $errors[] = 'MassiliaJudo_City_Required';
        }
        if (empty($datas['MassiliaJudo_Cp'])) {
            $errors[] = 'MassiliaJudo_Cp_Required';
        }
        if (empty($datas['MassiliaJudo_Status'])) {
            $errors[] = 'MassiliaJudo_Status_Required';
        }

        return array_map("utf8_encode", $errors );

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
        wp_enqueue_style('boostrap', "https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css", false, 1.0, 'all');
    }

    public function check_data_edit_judoka_callback()
    {
        echo 'coucoucoucocu';

        die();
    }

    public function check_data_edit_contact_callback()
    {
        echo 'coucoucoucocu';

        die();
    }

    /**
     * @param $str
     * @param string $charset
     * @return mixed|string
     */
    public static function wd_remove_accents($str, $charset = 'utf-8')
    {
        $str = htmlentities($str, ENT_NOQUOTES, $charset);

        $str = preg_replace('#&([A-za-z])(?:acute|cedil|caron|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $str);
        $str = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $str); // pour les ligatures e.g. '&oelig;'
        $str = preg_replace('#&[^;]+;#', '', $str); // supprime les autres caractères

        return $str;
    }

    /**
     * @param $datas
     */
    public function sendEmail($datas){
       echo '<pre>';var_dump($datas);echo '</pre>';exit;
        $recipients = ['arnaud@dollois', 'support@massilia-judo.fr'];
        $object = 'Nouvelle Inscription - 2017 / 2018';

        $sender = 'no-reply@massili-judo.fr';
        $header = array('From: '.$sender);

        $gender = MassiliaJudo_Gender_DB::getGenderById($datas['MassiliaJudo_Gender']);
        $genderLibelle = $gender['value'];
        $dojo = MassiliaJudo_Dojo_DB::getDojoById($datas['MassiliaJudo_Dojo']);
        $dojoLibelle = $dojo['value'];

        $lastName = $datas['MassiliaJudo_Lastname'];
        $firstName = $datas['MassiliaJudo_Firstname'];
        $email = $datas['MassiliaJudo_Email'];
        $birthdayDate = $datas['MassiliaJudo_Birthday'];

        $content = <<<HTML
Nouveau Judo

Voici les informations saisies:

    Civilité: $genderLibelle
    Prénom: $firstName
    Dollois: $lastName
    Date de naissence: $birthdayDate
    Dojo: $dojoLibelle

HTML;

        foreach ($recipients as $recipient) {
            $result = wp_mail($recipient, $object, $content, $header);
        }
    }

}