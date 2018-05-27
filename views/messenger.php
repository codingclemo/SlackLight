


<?php require_once('partials/header.php');
use SlackLight\AuthenticationManager;
?>


    <div class="container-fluid">
        <div class="row">

            <?php require_once('partials/sidebar.php'); ?>

            <?php

            foreach ($userChannels as $realChannel) {
                if ($realChannel->getName() == $channelName) {
                    $channelDesc = $realChannel->getDescription();
                    $channelId = $realChannel->getId();
                }

            }

            if ($channelName == NULL ) {
                //load other site
                ?>

                <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
                    <div id="topmessagebar">
                        <h2 class="page-header"><?php echo "Welcome back!"; ?></h2>

                        <h4 class="sub-header"><?php echo "Select a channel and see what's up" ?></h4>

                    </div>
                </div>
                <?php

            } else {

            $messages[] = \Data\DataManager::getMessages($channelId);
            ?>


            <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
                <div id="topmessagebar">
                    <h2 class="page-header"># <?php echo $channelName ?></h2>

                    <h4 class="sub-header"><?php echo $channelDesc ?></h4>

                </div>

                <br/>


                <?php //Add messages in here


                foreach ($messages[0] as $message) {

                    $user = \Data\DataManager::getUserById($message->getAuthorId());
                    ?>

                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title"><?php echo $user->getUserName() ?></h3>
                            <?php
                            echo $message->getTimestamp();
                            if ($message->isEdited())
                                echo " (edited)";
                            ?>
                        </div>
                        <div class="panel-body">
                            <?php
                            echo $message->getText();

                            echo hash('sha1', "clk" . '|' . "clk");
                            ?>
                        </div>
                    </div>

                    <?php
                }

                ?>

                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Jot your message down here">
                    <span class="input-group-btn">
                        <button class="btn btn-default" type="button">Send Message!</button>
                    </span>
                </div><!-- /input-group -->

            <?php
            }
            ?>
                </div>
            </div>


        </div>


<?php require('partials/footer.php');