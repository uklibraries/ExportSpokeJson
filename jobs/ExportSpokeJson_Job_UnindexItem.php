<?php
/**
 * Export SPOKEdb JSON
 *
 * @copyright 2015 Michael Slone <m.slone@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @package Omeka\Plugins\ExportSpokeJson */

define('DS', DIRECTORY_SEPARATOR);

class ExportSpokeJson_Job_UnindexItem extends Omeka_Job_AbstractJob
{
    public function perform()
    {
        $item = get_record_by_id('Item', $this->_options['itemId']);
        $recursive = $this->_options['recursive'];
        $this->clear($item, $recursive);
    }

    protected function clear($item, $recursive)
    {
        $path = dirname(dirname(__FILE__)) . DS . 'tmp';
        mkdir($path, 0775, true);
        $unindex_path = dirname(dirname(__FILE__)) . DS . 'deletes';
        mkdir($unindex_path, 0775, true);
        chmod($unindex_path, 0775);

        # delete this item
        $output = new Output_SpokeJson($item);
        $filename = $path . DS . $output->ark();
        touch($filename);
        chmod($filename, 0664);
        $unindex_filename = $unindex_path . DS . $output->ark();
        rename($filename, $unindex_filename);

        # ...and subobjects?
        if ($recursive === "1") {
                $objects = get_db()->getTable('ItemRelationsRelation')->findByObjectItemId($item->id);
                $objectRelations = array();
                foreach ($objects as $object) {
                    if ($object->getPropertyText() !== "Is Part Of") {
                        continue;
                    }
                    if (!($subitem = get_record_by_id('item', $object->subject_item_id))) {
                        continue;
                    }
                    $this->clear($subitem, $recursive);
                }
        }
    }
}
