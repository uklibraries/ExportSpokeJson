<?php
/**
 * Export SPOKEdb JSON
 *
 * @copyright 2015 Eric C. Weig
 * @license http://opensource.org/licenses/MIT MIT
 * @package Omeka\Plugins\ExportSpokeJson
 */

require_once "jobs" . DIRECTORY_SEPARATOR . "ExportSpokeJson_Job_ExportItem.php";

class ExportSpokeJsonPlugin extends Omeka_Plugin_AbstractPlugin
{
    protected $_hooks = array(
        'admin_items_show_sidebar',
        'define_routes',
    );

    public function hookAdminItemsShowSidebar($args)
    {
        $item = $args['item'];
        $output = new Output_SpokeJson($item);
        if ($output->rights() === 'restricted') {
            echo get_view()->partial(
                'export-denied-panel.php',
                array()
            );
        }
        else {
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
