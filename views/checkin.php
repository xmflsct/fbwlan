
<?php echo $head; ?>
    <div class="centered-row">
        <div class="l-box">
            <h2><i class="fa fa-gratipay"></i><?php echo _('Facebook check-in successful!') ?></h2>
        </div>
    </div>
    <div class="pure-g centered-row">
        <div class="pure-u-1 pure-u-md-1-2">
            <div class="l-box">
                <p> <?php
                    echo _('Thank you for sharing your visit to our store!');
                    echo ' ';
                    echo _('Hit the button below to connect to our WiFi.');
                    ?>
                </p>
                <p> <?php
                    $class = '';
                    $url = $loginurl;
                    // If the App is in review mode, then disable the login button. Or there will be an error message //
                    if (FB_REVIEW) {
                        echo '<div class="error-message">';
                        echo _('Note: You are outside our Network. I have disabled the button for you.');
                        echo '</div>';
                        $class = ' pure-button-disabled';
                        $url = '#';
                    }
                    ?>
                </p>
                <p>
                    <a href="<?php echo $url; ?>" class="pure-button pure-button-primary <?php echo $class; ?>">
                    <i class="fa fa-lg fa-wifi"></i>
                    <?php
                    $btntext = _('Surf Now');
                    echo $btntext;
                    ?>
                    </a>
                </p>
            </div>
        </div>
        <div class="pure-u-1 pure-u-md-1-2">
            <div class="l-box">
                <p> <?php
                    echo _('You can also check out your Facebook status or add a photo to it.');
                    ?>
                </p>
                <p> <?php
                    sprintf(_('But make sure to return to this page and hit the <strong>%s</strong> button.'), $btntext);;
                    ?>
                </p> 
                <p>
                    <a href="<?php echo $posturl; ?>" target="_blank" class="pure-button button-secondary">
                    <i class="fa fa-lg fa-facebook-official"></i>
                    <?php
                    echo _('Open Facebook in new window.');
                    ?>
                    </a>
                </p>
            </div>
        </div>
    </div>

<?php echo $foot; ?>
