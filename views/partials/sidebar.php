<?php

use Data\DataManager;
use SlackLight\AuthenticationManager;
use SlackLight\Channel;

?>


<div class="col-sm-3 col-md-2 sidebar">

    <!--<div class="nav navbar-fixed-top">
        <ul>
            <li>
                <a href="#" class="w3-bar-item w3-button">Username here</a>
            </li>
        </ul>
    </div>-->
    <ul class="nav nav-sidebar">

        <?php

            $user = AuthenticationManager::getAuthenticatedUser();
            $channels[] = \Data\DataManager::getChannelsByUserId($user->getid());


            $channelName = isset($_REQUEST['channel']) ? $_REQUEST['channel'] : null;

            if ($channels !== null) {
                // for some reason the array is stored in an array...hence this outer loop
                foreach ($channels as $channel) {
                    $GLOBALS['userChannels'] = $channel;
                    foreach ($channel as $realChannel) {
                        if ($realChannel === null) {
                        } else {?>
                            <li <?php if($realChannel->getName() == $channelName) { ?> class="active" <?php } ?> >
                                <a href="<?php echo $_SERVER['PHP_SELF']; ?>?view=messenger&channel=<?php echo urlencode($realChannel->getName()) ?>" class=\"w3-bar-item w3-button\">
                                #<?php echo $realChannel->getName();
                                    if ($realChannel->isMarked()) {
                                        ?> <span class="glyphicon glyphicon-star"></span> <?php
                                    }
                                ?>
                                </a>
                            </li>
                        <?php }
                    }
                }
            }
        ?>
    </ul>
</div>
