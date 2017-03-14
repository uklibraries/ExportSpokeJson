<?php
/**
 * Export SPOKEdb JSON
 *
 * @copyright 2015 Michael Slone <m.slone@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @package Omeka\Plugins\ExportSpokeJson */

class ExportSpokeJson_Job_UnindexInterviews extends Omeka_Job_AbstractJob
{
    public function perform()
    {
        $item = get_record_by_id('Item', $this->_options['itemId']);
        $this->clear($item);
    }

    protected function clear($item)
    {
        $objects = get_db()->getTable('ItemRelationsRelation')->findByObjectItemId($item->id);
        $objectRelations = array();
        foreach ($objects as $object) {
            if ($object->getPropertyText() !== "Is Part Of") {
                continue;
            }
            if (!($subitem = get_record_by_id('item', $object->subject_item_id))) {
                continue;
            }

            switch ($subitem->getItemType()->name) {
            case "collections":
                $this->clear($subitem, $recursive);
                break;
            case "series":
                $this->clear($subitem, $recursive);
                break;
            case "interviews":
                Zend_Registry::get('bootstrap')->getResource('jobs')->sendLongRunning(
                    'ExportSpokeJson_Job_UnindexItem', array(
                        'itemId' => $subitem->id,
                        'recursive' => "0",
                    )
                );
            }
        }
    }
}
