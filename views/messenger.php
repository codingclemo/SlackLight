


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
                <form class="form-vertical" method="post" id="channelMark" action="<?php echo Util::action(SlackLight\Controller::ACTION_SWITCHMARKCHANNEL, array('view' => $view, "channel" => $channelName)); ?>">
                    <button type="submit" class="btn btn-primary-outline">
                        <span class="glyphicon glyphicon-star"></span>
                    </button>
                </form>
            <?php else: ?>
                <form class="form-vertical" method="post" id="channelNoMark" action="<?php echo Util::action(SlackLight\Controller::ACTION_SWITCHMARKCHANNEL, array('view' => $view, "channel" => $channelName)); ?>">
                    <button type="submit" class="btn btn-primary-outline">
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
                    ?>

                    <?php if ($message->isMarked()): ?>

                        <?php
                        ?>
                        <form class="form-vertical" method="post" id="messageMark" action="<?php echo Util::action(SlackLight\Controller::ACTION_SWITCHMARKMESSAGE, array('view' => $view, "channel" => $channelName, "message" => $messageId)); ?>">
                            <button type="submit" class="btn btn-primary-outline">
                                <span class="glyphicon glyphicon-star"></span>
                            </button>
                        </form>
                    <?php else: ?>
                        <form class="form-vertical" method="post" id="messageNoMark" action="<?php echo Util::action(SlackLight\Controller::ACTION_SWITCHMARKMESSAGE, array('view' => $view, "channel" => $channelName, "message" => $messageId)); ?>">
                            <button type="submit" class="btn btn-primary-outline" >
                                <span class="glyphicon glyphicon-star-empty"></span>
                            </button>
                        </form>
                    <?php endif;
                    ?>

                    <?php
                    if ($message->getAuthorId() == AuthenticationManager::getAuthenticatedUser()->getId()): ?>
                        <form class="form-vertical" method="post">
                            <button type="button" class="btn btn-primary-outline editMsg" id="editMsg_<?php echo $messageId; ?>">
                                <span class="glyphicon glyphicon-pencil"></span>
                            </button>
                        </form>
                        <form class="form-vertical" method="post" id="trashMsg" action="<?php echo Util::action(SlackLight\Controller::ACTION_DELETEMESSAGE, array('view' => $view, "channel" => $channelName, "message" => $messageId)); ?>">
                            <button type="submit" class="btn btn-primary-outline">
                                <span class="glyphicon glyphicon-trash"></span>
                            </button>
                        </form>
                    <?php endif;?>
                </div>
                <div class="panel-body" id="msg-body-<?php echo $messageId; ?>">
                    <?php
                    if ($realChannel->getLastRead() < (int) $messageId) : ?>
                        <strong>
                    <?php
                    endif;
                    echo $message->getText();
                    if ($message->isEdited())
                        echo " (edited)";

                    if ($realChannel->getLastRead() < (int) $messageId) : ?>
                        </strong>
                    <?php
                    endif;

                    // if edit button is pressed -> add form and textfield to div (below message)
                    // add send button and abort button


                    ?>
                    <div class="editMsgFormDiv" id="form_editMsg_<?php echo $messageId; ?>">
                        <form class="form-horizontal" onsubmit="" method="post" action="<?php echo Util::action(SlackLight\Controller::ACTION_EDITMESSAGE, array('view' => $view, 'channel' => $channelName, 'message' => $messageId)); ?>">
                            <input class="form-control" name="<?php print SlackLight\Controller::EDITMESSAGE; ?>" value="<?php echo $message->getText(); ?>">
                            <button type="submit" class="btn btn-primary-outline">
                                <span class="glyphicon glyphicon-ok"></span>
                            </button>
                            <button type="button" class="btn btn-primary-outline removeEditMsg">
                                <span class="glyphicon glyphicon-remove"></span>
                            </button>
                        </form>
                    </div>
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
            <input class="form-control" id="textMsg" name="<?php print SlackLight\Controller::SENDMSG; ?>" placeholder="Message @<?php echo $channelName ?>"></input>
        </form>
    </div>

</div>





        </div>
    </div>


<?php require('partials/footer.php');