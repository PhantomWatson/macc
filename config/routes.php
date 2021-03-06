<?php
/**
 * Routes configuration
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different URLs to chosen controllers and their actions (functions).
 *
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;
use Cake\Routing\Route\DashedRoute;

/**
 * The default class to use for all routes
 *
 * The following route classes are supplied with CakePHP and are appropriate
 * to set as the default:
 *
 * - Route
 * - InflectedRoute
 * - DashedRoute
 *
 * If no call is made to `Router::defaultRouteClass()`, the class used is
 * `Route` (`Cake\Routing\Route\Route`)
 *
 * Note that `Route` does not do any inflections on URLs which will result in
 * inconsistently cased URLs when used with `:plugin`, `:controller` and
 * `:action` markers.
 *
 * Cache: Routes are cached to improve performance, check the RoutingMiddleware
 * constructor in your `src/Application.php` file to change this behavior.
 *
 */
Router::defaultRouteClass(DashedRoute::class);

Router::scope('/', function (RouteBuilder $routes) {
    $routes->setExtensions(['json']);

    $routes->connect('/styling', ['controller' => 'Pages', 'action' => 'styling']);
    $routes->connect('/terms', ['controller' => 'Pages', 'action' => 'terms']);
    $routes->connect('/privacy', ['controller' => 'Pages', 'action' => 'privacy']);

    $routes->connect('/account', ['controller' => 'Users', 'action' => 'account']);
    $routes->connect('/change-password', ['controller' => 'Users', 'action' => 'changePassword']);
    $routes->connect('/forgot-password', ['controller' => 'Users', 'action' => 'forgotPassword']);
    $routes->connect('/login', ['controller' => 'Users', 'action' => 'login']);
    $routes->connect('/logout', ['controller' => 'Users', 'action' => 'logout']);
    $routes->connect('/members', ['controller' => 'Users', 'action' => 'members']);
    $routes->connect('/register', ['controller' => 'Users', 'action' => 'register']);
    $routes->connect('/reset-password/*', ['controller' => 'Users', 'action' => 'resetPassword']);
    $routes->connect('/u/:id/:slug', ['controller' => 'Users', 'action' => 'view'], [
        'pass' => ['id', 'slug'],
        'id' => '[0-9]+'
    ]);
    $routes->connect('/my-bio', ['controller' => 'Users', 'action' => 'myBio']);
    $routes->connect('/my-tags', ['controller' => 'Users', 'action' => 'myTags']);
    $routes->connect('/my-pictures', ['controller' => 'Users', 'action' => 'myPictures']);
    $routes->connect('/my-contact', ['controller' => 'Users', 'action' => 'myContact']);
    $routes->connect('/my-logo', ['controller' => 'Users', 'action' => 'myLogo']);

    $routes->connect('/tag/:slug', ['controller' => 'Tags', 'action' => 'view'], [
        'pass' => ['slug']
    ]);

    $routes->connect('/donate', ['controller' => 'Donations', 'action' => 'donate']);
    $routes->connect('/donation-complete', ['controller' => 'Donations', 'action' => 'donationComplete']);

    $routes->connect('/', ['controller' => 'Memberships', 'action' => 'levels']);
    $routes->connect('/my-membership', ['controller' => 'Memberships', 'action' => 'myMembership']);
    $routes->connect('/purchase-complete', ['controller' => 'Memberships', 'action' => 'purchaseComplete']);

    $routes->redirect('/admin/users/login', ['prefix' => false, 'controller' => 'Users', 'action' => 'login']);
    $routes->redirect('/admin/users/my-bio', ['prefix' => false, 'controller' => 'Users', 'action' => 'myBio']);

    $routes->fallbacks(DashedRoute::class);
});

Router::prefix('admin', function (RouteBuilder $routes) {
    $routes->setExtensions(['json']);
    $routes->fallbacks(DashedRoute::class);
});
