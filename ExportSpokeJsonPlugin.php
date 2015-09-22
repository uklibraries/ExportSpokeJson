<?php
/**
 * Export SPOKEdb JSON
 *
 * @copyright 2015 Eric C. Weig
 * @license http://opensource.org/licenses/MIT MIT
 * @package Omeka\Plugins\ExportSpokeJson
 */

define('DS', DIRECTORY_SEPARATOR);
require_once "jobs" . DS . "ExportSpokeJson_Job_ExportItem.php";
require_once "models" . DS . "Output" . DIRECTORY_SEPARATOR . "SpokeJson.php";
$pluginDir = dirname(dirname(__FILE__));
require_once $pluginDir . DS . "RecursiveSuppression" . DS . "models" . DS . "SuppressionChecker.php";

class ExportSpokeJsonPlugin extends Omeka_Plugin_AbstractPlugin
{
    protected $_hooks = array(
        'admin_items_show_sidebar',
        'define_routes',
    );

    public function hookAdminItemsShowSidebar($args)
    {
        $item = get_record_by_id('Item', $args['item']['id']);
        $checker = new SuppressionChecker($item);
        if ($checker->exportable()) {
            echo get_view()->partial(
                'export-panel.php',
                array()
            );
        }
    }

    public function hookDefineRoutes($args)
    {
        $args['router']->addRoute(
            'export_spoke_json_route',
            new Zend_Controller_Router_Route(
                'items/export/:id',
                array(
                    'module' => 'export-spoke-json',
                    'controller' => 'items',
                    'action' => 'export'
                ),
                array(
                    'id' => '\d+'
                )
            )
        );
    }
}
