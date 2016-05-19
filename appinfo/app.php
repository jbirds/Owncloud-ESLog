<?php

/**
 * ownCloud - Dashboard
 *
 * @author Patrick Paysant <ppaysant@linagora.com>
 * @copyright 2014 CNRS DSI
 * @license This file is licensed under the Affero General Public License version 3 or later. See the COPYING file.
 */

namespace OCA\Eslog\Appinfo;

$app = new Application();
\OCA\Eslog\Lib\Hooks::connectHooks();
//$app->getContainer()->query('Hooks')->register();
$app->registerSettings();
$app->registerEslogConfig();
//`error_log(\OCP\User::getUser(),0);
//error_log(\OC::$server->getUserSession()->getUser()->getUID(), 0);

