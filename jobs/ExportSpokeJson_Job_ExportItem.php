<?php
/**
 * Export SPOKEdb JSON
 *
 * @copyright 2015 Michael Slone <m.slone@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @package Omeka\Plugins\ExportSpokeJson */

define('DS', DIRECTORY_SEPARATOR);
$pluginDir = dirname(dirname(dirname(__FILE__)));
require_once $pluginDir . DS . "RecursiveSuppression" . DS . "models" . DS . "SuppressionChecker.php";

class ExportSpokeJson_Job_ExportItem extends Omeka_Job_AbstractJob
{
    public function perform()
    {
        $path = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'tmp';
        $recursive = $this->_options['recursive'];
        $export_path = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'exports';
        mkdir($export_path, 0775, true);
        $item = get_record_by_id('Item', $this->_options['itemId']);
        $checker = new SuppressionChecker($item);

        if ($checker->exportable()) {
            $output = new Output_SpokeJson($item);
            $filename = $path . DIRECTORY_SEPARATOR . $output->ark() . '.json';
            file_put_contents($filename, $output->toJSON());
            chmod($filename, fileperms($filename) | 16);
            $export_filename = $export_path . DIRECTORY_SEPARATOR . $output->ark() . '.json';
            rename($filename, $export_filename);
            if ($recursive === "1") {
                switch ($output->itemType()) {
                case "collections":
                    $objects = get_db()->getTable('ItemRelationsRelation')->findByObjectItemId($item->id);
                    $objectRelations = array();
                    foreach ($objects as $object) {
                        if ($object->getPropertyText() !== "Is Part Of") {
                            continue;
                        }
                        if (!($subitem = get_record_by_id('item', $object->subject_item_id))) {
                            continue;
                        }
                        Zend_Registry::get('bootstrap')->getResource('jobs')->sendLongRunning(
                            'ExportSpokeJson_Job_ExportItem', array(
                                'itemId' => $object->subject_item_id,
                                'recursive' => $recursive,
                            )
                        );
                    }
                    break;
                case "series":
                    $objects = get_db()->getTable('ItemRelationsRelation')->findByObjectItemId($item->id);
                    $objectRelations = array();
                    foreach ($objects as $object) {
                        if ($object->getPropertyText() !== "Is Part Of") {
                            continue;
                        }
                        if (!($subitem = get_record_by_id('item', $object->subject_item_id))) {
                            continue;
                        }
                        Zend_Registry::get('bootstrap')->getResource('jobs')->sendLongRunning(
                            'ExportSpokeJson_Job_ExportItem', array(
                                'itemId' => $object->subject_item_id,
                                'recursive' => $recursive,
                            )
                        );
                    }
                    break;
                }
            }
        }
    }
}
