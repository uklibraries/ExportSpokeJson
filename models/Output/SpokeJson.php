<?php
/**
 * Export SPOKEdb JSON
 *
 * @copyright 2015 Eric C. Weig
 * @license http://opensource.org/licenses/MIT MIT
 * @package Omeka\Plugins\ExportSpokeJson */

class Output_SpokeJson
{
    public function __construct($item)
    {
        $metadata = array();
        $itemType = $item->getItemType();
        switch($itemType->name) {
        case "collections":
            $metadata = $this->getCollectionFields($item);
            break;
        case "series":
            $metadata = array('foo' => 'series');
            break;
        case "interviews":
            $metadata = array('foo' => 'interviews');
            break;
        }
        $this->_metadata = $metadata;
    }

    public function getCollectionFields($item)
    {
        $metadata = array(
            'id' => metadata($item, array('Dublin Core', 'Identifier')),
            'rights_display' => metadata($item, array('Dublin Core', 'Rights')),
            'format_display' => metadata($item, array('Dublin Core', 'Format')),
            'CollectionExternalLinkR_display' => metadata($item, array('Item Type Metadata', 'Collection External Link')),
            'CollectionKeywordR_display' => metadata($item, array('Item Type Metadata', 'Collection Keyword')),
            'CollectionLCSubjectR_display' => metadata($item, array('Item Type Metadata', 'Collection LC Subject')),
            'CollectionTitle_display' => metadata($item, array('Item Type Metadata', 'Collection Title')),
            'CollectionSeriesR_display' => metadata($item, array('Item Type Metadata', 'Collection Series')),
            'CollectionMasterType_display' => metadata($item, array('Item Type Metadata', 'Collection Master Type')),
            'CollectionAccession_display' => metadata($item, array('Item Type Metadata', 'Collection Accession')),
            'CollectionNumberofInterviews_display' => metadata($item, array('Item Type Metadata', 'Collection Number of Interviews')),
            'CollectionDIP_display' => metadata($item, array('Item Type Metadata', 'Collection DIP')),
            'CollectionSummary_display' => metadata($item, array('Item Type Metadata', 'Collection Summary')),
            'CollectionThemeR_display' => metadata($item, array('Item Type Metadata', 'Collection Theme')),
        );

        return $metadata;
    }

    public function toJson()
    {
        return json_encode($this->_metadata);
    }

    private $_metadata;
}
