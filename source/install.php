<?php

$objPlugin = new QPlugin();
$objPlugin->strName = "QjsTree";
$objPlugin->strDescription = 'Tree view/editor control based on jQuery jsTree plugin.';
$objPlugin->strVersion = "0.1";
$objPlugin->strPlatformVersion = "2.2";
$objPlugin->strAuthorName = "Oleg Abrosimov";
$objPlugin->strAuthorEmail = "olegabr [at] yandex [dot] ru";

$components = array();

$components[] = new QPluginJsFile("jstree-v.pre1.0/jquery.jstree.js");
$components[] = new QPluginJsFile("jstree-v.pre1.0/_lib/jquery.cookie.js");
$components[] = new QPluginJsFile("jstree-v.pre1.0/_lib/jquery.hotkeys.js");

// There are not only css, but images too.
$components[] = new QPluginCssFile("jstree-v.pre1.0/themes");

$components[] = new QPluginControlFile("includes/QjsTree.class.php");
$components[] = new QPluginControlFile("includes/QjsTreeBase.class.php");
$components[] = new QPluginControlFile("includes/QjsTreeGen.class.php");

$components[] = new QPluginIncludedClass("QjsTree", "includes/QjsTree.class.php");
$components[] = new QPluginIncludedClass("QjsTreeBase", "includes/QjsTreeBase.class.php");
$components[] = new QPluginIncludedClass("QjsTreeGen", "includes/QjsTreeGen.class.php");

$components[] = new QPluginExample("example/jstree.php", "QjsTree: tree view/editor control based on jQuery jsTree plugin");
$components[] = new QPluginExampleFile("example/jstree.php");
$components[] = new QPluginExampleFile("example/jstree.tpl.php");

$components[] = new QPluginExample("example/jstree_edit.php", "QjsTree editable: tree view/editor control based on jQuery jsTree plugin");
$components[] = new QPluginExampleFile("example/jstree_edit.php");
$components[] = new QPluginExampleFile("example/jstree_edit.tpl.php");

$objPlugin->addComponents($components);
$objPlugin->install();

?>
