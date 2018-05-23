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
            var_dump($channels);
            echo " -----------------------------\n";

            


            /*
            foreach ($channels as $channel) {
                if ($channel->_visible == 1) {
                    echo "<li>page</li>";
                }
            }
            */
            //echo $channels[1]->getName();

            /*foreach ($channels as $channel) {

                var_dump($channel);
                echo " -----------------------------\n";
                //if ($channel->getChannelId() > 0) {
                    //echo "<li>";
                    //echo '<a <ref="#" class="w3-bar-item w3-button">';
                    //echo $channel->getName();
                    //echo '</a>';
                    //echo "</li>";
                //}
            }*/
        ?>
        <li>
            <a <ref="#" class="w3-bar-item w3-button">Channel 1</a>
        </li>

       <!-- <li>
            <a href="#" class="w3-bar-item w3-button">Channel 1</a>
        </li>
        <li>
            <a href="#" class="w3-bar-item w3-button">Channel 2</a>
        </li>
        <li>
            <a href="#" class="w3-bar-item w3-button">Channel 3</a>
        </li>-->
    </ul>
</div>
