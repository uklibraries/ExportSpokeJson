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
        $filename = $path . DIRECTORY_SEPARATOR . $output->id() . '.json';
        file_put_contents($filename, $output->toJSON());
        chmod($filename, fileperms($filename) | 16);
        switch ($output->itemType()) {
        case "collections":
            $subType = 'series';
            $subField = 'Series Collection';
            $itemType = get_record('ItemType', array(
                'name' => $subType,
            ));
            $itemTypeId = $itemType['id'];
            $itemTypesElements = $itemType->Elements;
            $element = NULL;
            foreach ($itemTypesElements as $itemTypesElement) {
                if ($itemTypesElement->name === $subField) {
                    $element = $itemTypesElement;
                    break;
                }
            }
            if (isset($element)) {
                $elementId = $element['id'];
                $relatedItems = get_records('Item', array(
                    'advanced' => array(
                        array(
                            'element_id' => $elementId,
                            'type' => 'is exactly',
                            'terms' => $output->title(),
                        )
                    )
                ));
                foreach ($relatedItems as $relatedItem) {
                    Zend_Registry::get('bootstrap')->getResource('jobs')->sendLongRunning(
                        'ExportSpokeJson_Job_ExportItem', array(
                            'itemId' => $relatedItem['id']
                        )
                    );
                }
            }
            break;
        case "series":
            $subType = 'interviews';
            $subField = 'Interview Series';
            $itemType = get_record('ItemType', array(
                'name' => $subType,
            ));
            $itemTypeId = $itemType['id'];
            $itemTypesElements = $itemType->Elements;
            $element = NULL;
            foreach ($itemTypesElements as $itemTypesElement) {
                if ($itemTypesElement->name === $subField) {
                    $element = $itemTypesElement;
                    break;
                }
            }
            if (isset($element)) {
                $elementId = $element['id'];
                $relatedItems = get_records('Item', array(
                    'advanced' => array(
                        array(
                            'element_id' => $elementId,
                            'type' => 'is exactly',
                            'terms' => $output->title(),
                        )
                    )
                ));
                foreach ($relatedItems as $relatedItem) {
                    Zend_Registry::get('bootstrap')->getResource('jobs')->sendLongRunning(
                        'ExportSpokeJson_Job_ExportItem', array(
                            'itemId' => $relatedItem['id']
                        )
                    );
                }
            }
            break;
        }
    }
}
