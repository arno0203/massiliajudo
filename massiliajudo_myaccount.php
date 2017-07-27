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
        add_action('woocommerce_account_judokas_endpoint', array($this, 'massilia_judokas_endpoint_content'));

        add_action('init', array($this, 'massilia_add_edit_judoka_endpoint'));
        add_action('woocommerce_account_edit_judoka_endpoint', array($this, 'massilia_edit_judoka_endpoint_content'));


        //Formulaire front edit judoka
        add_action('template_redirect', array($this, 'traitement_formulaire_edit_judoka'), 10, 1);

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

        $this->massilia_myaccount_page_id = get_option( 'woocommerce_myaccount_page_id' );
        if ( $this->massilia_myaccount_page_id ) {
            $this->massilia_myaccount_page_url = get_permalink( $this->massilia_myaccount_page_id );
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
     * Information content
     */
    public function massilia_edit_judoka_endpoint_content()
    {
        $current_user = wp_get_current_user();

        $genderSelect  = MassiliaJudo_Form_Builder::buildSelect('MassiliaJudo_Gender_DB','MassiliaJudo_Gender','', 'MassiliaJudo_Gender', 'Genre');
        $dojoSelect  = MassiliaJudo_Form_Builder::buildSelect('MassiliaJudo_Dojo_DB', 'MassiliaJudo_Dojo', '', 'MassiliaJudo_Dojo', 'Dojo');
        $firstNameText = MassiliaJudo_Form_Builder::buildText('MassiliaJudo_Firstname', '', 'MassiliaJudo_Firstname', 'Prénom', 'Rentrez le prénom du judoka');
        $lastNameText = MassiliaJudo_Form_Builder::buildText('MassiliaJudo_Lastname', '', 'MassiliaJudo_Lastname', 'Nom', 'Rentrez le nom du judoka');
        $emailText = MassiliaJudo_Form_Builder::buildText('MassiliaJudo_Email', '', 'MassiliaJudo_Email', 'Email', 'Rentrez l\'email du judoka');
        $birthdayDate = MassiliaJudo_Form_Builder::buildDate('MassiliaJudo_Birthday', '', 'MassiliaJudo_Birthday', 'Date de naissance');
        $submit = MassiliaJudo_Form_Builder::buildSubmit('submit_edit_judoka', 'MassiliaJudo_Submit', 'Validez');
        $contactHidden = MassiliaJudo_Form_Builder::buildHidden('MassiliaJudo_ContactId', 'MassiliaJudo_ContactId', $current_user->ID);

        $html ='<form action="#" method="POST" class="">%s
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
	    %s
	</span>
</form>';

       echo sprintf($html,
           wp_nonce_field('MassiliaJudo', 'massiliajudo_editjudoka',true, false),
            $genderSelect,
            $firstNameText,
		    $lastNameText,
            $emailText,
            $dojoSelect,
            $birthdayDate,
            $submit,
            $contactHidden);

    }

    public function traitement_formulaire_edit_judoka(){
        var_dump($_REQUEST);
//        if (isset($_POST['submit_edit_judoka']) && isset($_POST['massiliajudo_editjudoka'])) {
//
//            if (wp_verify_nonce($_POST['massiliajudo_editjudoka-verif'], 'faire-MassiliaJudo')) {
//                var_dump($_POST);exit;
//            }
//        }
    }

    /**
     * Information content
     */
    public function massilia_judokas_endpoint_content()
    {
        $judokaDb = new MassiliaJudo_Judoka_DB();
        $judokas = $judokaDb->getJudokasById();
        $nbrJudoka = count($judokas);
        if ($nbrJudoka == 0) {
           $string = <<<STRING
Vous n'avez inscrit aucun judoka chez Massilia Judo
STRING;
        }elseif($nbrJudoka == 1){
            $string = <<<STRING
Vous avez inscrit 1 judoka chez Massilia Judo
STRING;
        }else{
            $string = <<<STRING
Vous avez inscrit $nbrJudoka judokas ches Massilia Judo
STRING;
        }
        echo '<div class="woocommerce-message woocommerce-message--info woocommerce-Message woocommerce-Message--info woocommerce-info">
		<a class="woocommerce-Button button" href="'.$this->massilia_myaccount_page_url.'edit_judoka">
        Inscrire un Judoka		</a>
   '.$string.'</div>';

        $table =<<<STRING
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
        foreach ($judokas as $judoka){
            $table .=<<<STRING
<tr>
<td>$judoka->lastname</td>
<td>$judoka->firstname</td>
<td>$judoka->email</td>
<td>$judoka->dojoId</td>
<td> </td>
<td><a href="">Modifier</a> | <a href="">Supprimer</a></td>
</tr>
STRING;
      $table .=<<<STRING
</table>
</div>
STRING;
        echo $table;

        }
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

}