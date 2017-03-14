<?php
/**
 * Export SPOKEdb JSON
 *
 * @copyright 2015 Eric C. Weig
 * @license http://opensource.org/licenses/MIT MIT
 * @package Omeka\Plugins\ExportSpokeJson
 */

require_once "jobs" . DIRECTORY_SEPARATOR . "ExportSpokeJson_Job_ExportItem.php";
require_once "jobs" . DIRECTORY_SEPARATOR . "ExportSpokeJson_Job_UnindexInterviews.php";
require_once "jobs" . DIRECTORY_SEPARATOR . "ExportSpokeJson_Job_UnindexItem.php";
require_once "models" . DIRECTORY_SEPARATOR . "Output" . DIRECTORY_SEPARATOR . "SpokeJson.php";
$pluginDir = dirname(dirname(__FILE__));
require_once $pluginDir . DIRECTORY_SEPARATOR . "RecursiveSuppression" . DIRECTORY_SEPARATOR . "models" . DIRECTORY_SEPARATOR . "SuppressionChecker.php";

class ExportSpokeJsonPlugin extends Omeka_Plugin_AbstractPlugin
{
    protected $_hooks = array(
        'admin_head',
        'admin_items_show_sidebar',
        'define_routes',
        'before_save_item',
    );

    public function hookBeforeSaveItem($args)
    {
        if (empty($args['post'])) {
            return;
        }

        $item = $args['record'];

        if (empty($item->id)) {
            return;
        }

        $suppressionField = false;
        switch($item->getItemType()->name) {
        case 'collections':
            $suppressionField = 'Collection Suppressed';
            break;
        case 'series':
            $suppressionField = 'Series Suppressed';
            break;
        case 'interviews':
            $suppressionField = 'Interview Suppressed';
            break;
        }

        if (!$suppressionField) {
            return;
        }

        $elementSet = get_record('ElementSet', array('name' => 'Item Type Metadata'));
        $element = false;
        foreach ($elementSet->getElements() as $elt) {
            if ($elt->name === $suppressionField) {
                $element = $elt;
                break;
            }
        }

        if (!$element) {
            return;
        }

        $id = $element->id;

        $itemSuppressionMetadata = json_decode(
            metadata($item, array('Item Type Metadata', $suppressionField), array('no_escape' => true, 'no_filter' => true)),
            true
        );

        $postSuppressionMetadata = json_decode(
            $args['post']['Elements'][$id][0]['text'],
            true
        );

        if (array_key_exists('recursive', $itemSuppressionMetadata)) {
            $recursiveInItem = $itemSuppressionMetadata['recursive'];
            $recursiveInPost = $postSuppressionMetadata['recursive'];

            # Should interviews in this item be suppressed?
            if (!$recursiveInItem && $recursiveInPost) {
                Zend_Registry::get('bootstrap')->getResource('jobs')->sendLongRunning(
                    'ExportSpokeJson_Job_UnindexInterviews', array(
                        'itemId' => $item['id'],
                    )
                );
            }
        }

        $suppressedInItem = $itemSuppressionMetadata['description'];
        $suppressedInPost = $postSuppressionMetadata['description'];

        # Has this specific item just been suppressed?
        if (!$suppressedInItem && $suppressedInPost) {
            Zend_Registry::get('bootstrap')->getResource('jobs')->sendLongRunning(
                'ExportSpokeJson_Job_UnindexItem', array(
                    'itemId' => $item['id'],
                    'recursive' => "1",
                )
            );
        }
    }

    public function hookAdminHead($args)
    {
        $request = Zend_Controller_Front::getInstance()->getRequest();

        $module = $request->getModuleName();
        if (is_null($module)) {
            $module = 'default';
        }
        $controller = $request->getControllerName();
        $action = $request->getActionName();

        if ($module === 'default'
            && $controller === 'items'
            && in_array($action, array('show')))
        {
            queue_js_file('export_json');
            queue_js_file('delete_json');
        }
    }

    public function hookAdminItemsShowSidebar($args)
    {
        $item = get_record_by_id('Item', $args['item']['id']);
        $checker = new SuppressionChecker($item);
        if ($checker->exportable()) {
            $itemType = $item->getItemType()->name;
            if ($itemType === 'interviews') {
                $visibleCheckbox = false;
            }
            else {
                $visibleCheckbox = true;
            }
            $exportable = true;
            $subitemCount = 0;
            # TODO: make this configurable
            if (false) {
                $subitemCount = $this->getSubitemCount($item);
                $exportable = $subitemCount <= 200;
            }
            echo get_view()->partial(
                'export-panel.php',
                array(
                    'visibleCheckbox' => $visibleCheckbox,
                    'subitemCount' => $subitemCount,
                    'exportable' => $exportable,
                )
            );
        }
    }

    public function hookDefineRoutes($args)
    {
        $args['router']->addRoute(
            'export_spoke_json_route',
            new Zend_Controller_Router_Route(
                'items/export/:id/:recursive',
                array(
                    'module' => 'export-spoke-json',
                    'controller' => 'items',
                    'action' => 'export'
                ),
                array(
                    'id' => '\d+',
                    'recursive' => '\d+',
                )
            )
        );
        $args['router']->addRoute(
            'unindex_spoke_json_route',
            new Zend_Controller_Router_Route(
                'items/unindex/:id/:recursive',
                array(
                    'module' => 'export-spoke-json',
                    'controller' => 'items',
                    'action' => 'unindex'
                ),
                array(
                    'id' => '\d+',
                    'recursive' => '\d+',
                )
            )
        );
    }

    private function getSubitemCount($item) {
        $count = 1;
        $objects = get_db()->getTable('ItemRelationsRelation')->findByObjectItemId($item->id);
        $objectRelations = array();
        foreach ($objects as $object) {
            if ($object->getPropertyText() !== "Is Part Of") {
                continue;
            }
            if (!($subitem = get_record_by_id('item', $object->subject_item_id))) {
                continue;
            }
            $count += $this->getSubitemCount($subitem);
        }
        return $count;
    }
}
