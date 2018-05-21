<?php

use SlackLight\AuthenticationManager;
use SlackLight\Util;
use Data\DataManager;

if (AuthenticationManager::isAuthenticated()) {
    Util::redirect("index.php");
}

$userName = isset($_REQUEST['userName']) ? $_REQUEST['userName'] : null;

?>

<?php require_once('views/partials/header.php'); ?>

    <div class="page-header">
        <h2>Register</h2>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            Please fill out the form below:
        </div>
        <div class="panel-body">

            <form class="form-horizontal" method="post" action="<?php echo Util::action(SlackLight\Controller::ACTION_REGISTER, array('view' => $view)); ?>">
                <div class="form-group">
                    <label for="inputName" class="col-sm-2 control-label">User name:</label>
                    <div class="col-sm-6">
                        <input type="text" class="form-control" id="inputName" name="<?php print SlackLight\Controller::USER_NAME; ?>" placeholder="Set your username" value="<?php echo htmlentities($userName); ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputPassword" class="col-sm-2 control-label">Password</label>
                    <div class="col-sm-6">
                        <input type="password" class="form-control" id="inputPassword" name="<?php print SlackLight\Controller::USER_PASSWORD; ?>" placeholder="Set your personal password">
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-6">
                        <button type="submit" class="btn btn-default">Register</button>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-6">
                        <a href="index.php?view=login">Already registered? Click here to login</a>
                    </div>
                </div>
            </form>

        </div>
    </div>

<?php
require_once('views/partials/footer.php');