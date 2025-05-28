<?php
/**
 * Routes configuration
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different urls to chosen controllers and their actions (functions).
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       cake
 * @subpackage    cake.app.config
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
/**
 * Here, we are connecting '/' (base path) to controller called 'Pages',
 * its action called 'display', and we pass a param to select the view file
 * to use (in this case, /app/views/pages/home.ctp)...
 */
	Router::connect('/', array('controller' => 'pages', 'action' => 'home'));
/**
 * ...and connect the rest of 'Pages' controller's urls.
 */
	Router::connect('/dashboard', array('controller' => 'reports', 'action' => 'dashboard'));
	Router::connect('/whatisjeeb', array('controller' => 'pages', 'action' => 'whatisjeeb'));
	Router::connect('/mobile', array('controller' => 'pages', 'action' => 'mobile'));
	Router::connect('/faq', array('controller' => 'pages', 'action' => 'faq'));
	Router::connect('/discount', array('controller' => 'pages', 'action' => 'discount'));
	Router::connect('/help', array('controller' => 'pages', 'action' => 'help'));
	Router::connect('/about', array('controller' => 'pages', 'action' => 'about'));
	Router::connect('/off', array('controller' => 'pages', 'action' => 'application'));
	Router::connect('/contact', array('controller' => 'pages', 'action' => 'contact'));
	Router::connect('/bugs', array('controller' => 'pages', 'action' => 'bugs'));
	Router::connect('/application', array('controller' => 'pages', 'action' => 'application'));
	Router::connect('/activation', array('controller' => 'pages', 'action' => 'activation'));
	Router::connect('/unsubscribe/*', array('controller' => 'users', 'action' => 'unsubscribe'));
	Router::connect('/pages/windows', array('controller' => 'application_orders', 'action' => 'index'));

/*
 * Asset Complress
 */
        Router::connect('/cache_css/*', array('plugin' => 'asset_compress', 'controller' => 'css_files', 'action' => 'get'));
        Router::connect('/cache_js/*', array('plugin' => 'asset_compress', 'controller' => 'js_files', 'action' => 'get'));

        // Make sure CakePHP parses CSV file requests correctly
        Router::parseExtensions('csv');
