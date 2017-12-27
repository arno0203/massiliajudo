<?php
$nonce = $_REQUEST['_wpnonce'];
$ID = $_REQUEST['member'];
if (!wp_verify_nonce($nonce, 'editmember_'.$ID)) {

    echo __('You are not authorized to perform this operation.', $this->plugin_text_domain);

} else {
    ?>
    <div class="wrap">
        <h2><?php echo __('Fiche membre', 'massiliajudo'); ?></h2>
        <div id="massiliajudo_list_member">
            <div id="nds-post-body">
                <?php $member = MassiliaJudo_Member_DB::getFullMemberById($ID); ?>
                <div class="row">
                    <div class="col-md-4">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <?php echo __('Informations générales', 'massiliajudo'); ?>
                            </div>
                            <div class="panel-footer">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label><?php echo __('Identifiant', 'massiliajudo'); ?></label>
                                    </div>
                                    <div class="col-md-6">
                                        <?php echo $member['ID']; ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label><?php echo __('Login', 'massiliajudo'); ?></label>
                                    </div>
                                    <div class="col-md-6">
                                        <?php echo $member['login']; ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label><?php echo __('Email', 'massiliajudo'); ?></label>
                                    </div>
                                    <div class="col-md-6">
                                        <?php echo $member['email']; ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label><?php echo __('Prénom', 'massiliajudo'); ?></label>
                                    </div>
                                    <div class="col-md-6">
                                        <?php echo $member['first_name']; ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label><?php echo __('Nom', 'massiliajudo'); ?></label>
                                    </div>
                                    <div class="col-md-6">
                                        <?php echo $member['last_name']; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <?php echo __('Contacts', 'massiliajudo'); ?>
                            </div>
                            <div class="panel-footer">

                                <div class="row">
                                    <div class="col-md-2">
                                        <label><?php echo __('Status', 'massiliajudo'); ?></label>
                                    </div>
                                    <div class="col-md-2">
                                        <label><?php echo __('Prénom', 'massiliajudo'); ?></label>
                                    </div>
                                    <div class="col-md-2">
                                        <label><?php echo __('Nom', 'massiliajudo'); ?></label>
                                    </div>
                                    <div class="col-md-3">
                                        <label><?php echo __('Email', 'massiliajudo'); ?></label>
                                    </div>
                                    <div class="col-md-3">
                                        <label><?php echo __('Téléphone', 'massiliajudo'); ?></label>
                                    </div>
                                </div>
                                <?php foreach($member['contacts'] as $contact){ ?>
                                <div class="row">
                                    <div class="col-md-2">
                                        <?php echo $contact['status']; ?>
                                    </div>
                                    <div class="col-md-2">
                                        <?php echo $contact['firstname']; ?>
                                    </div>
                                    <div class="col-md-2">
                                        <?php echo $contact['lastname']; ?>
                                    </div>
                                    <div class="col-md-3">
                                        <a href="mailto:<?php echo $contact['email'];?>"><?php echo  $contact['email'];?></a>
                                    </div>
                                    <div class="col-md-3">
                                        <?php echo $contact['phoneNumber']; ?>
                                    </div>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </div>
    </div>
    <?php
}
echo '<pre>';
print_r($member);
echo '</pre>';
?>