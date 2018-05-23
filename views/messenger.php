


<?php require_once('partials/header.php');
use SlackLight\AuthenticationManager;
?>


    <div class="container-fluid">
        <div class="row">

            <?php require_once('partials/sidebar.php'); ?>

            <?php /*
            $user = AuthenticationManager::getAuthenticatedUser();
            $channels[] = \Data\DataManager::getChannelsByUserId($user->getid());

            $channelName = isset($_REQUEST['channel']) ? $_REQUEST['channel'] : null;
*/
            //var_dump($userChannels);
            //$channelDesc = null;

            foreach ($userChannels as $realChannel) {
                if ($realChannel->getName() == $channelName) {
                    $channelDesc = $realChannel->getDescription();
                    $channelId = $realChannel->getId();
                }
            }
            ?>


            <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
                <div id="topmessagebar">
                    <h2 class="page-header">#<?php echo $channelName?></h2>

                    <h4 class="sub-header"><?php echo $channelDesc?></h4>

                </div>

                <br/>
                <?php //TODO: Add messages in here ?>

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Admin</h3>
                        19:16 on 2018-05-14
                    </div>
                    <div class="panel-body">
                        läuft bei euch das feedback. hab jz zum zweiten mal keins bekommen und vom tutor sowieso noch nie eins.
                    </div>
                </div>



            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Admin</h3>
                    19:16 on 2018-05-14
                </div>
                <div class="panel-body">
                    läuft bei euch das feedback. hab jz zum zweiten mal keins bekommen und vom tutor sowieso noch nie eins.
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Admin</h3>
                    19:16 on 2018-05-14
                </div>
                <div class="panel-body">
                    läuft bei euch das feedback. hab jz zum zweiten mal keins bekommen und vom tutor sowieso noch nie eins.
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Admin</h3>
                    19:16 on 2018-05-14
                </div>
                <div class="panel-body">
                    läuft bei euch das feedback. hab jz zum zweiten mal keins bekommen und vom tutor sowieso noch nie eins.
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Admin</h3>
                    19:16 on 2018-05-14
                </div>
                <div class="panel-body">
                    läuft bei euch das feedback. hab jz zum zweiten mal keins bekommen und vom tutor sowieso noch nie eins.
                </div>
            </div>


            <div class="input-group">
                <input type="text" class="form-control" placeholder="Jot your message down here">
                <span class="input-group-btn">
                    <button class="btn btn-default" type="button">Send Message!</button>
                </span>
            </div><!-- /input-group -->
            </div>
        </div>


    </div>


<?php require('partials/footer.php');