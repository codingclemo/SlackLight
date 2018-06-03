


<?php require_once('partials/header.php');
use SlackLight\AuthenticationManager;
use SlackLight\Util;
use Data\DataManager;
?>


    <div class="container">
        <div class="row channelContent">
            <div class="channelList col-md-3">
                <?php require_once('partials/sidebar.php'); ?>
            </div>

            <?php
/*
            foreach ($userChannels as $realChannel) {
                if ($realChannel->getName() == $channelName) {
                    $channelDesc = $realChannel->getDescription();
                    $channelId = $realChannel->getId();
                }

            }
*/

            //TODO: Check if channel is starred for user

            if (!isset($_REQUEST['channel'])) {
            //load other site
            ?>

            <div class="channelMeta col-xs-12 col-md-7">
                    <h2 class="page-header"><?php echo "Welcome back!"; ?></h2>
                    <h5 class="sub-header"><?php echo "Select a channel and see what's up" ?></h5>
            </div>
            <?php
            } else {
                $realChannel = DataManager::getChannelByName($_REQUEST['channel']);
                $channelName = $realChannel->getName();
                $channelDesc = $realChannel->getDescription();
                $channelId = $realChannel->getId();

            ?>
<div class="col-xs-12 col-md-7">
    <div class="channelMeta">
        <h2 class="page-header"># <?php echo $channelName ?>
            <?php if ($realChannel->isMarked()): ?>
                <form class="form-horizontal" method="post" id="channelMark" action="<?php echo Util::action(SlackLight\Controller::ACTION_SWITCHMARKCHANNEL, array('view' => $view, "channel" => $channelName)); ?>">
                    <button type="submit" class="btn btn-primary-outline" form="channelMark">
                        <span class="glyphicon glyphicon-star"></span>
                    </button>
                </form>
            <?php else: ?>
                <form class="form-horizontal" method="post" id="channelNoMark" action="<?php echo Util::action(SlackLight\Controller::ACTION_SWITCHMARKCHANNEL, array('view' => $view, "channel" => $channelName)); ?>">
                    <button type="submit" class="btn btn-primary-outline" form="channelNoMark">
                        <span class="glyphicon glyphicon-star-empty"></span>
                    </button>
                </form>
            <?php endif; ?>
        </h2>

        <h5 class="sub-header"><?php echo $channelDesc; ?></h5>
    </div>

    <div class="messages" id="msgs">
        <?php //Add messages in here
        $messages[] = \Data\DataManager::getMessages($channelId);


        try {
            if ($messages[0] == null)
                throw new Exception("no messages");

        foreach ($messages[0] as $message) {
            $user = \Data\DataManager::getUserById($message->getAuthorId());
            $messageId = (string) $message->getId();
            ?>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><?php echo $user->getUserName() ?></h3>
                    <?php
                    echo $message->getTimestamp();
                    echo " msgId=".$messageId;

                    if ($message->isEdited())
                        echo " (edited)";
                    ?>

                    <?php if ($message->isMarked()): ?>

                        <?php
                        ?>
                        <form class="form-horizontal" method="post" id="messageMark" action="<?php echo Util::action(SlackLight\Controller::ACTION_SWITCHMARKMESSAGE, array('view' => $view, "channel" => $channelName, "message" => $messageId)); ?>">
                            <button type="submit" class="btn btn-primary-outline" form="messageMark">
                                <span class="glyphicon glyphicon-star"></span>
                            </button>
                        </form>
                    <?php else: ?>
                        <form class="form-horizontal" method="post" id="messageNoMark" action="<?php echo Util::action(SlackLight\Controller::ACTION_SWITCHMARKMESSAGE, array('view' => $view, "channel" => $channelName, "message" => $messageId)); ?>">
                            <button type="submit" class="btn btn-primary-outline" form="messageNoMark">
                                <span class="glyphicon glyphicon-star-empty"></span>
                            </button>
                        </form>
                    <?php endif;
                    ?>

                    <?php
                    if ($message->getAuthorId() == AuthenticationManager::getAuthenticatedUser()->getId()): ?>
                        <form class="form-horizontal" method="post" id="trashMsg" action="<?php echo Util::action(SlackLight\Controller::ACTION_DELETEMESSAGE, array('view' => $view, "channel" => $channelName, "message" => $messageId)); ?>">
                            <button type="submit" class="btn btn-primary-outline"  form="trashMsg">
                                <span class="glyphicon glyphicon glyphicon-trash"></span>
                            </button>
                        </form>
                    <?php endif;?>
                </div>
                <div class="panel-body">
                    <?php

                    if ($realChannel->getLastRead() < (int) $messageId) : ?>
                        <strong>
                    <?php
                    endif;
                    echo $message->getText();
                    echo "deleted: ".$message->isDeleted();

                    if ($realChannel->getLastRead() < (int) $messageId) : ?>
                        </strong>
                    <?php
                    endif; ?>
                </div>
            </div>

            <?php
        }
        } catch (\Exception $e) {
            // do magic
        }
        ?>

    </div>


    <?php
        Data\DataManager::updateLastRead();
    }

        if (isset($errors) && is_array($errors)): ?>
        <div class="errors alert alert-danger">
            <ul>
                <?php foreach ($errors as $errMsg): ?>
                    <li><?php echo(Util::escape($errMsg)); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!--/display error messages-->


    <div class="messageBar">
        <form class="form-horizontal" method="post" id="sendMsg" action="<?php echo Util::action(SlackLight\Controller::ACTION_SENDMSG, array('view' => $view, "channel" => $channelName)); ?>">
            <input class="form-control" id="textMsg" rows="3" name="<?php print SlackLight\Controller::SENDMSG; ?>" placeholder="Message @<?php echo $channelName ?>"></input>
        </form>
    </div>

</div>





        </div>
    </div>


<?php require('partials/footer.php');