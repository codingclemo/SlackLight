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

            // for some reason the array is stored in an array...hence this outer loop
            foreach ($channels as $channel) {
                foreach ($channel as $realChannel) {
                    if ($realChannel === null) {
                    } else {
                        echo "<li>";
                        echo '<a <ref=\"#\" class=\"w3-bar-item w3-button\">';
                        echo $realChannel->getName();
                        echo "</a> </li>";
                    }
                }
            }
        ?>
    </ul>
</div>
