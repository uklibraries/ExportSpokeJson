<?php
/**
 * Export SPOKEdb JSON
 *
 * @copyright 2015 Michael Slone <m.slone@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @package Omeka\Plugins\ExportSpokeJson */

class ExportSpokeJson_Job_ExportItem extends Omeka_Job_AbstractJob
{
    public function perform()
    {
        $path = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'tmp';
        $item = get_record_by_id('Item', $this->_options['itemId']);
        $output = new Output_SpokeJson($item);

        if ($output->exportable()) {
            $filename = $path . DIRECTORY_SEPARATOR . $output->id() . '.json';
            file_put_contents($filename, $output->toJSON());
            chmod($filename, fileperms($filename) | 16);
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
                        )
                    );
                }
                break;
            }
        }
    }
}
