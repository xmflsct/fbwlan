<?php echo $head; ?>
    <div class="pure-g centered-row">
        <div class="pure-u-1 pure-u-md-1-2">
            <div class="l-box">
                <p> <?php
                    echo $msg;
                    ?>
                </p>
            </div>
        </div>
        <div class="pure-u-1 pure-u-md-1-2">
            <?php echo $access_code_widget ?>
        </div>
    </div>
    <div class="pure-g centered-row">
        <div class="pure-u-1 pure-u-md-1-2">
            <div class="l-box">
                <p>
                    <?php echo _('Or have you changed your mind?'); ?>
                </p>
            </div>
        </div>
        <div class="pure-u-1 pure-u-md-1-2">
            <div class="l-box">
                <p>
                    <a href="<?php echo $retry_url; ?>" class="pure-button button-secondary">
                        <i class="fa fa-play fa-lg"></i>
                        <?php echo _('Take me to check-in'); ?>
                    </a>
                </p>
            </div>
        </div>
    </div>


<?php echo $foot; ?>
