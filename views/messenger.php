


<?php require_once('partials/header.php');
use SlackLight\AuthenticationManager;
use SlackLight\Util;
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
                            ?>
                        </div>
                    </div>

                    <?php
                }

                ?>

                <div class="input-group">
                    <form class="form-horizontal" method="post" id="sendMsg" action="<?php echo Util::action(SlackLight\Controller::ACTION_SENDMSG, array('view' => $view, "channel" => $channelName)); ?>">
                        <input type="text" class="form-control" name="<?php print SlackLight\Controller::SENDMSG; ?>" placeholder="Message @<?php echo $channelName ?>">
                    </form>
                        <span class="input-group-btn" form="sendMsg">
                            <button class="btn btn-default" type="submit" form="sendMsg"><span class="glyphicon glyphicon-send" ></span></button>
                        </span>
                </div><!-- /input-group -->
                <?php
            }
            ?>
                </div>
            </div>


        </div>


<?php require('partials/footer.php');