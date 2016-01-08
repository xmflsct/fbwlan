<?php echo $head; ?>
    <div class="pure-g centered-row">
        <div class="pure-u-1">
            <div class="l-box">
                <h2>Hi! Welcome to Round&amp;Round Rotterdam!</h2>
            </div>
        </div>
    </div>
    <div class="pure-g centered-row">
       <div class="pure-u-1 pure-u-md-1-2">
        <div class="l-box">
        <p> <?php
            echo _('We have <b>KPN</b> class internet ready for you.');
            ?>
        </p>
        <p> <?php
            echo _('If you would like to take this opportunity to share your visit at our store, feel free to click on the check-in button.');
            ?>
        </p>
        </div>
        </div>
        <div class="pure-u-1 pure-u-md-1-2">
            <div class="l-box">
                <p>
                    <a class="pure-button pure-button-primary" href="<?php echo $fburl; ?>">
                        <i class="fa fa-facebook-official fa-lg"></i>
                        <?php echo _('Check-in on Facebook'); ?></a>
                </p>
                <div class="pure-u-1">
                    <img class="facebook-sample" src="./static/facebook_sample.jpg" />
                    <div class="sample-text">
                        <i>Sample check-in post</i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="pure-g centered-row">
        <div class="pure-u-1 pure-u-md-1-2">
            <div class="l-box">
                <p> <?php
                    echo _('No worries, if you are not a fan of Facebook.');
                    ?>
                </p>
                 <p> <?php
                    echo _('Simply ask <b>Chao</b> or <b>Bing</b> for an access code. This mechanism ensures a high-speed internet just for you.');
                    ?>
                </p>
            </div>
        </div>
        <div class="pure-u-1 pure-u-md-1-2">
            <?php echo $access_code_widget ?>
        </div>
    </div>


<?php echo $foot; ?>
