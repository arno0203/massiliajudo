<?php

/**
 * The admin area of the plugin to load the User List Table
 */
?>

<div class="wrap">
    <h2><?php __( 'Liste des membres', 'massiliajudo'); ?></h2>
    <div id="massiliajudo_list_member">
        <div id="nds-post-body">
            <form id="massiliajudo_list_member_form" method="get">
                <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
                <?php
                $this->search_box( __( 'Chercher', 'massiliajudo'), 'nds-user-find');
                $this->display();
                ?>
            </form>
        </div>
    </div>
</div>