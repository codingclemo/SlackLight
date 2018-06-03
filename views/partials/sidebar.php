<?php

use Data\DataManager;
use SlackLight\AuthenticationManager;
use SlackLight\Channel;

?>


<div class="col-sm-3 col-md-2 sidebar">

    <ul class="nav nav-sidebar">

        <?php

            $user = AuthenticationManager::getAuthenticatedUser();
            $channels[] = \Data\DataManager::getChannelsByUserId($user->getid());

            $channelName = isset($_REQUEST['channel']) ? $_REQUEST['channel'] : null;

            if ($channels !== null) {
                // for some reason the array is stored in an array...hence this outer loop
                foreach ($channels as $channel) {
                    $GLOBALS['userChannels'] = $channel;

                    $showTitle = true;
                    foreach ($channel as $realChannel) {
                        if ($realChannel !== null && $realChannel->isMarked()) {
                            // get starred channels
                            if ($showTitle) {
                                $showTitle = false;
                                ?>
                                <li>
                                    <h5>Starred</h5>
                                </li>
                            <?php }

                            ?>
                            <li <?php if($realChannel->getName() == $channelName) { ?> class="active" <?php }
                                $hasStarred = true;
                            ?> >
                                <a href="<?php echo $_SERVER['PHP_SELF']; ?>?view=messenger&channel=<?php echo urlencode($realChannel->getName()) ?>" class=\"w3-bar-item w3-button\">
                                #<?php echo $realChannel->getName();?>
                                </a>

                            </li>
                        <?php }
                    }

                    // get regular channels
                    $showTitle = true;
                    foreach ($channel as $realChannel) {
                        if ($realChannel !== null && !$realChannel->isMarked()) {
                            if ($showTitle) {
                                $showTitle = false;
                                ?>
                                <li>
                                    <h5>Channels</h5>
                                </li>
                            <?php } ?>
                            <li <?php if($realChannel->getName() == $channelName) { ?> class="active" <?php } ?> >
                                <a href="<?php echo $_SERVER['PHP_SELF']; ?>?view=messenger&channel=<?php echo urlencode($realChannel->getName()) ?>" class=\"w3-bar-item w3-button\">
                                #<?php echo $realChannel->getName();?>
                                </a>
                            </li>
                        <?php }
                    }
                }
            }
        ?>
    </ul>
</div>
