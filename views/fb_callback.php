
<?php echo $head; ?>
    <div class="centered-row">
        <div class="l-box">
            <h2><i class="fa fa-gratipay"></i><?php echo _('Facebook login successful!') ?></h2>
        </div>
    </div>
    <div class="pure-g centered-row">
        <div class="pure-u-1 pure-u-md-1-2">
            <div class="l-box">
                <p> <?php
                    echo _('For your check-in at our place, would you like to share some thoughts as well?');
                    ?>
                </p>
            </div>
        </div>
        <div class="pure-u-1 pure-u-md-1-2">
            <div class="l-box">
                <form class="pure-form pure-form-stacked" action="<?php echo $post_action; ?>">
                    <fieldset>
                        <label for="text_fb_message"><?php echo _('Message'); ?></label>
                        <textarea rows="2" name="message" id="text_fb_message" placeholder="<?php echo _('Optional');?>"></textarea>
                        <input type="hidden" name="nonce" id="fb_message_nonce" value="<?php echo $nonce; ?>"/>
                        <button type="submit" class="pure-button pure-button-primary">
                            <i class="fa fa-facebook-official fa-lg"></i>
                            <!-- echo _('Check in to') . ' ' .$place_name; -->
                            <?php echo _('Check in to Round&Round'); ?>
                        </button>
                    </fieldset>
                </form>
            </div>
        </div>
    </div>
    <?php echo $back_to_code_widget ?>

<?php echo $foot; ?>
