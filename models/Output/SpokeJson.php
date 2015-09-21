<?php
/**
 * Export SPOKEdb JSON
 *
 * @copyright 2015 Michael Slone <m.slone@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @package Omeka\Plugins\ExportSpokeJson */

class Output_SpokeJson
{
    public function __construct($item)
    {
        $this->_rights = NULL;
        $this->_item = $item;
        $metadata = array();
        $this->_itemType = $item->getItemType()->name;
        switch($this->_itemType) {
        case "collections":
            $metadata = $this->getCollectionFields();
            break;
        case "series":
            $metadata = $this->getSeriesFields();
            break;
        case "interviews":
            $metadata = $this->getInterviewFields();
            break;
        }
        $this->_metadata = $metadata;
    }

    public function parents()
    {
        $subjects = get_db()->getTable('ItemRelationsRelation')->findBySubjectItemId($this->_item->id);
        $results = array();
        foreach ($subjects as $subject) {
            if ($subject->getPropertyText() !== "Is Part Of") {
                continue;
            }
            if (!($superItem = get_record_by_id('item', $subject->object_item_id))) {
                continue;
            }
            $results[] = $superItem;
        }
        return $results;
    }

    public function exportable()
    {
        $exportable = false;
        switch($this->_itemType) {
        case "collections":
            $raw_suppression = metadata($this->_item, array('Item Type Metadata', 'Collection Suppressed'), array('no_filter' => true));
            $raw_suppression = str_replace('&quot;', '"', $raw_suppression);
            $suppression = json_decode($raw_suppression, true);
            $exportable = $suppression['description'] ? false : true;
            break;
        case "series":
            $raw_suppression = metadata($this->_item, array('Item Type Metadata', 'Series Suppressed'), array('no_filter' => true));
            $suppression = json_decode($raw_suppression, true);
            if ($suppression['description']) {
                $exportable = false;
            }
            else {
                $exportable = true;
                foreach ($this->parents() as $parent) {
                    $parent_raw_suppression = metadata($parent, array('Item Type Metadata', 'Collection Suppressed'), array('no_filter' => true));
                    $parent_raw_suppression = str_replace('&quot;', '"', $parent_raw_suppression);
                    $parent_suppression = json_decode($parent_raw_suppression, true);
                    if ($parent_suppression['recursive']) {
                        $exportable = false;
                    }
                }
            }
            break;
        case "interviews":
            $suppression = metadata($this->_item, array('Item Type Metadata', 'Interview Suppressed'), array('no_filter' => true));
            if (strlen($suppression) > 0) {
                $exportable = false;
            }
            else {
                $exportable = true;
                foreach ($this->parents() as $parent) {
                    $parent_raw_suppression = metadata($parent, array('Item Type Metadata', 'Series Suppressed'), array('no_filter' => true));
                    $parent_raw_suppression = str_replace('&quot;', '"', $parent_raw_suppression);
                    $parent_suppression = json_decode($parent_raw_suppression, true);
                    if ($parent_suppression['recursive']) {
                        $exportable = false;
                    }
                    else {
                        $parentOutput = new Output_SpokeJson($parent);
                        foreach ($parentOutput->parents() as $grandparent) {
                            $grandparent_raw_suppression = metadata($grandparent, array('Item Type Metadata', 'Collection Suppressed'), array('no_filter' => true));
                            $grandparent_raw_suppression = str_replace('&quot;', '"', $grandparent_raw_suppression);
                            $grandparent_suppression = json_decode($grandparent_raw_suppression, true);
                            if ($grandparent_suppression['recursive']) {
                                $exportable = false;
                            }
                        }
                    }
                }
            }
            break;
        }
        return $exportable;
    }

    public function rights()
    {
        if (!isset($this->_rights)) {
            $this->_rights = metadata($this->_item, array('Dublin Core', 'Rights'), array('no_filter' => true));
        }
        return $this->_rights;
    }

    public function id()
    {
        if (!isset($this->_id)) {
            $this->_id = metadata($this->_item, array('Dublin Core', 'Identifier'), array('no_filter' => true));
        }
        return $this->_id;
    }

    public function title()
    {
        if (!isset($this->_title)) {
            $this->_title = metadata($this->_item, array('Dublin Core', 'Title'), array('no_filter' => true));
        }
        return $this->_title;
    }

    public function itemType()
    {
        return $this->_itemType;
    }

    public function getCollectionFields()
    {
        $item = $this->_item;
        $restriction = metadata($item, array('Dublin Core', 'Rights'), array('no_filter' => true));
        $metadata = array(
            'id' => metadata($item, array('Dublin Core', 'Identifier'), array('no_filter' => true)),
            'recordtype_display' => 'collection',
            'recordtype_t' => 'collection',
            'Restriction_t' => $restriction,
            'CacheFile_display' => 'nil',
            'Series_display' => 'nil',
            'RelatedSeries_display' => array(),
            'Keyword_display' => metadata($item, array('Item Type Metadata', 'Collection Keyword'), array('all' => true, 'no_filter' => true)),
            'Keyword_t' => metadata($item, array('Item Type Metadata', 'Collection Keyword'), array('all' => true, 'no_filter' => true)),
            'subject_display' => metadata($item, array('Item Type Metadata', 'Collection LC Subject'), array('all' => true, 'no_filter' => true)),
            'subject_facet' => metadata($item, array('Item Type Metadata', 'Collection LC Subject'), array('all' => true, 'no_filter' => true)),
            'subject_t' => metadata($item, array('Item Type Metadata', 'Collection LC Subject'), array('all' => true, 'no_filter' => true)),
            'Material_Type_display' => metadata($item, array('Item Type Metadata', 'Collection Master Type')),
            'Material_Type_t' => metadata($item, array('Item Type Metadata', 'Collection Master Type')),
            'AccessionNumber_display' => metadata($item, array('Item Type Metadata', 'Collection Accession')),
            'AccessionNumber_s' => metadata($item, array('Item Type Metadata', 'Collection Accession')),
            'AccessionNumber_t' => metadata($item, array('Item Type Metadata', 'Collection Accession')),
            'Collection_display' => metadata($item, array('Dublin Core', 'Title')),
            'Collection_facet' => metadata($item, array('Dublin Core', 'Title')),
            'title_display' => metadata($item, array('Dublin Core', 'Title')),
            'title_t' => metadata($item, array('Dublin Core', 'Title')),
            'title_added_entry_display' => metadata($item, array('Item Type Metadata', 'Collection Summary'), array('all' => true, 'no_filter' => true)),
            'title_added_entry_t' => metadata($item, array('Item Type Metadata', 'Collection Summary'), array('all' => true, 'no_filter' => true)),
            'Theme_display' => metadata($item, array('Item Type Metadata', 'Collection Theme'), array('all' => true, 'no_filter' => true)),
            'Theme_t' => metadata($item, array('Item Type Metadata', 'Collection Theme'), array('all' => true, 'no_filter' => true)),
        );

        if ($restriction === 'False') {
            $metadata['Restriction_display'] = "No Restrictions";
        }
        else {
            $metadata['Restriction_display'] = "Restrictions";
        }

        $objects = get_db()->getTable('ItemRelationsRelation')->findByObjectItemId($item->id);
        $objectRelations = array();
        foreach ($objects as $object) {
            if ($object->getPropertyText() !== "Is Part Of") {
                continue;
            }
            if (!($subitem = get_record_by_id('item', $object->subject_item_id))) {
                continue;
            }
            $metadata['RelatedSeries_display'][] = array(
                'id' => metadata($subitem, array('Dublin Core', 'Identifier'), array('no_filter' => true)),
                'label' => metadata($subitem, array('Dublin Core', 'Title'), array('no_filter' => true)),
            );
        }

        return $metadata;
    }

    public function getSeriesFields()
    {
        $item = $this->_item;
        $restriction = metadata($item, array('Dublin Core', 'Rights'), array('no_filter' => true));
        $accession_number = metadata($item, array('Item Type Metadata', 'Series Accession'));
        $keywords = metadata($item, array('Item Type Metadata', 'Series Keyword'), array('all' => true, 'no_filter' => true));
        $type = metadata($item, array('Item Type Metadata', 'Series Master Type'));
        $title = metadata($item, array('Dublin Core', 'Title'));
        $themes = metadata($item, array('Item Type Metadata', 'Series Theme'), array('all' => true, 'no_filter' => true));
        $subjects = metadata($item, array('Item Type Metadata', 'Series LC Subject'), array('all' => true, 'no_filter' => true));
        $summary = metadata($item, array('Item Type Metadata', 'Series Summary'), array('all' => true, 'no_filter' => true));
        $metadata = array(
            'id' => metadata($item, array('Dublin Core', 'Identifier'), array('no_filter' => true)),
            'recordtype_display' => 'series',
            'recordtype_t' => 'series',
            'title_display' => metadata($item, array('Dublin Core', 'Title')),
            'title_t' => metadata($item, array('Dublin Core', 'Title')),
            'Restriction_t' => $restriction,
            'RelatedSeries_display' => array(),
            'Series_display' => $title,
            'Series_facet' => $title,
            'subject_display' => $subjects,
            'subject_facet' => $subjects,
            'subject_t' => $subjects,
            'Keyword_display' => $keywords,
            'Keyword_t' => $keywords,
            'Theme_display' => $themes,
            'Theme_t' => $themes,
            'AccessionNumber_display' => $accession_number,
            'AccessionNumber_s' => $accession_number,
            'AccessionNumber_t' => $accession_number,
            'Collection_display' => metadata($item, array('Item Type Metadata', 'Series Collection')),
            'title_added_entry_display' => $summary,
            'title_added_entry_t' => $summary,
            'Material_Type_display' => $type,
            'Material_Type_t' => $type,
            'CacheFile_display' => 'nil',
        );

        if ($restriction === 'False') {
            $metadata['Restriction_display'] = "No Restrictions";
        }
        else {
            $metadata['Restriction_display'] = "Restrictions";
        }

        $objects = get_db()->getTable('ItemRelationsRelation')->findByObjectItemId($item->id);
        $objectRelations = array();
        foreach ($objects as $object) {
            if ($object->getPropertyText() !== "Is Part Of") {
                continue;
            }
            if (!($subitem = get_record_by_id('item', $object->subject_item_id))) {
                continue;
            }
            $metadata['RelatedSeries_display'][] = array(
                'id' => metadata($subitem, array('Dublin Core', 'Identifier'), array('no_filter' => true)),
                'label' => metadata($subitem, array('Dublin Core', 'Title'), array('no_filter' => true)),
            );
        }

        return $metadata;
    }

    public function getInterviewFields()
    {
        $item = $this->_item;
        $restriction = metadata($item, array('Dublin Core', 'Rights'), array('no_filter' => true));
        $accession = metadata($item, array('Dublin Core', 'Identifier'));
        $subjects = metadata($item, array('Item Type Metadata', 'Interview LC Subject'), array('all' => true, 'no_filter' => true));
        $keywords = metadata($item, array('Item Type Metadata', 'Interview Keyword'), array('all' => true, 'no_filter' => true));
        $summary = metadata($item, array('Item Type Metadata', 'Interview Summary'), array('all' => true, 'no_filter' => true));
        $title = metadata($item, array('Dublin Core', 'Title'));

        $interviewees = $this->getNames(metadata($item, array('Item Type Metadata', 'Interviewee Name'), array('all' => true, 'no_filter' => true)));
        $interviewers = $this->getNames(metadata($item, array('Item Type Metadata', 'Interviewer Name'), array('all' => true, 'no_filter' => true)));

        $metadata = array(
            'id' => metadata($item, array('Dublin Core', 'Identifier'), array('no_filter' => true)),
            'recordtype_display' => 'interview',
            'recordtype_t' => 'interview',
            'Restriction_t' => $restriction,
            'Interviewee_display' => $interviewees,
            'Interviewee_t' => $interviewees,
            'author_display' => $interviewees,
            'author_facet' => $interviewees,
            'author_t' => $interviewees,
            'Interviewer_display' => $interviewers,
            'Interviewer_t' => $interviewers,
            'AccessionNumber_display' => $accession,
            'AccessionNumber_s' => $accession,
            'AccessionNumber_t' => $accession,
            'Collection_display' => 'nil',
            'RelatedSeries_display' => array(),
            'subject_display' => $subjects,
            'subject_facet' => $subjects,
            'subject_t' => $subjects,
            'title_added_entry_display' => $summary,
            'title_added_entry_t' => $summary,
            'title_display' => $title,
            'title_t' => $title,
            'Date_display' => metadata($item, array('Item Type Metadata', 'Interview Date'), array('all' => true, 'no_filter' => true)),
        );
        $metadata = array_merge($metadata, array(
            'CacheFile_display' => metadata($item, array('Item Type Metadata', 'Interview Cache File')),
            'Keyword_display' => $keywords,
            'Keyword_t' => $keywords,
            'Material_Type_display' => 'nil',
            'Material_Type_t' => 'nil',
        ));

        if ($restriction === 'False') {
            $metadata['Restriction_display'] = "No Restrictions";
        }
        else {
            $metadata['Restriction_display'] = "Restrictions";
        }

        $relatedSeries = array();
        $subjects = get_db()->getTable('ItemRelationsRelation')->findBySubjectItemId($item->id);
        $subjectRelations = array();
        foreach ($subjects as $subject) {
            if ($subject->getPropertyText() !== "Is Part Of") {
                continue;
            }
            if (!($subitem = get_record_by_id('item', $subject->object_item_id))) {
                continue;
            }
            $label = metadata($subitem, array('Dublin Core', 'Title'), array('no_filter' => true));
            $metadata['RelatedSeries_display'][] = array(
                'id' => metadata($subitem, array('Dublin Core', 'Identifier'), array('no_filter' => true)),
                'label' => $label,
            );
            $relatedSeries[] = $label;
        }
        $metadata['Series_display'] = implode('', $relatedSeries);

        return $metadata;
    }

    public function toJson()
    {
        return json_encode($this->_metadata);
    }

    public function getNames($list) {
        $people = array();
        $keys = array('first', 'middle', 'last');
        foreach ($list as $text) {
            $person = json_decode(str_replace('&quot;', '"', $text), true);
            $pieces = array();
            foreach ($keys as $key) {
                if (isset($person[$key]) and strlen($person[$key]) > 0) {
                    $pieces[] = $person[$key];
                }
            }
            $people[] = implode(' ', $pieces);
        }
        return $people;
    }

    private $_metadata;
    private $_item;
    private $_id;
    private $_title;
    private $_rights;
    private $_itemType;
}
