<php
$tmpl = new OC_Template('eslog', 'settings');
$tmpl->assign('eslog_host', \OCP\Config::getAppValue('eslog', 'eslog_host','localhost:9200'));
$tmpl->assign('eslog_auth', \OCP\Config::getAppValue('eslog', 'eslog_auth','none'));
$tmpl->assign('eslog_user', \OCP\Config::getAppValue('eslog', 'eslog_user',''));
$tmpl->assign('eslog_password', \OCP\Config::getAppValue('eslog', 'eslog_password',''));
$tmpl->assign('eslog_index', \OCP\Config::getAppValue('eslog', 'eslog_index','owncloud'));
$tmpl->assign('eslog_type', \OCP\Config::getAppValue('eslog', 'eslog_type','owncloud'));

return $tmpl->fetchPage();
