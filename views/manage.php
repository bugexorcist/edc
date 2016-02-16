<div class="wrap">
    <h1>Easy Domain Change</h1>

    <div class="fileedit-sub">
        <div class="alignleft">
            <h2>Domain Settings</h2>
        </div>
        <br class="clear" />
        <form action="" method="post">
            <div class="alignleft">
                <strong><label for="old_domain"><?php _e('Old Domain:'); ?> </label></strong>
                <input id="old_domain" size="50" name="old_domain"/>
                <input id="set_current_as_old" type="checkbox"/>
                <label for="set_current_as_old"><?php _e('Current'); ?></label>
            </div>
            <br class="clear" />
            <div class="alignleft">
                <strong><label for="new_domain"><?php _e('New Domain:'); ?> </label></strong>
                <input id="new_domain" size="50" name="new_domain"/>
                <input id="set_current_as_new" type="checkbox"/>
                <label for="set_current_as_new"><?php _e('Current'); ?></label>
            </div>
            <br class="clear" />
            <?php submit_button(__('Update'), 'button', 'Update', false); ?>
        </form>
        <br class="clear" />
    </div>
</div>