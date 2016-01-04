<?php
/**
 * Export SPOKEdb JSON
 *
 * @copyright 2015 Michael Slone <m.slone@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @package Omeka\Plugins\ExportSpokeJson */

define('DS', DIRECTORY_SEPARATOR);
$pluginDir = dirname(dirname(dirname(dirname(__FILE__))));
require_once $pluginDir . DS . "RecursiveSuppression" . DS . "models" . DS . "SuppressionChecker.php";

define('LAST_FIRST_MIDDLE', 1);
define('FIRST_MIDDLE_LAST', 2);

class Output_SpokeJson
{
    public function __construct($item)
    {
        $this->_rights = NULL;
        $this->_item = $item;
        $metadata = array();
        $this->_itemType = $item->getItemType()->name;
        switch ($this->_itemType) {
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

    public function ark()
    {
        if (!isset($this->_ark)) {
            switch ($this->_itemType) {
            case "collections":
                $this->_ark = metadata($this->_item, array('Item Type Metadata', 'Collection ARK Identifier'), array('no_filter' => true));
                break;
            case "series":
                $this->_ark = metadata($this->_item, array('Item Type Metadata', 'Series ARK Identifier'), array('no_filter' => true));
                break;
            case "interviews":
                $this->_ark = metadata($this->_item, array('Item Type Metadata', 'Interview ARK Identifier'), array('no_filter' => true));
                break;
            }
        }
        return $this->_ark;
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
        $title = metadata($item, array('Dublin Core', 'Title'));
        $accession = metadata($item, array('Item Type Metadata', 'Collection Accession'));
        $metadata = array(
            'id' => $this->ark(),
            'recordtype_display' => 'collection',
            'recordtype_t' => 'collection',
            'restriction_t' => $restriction,
            'related_series_display' => array(),
            'keyword_display' => metadata($item, array('Item Type Metadata', 'Collection Keyword'), array('all' => true, 'no_filter' => true)),
            'keyword_t' => metadata($item, array('Item Type Metadata', 'Collection Keyword'), array('all' => true, 'no_filter' => true)),
            'subject_display' => metadata($item, array('Item Type Metadata', 'Collection LC Subject'), array('all' => true, 'no_filter' => true)),
            'subject_facet' => metadata($item, array('Item Type Metadata', 'Collection LC Subject'), array('all' => true, 'no_filter' => true)),
            'subject_t' => metadata($item, array('Item Type Metadata', 'Collection LC Subject'), array('all' => true, 'no_filter' => true)),
            'material_type_display' => metadata($item, array('Item Type Metadata', 'Collection Master Type')),
            'material_type_t' => metadata($item, array('Item Type Metadata', 'Collection Master Type')),
            'accession_number_display' => $accession,
            'accession_number_s' => $accession,
            'accession_number_t' => $accession,
            'collection_display' => metadata($item, array('Dublin Core', 'Title')),
            'collection_facet' => metadata($item, array('Dublin Core', 'Title')),
            'title_display' => $title,
            'title_t' => $title,
            'longtitle_display' => "$title ($accession)",
            'longtitle_t' => "$title ($accession)",
            'title_added_entry_display' => metadata($item, array('Item Type Metadata', 'Collection Summary'), array('all' => true, 'no_filter' => true)),
            'title_added_entry_t' => metadata($item, array('Item Type Metadata', 'Collection Summary'), array('all' => true, 'no_filter' => true)),
            'theme_display' => metadata($item, array('Item Type Metadata', 'Collection Theme'), array('all' => true, 'no_filter' => true)),
            'theme_t' => metadata($item, array('Item Type Metadata', 'Collection Theme'), array('all' => true, 'no_filter' => true)),
        );

        if ($restriction === 'False') {
            $metadata['restriction_display'] = "No Restrictions";
        }
        else {
            $metadata['restriction_display'] = "Restrictions";
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
            $checker = new SuppressionChecker($subitem);
            if ($checker->exportable()) {
                $metadata['related_series_display'][] = array(
                    'id' => metadata($subitem, array('Item Type Metadata', 'Series ARK Identifier'), array('no_filter' => true)),
                    'label' => metadata($subitem, array('Dublin Core', 'Title'), array('no_filter' => true)),
                    'accession_number' => metadata($subitem, array('Item Type Metadata', 'Series Accession')),
                );
            }
        }
        $metadata['related_series_t'] = $metadata['related_series_display'];

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
            'id' => $this->ark(),
            'recordtype_display' => 'series',
            'recordtype_t' => 'series',
            'title_display' => $title,
            'title_t' => $title,
            'restriction_t' => $restriction,
            'related_series_display' => array(),
            'series_display' => $title,
            'series_facet' => $title,
            'subject_display' => $subjects,
            'subject_facet' => $subjects,
            'subject_t' => $subjects,
            'keyword_display' => $keywords,
            'keyword_t' => $keywords,
            'theme_display' => $themes,
            'theme_t' => $themes,
            'accession_number_display' => $accession_number,
            'accession_number_s' => $accession_number,
            'accession_number_t' => $accession_number,
            'longtitle_display' => "$title ($accession_number)",
            'longtitle_t' => "$title ($accession_number)",
            'title_added_entry_display' => $summary,
            'title_added_entry_t' => $summary,
            'material_type_display' => $type,
            'material_type_t' => $type,
        );

        if ($restriction === 'False') {
            $metadata['restriction_display'] = "No Restrictions";
        }
        else {
            $metadata['restriction_display'] = "Restrictions";
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
            $checker = new SuppressionChecker($subitem);
            if ($checker->exportable()) {
                $metadata['related_series_display'][] = array(
                    'id' => metadata($subitem, array('Item Type Metadata', 'Interview ARK Identifier'), array('no_filter' => true)),
                    'label' => metadata($subitem, array('Dublin Core', 'Title'), array('no_filter' => true)),
                    'accession_number' => metadata($subitem, array('Dublin Core', 'Identifier')),
                );
            }
        }
        $metadata['related_series_t'] = $metadata['related_series_display'];

        $relatedCollections = array();
        $subjects = get_db()->getTable('ItemRelationsRelation')->findBySubjectItemId($item->id);
        $subjectRelations = array();
        foreach ($subjects as $subject) {
            if ($subject->getPropertyText() !== "Is Part Of") {
                continue;
            }
            if (!($subitem = get_record_by_id('item', $subject->object_item_id))) {
                continue;
            }
            $checker = new SuppressionChecker($subitem);
            if ($checker->exportable()) {
                $relatedCollections[] = metadata($subitem, array('Dublin Core', 'Title'), array('no_filter' => true));
            }
        }
        $metadata['collection_display'] = implode('', $relatedCollections);
        $metadata['collection_facet'] = $metadata['collection_display'];

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

        $interviewees_lfm = $this->getNames(metadata($item, array('Item Type Metadata', 'Interviewee Name'), array('all' => true, 'no_filter' => true)), LAST_FIRST_MIDDLE);
        $interviewees_fml = $this->getNames(metadata($item, array('Item Type Metadata', 'Interviewee Name'), array('all' => true, 'no_filter' => true)));
        $interviewers = $this->getNames(metadata($item, array('Item Type Metadata', 'Interviewer Name'), array('all' => true, 'no_filter' => true)));

        if ($restriction === "False") {
            $metadata = array(
                'id' => $this->ark(),
                'recordtype_display' => 'interview',
                'recordtype_t' => 'interview',
                'restriction_t' => $restriction,
                'interviewee_display' => $interviewees_lfm,
                'interviewee_t' => $interviewees_lfm,
                'interviewee_facet' => $interviewees_lfm,
                'author_display' => $interviewees_fml,
                'author_facet' => $interviewees_fml,
                'author_t' => $interviewees_fml,
                'interviewer_display' => $interviewers,
                'interviewer_t' => $interviewers,
                'accession_number_display' => $accession,
                'accession_number_s' => $accession,
                'accession_number_t' => $accession,
                'related_series_display' => array(),
                'subject_display' => $subjects,
                'subject_facet' => $subjects,
                'subject_t' => $subjects,
                'title_added_entry_display' => $summary,
                'title_added_entry_t' => $summary,
                'title_display' => $title,
                'title_t' => $title,
                'longtitle_display' => "$title ($accession)",
                'longtitle_t' => "$title ($accession)",
                'date_display' => metadata($item, array('Item Type Metadata', 'Interview Date'), array('all' => true, 'no_filter' => true)),
                'date_t' => metadata($item, array('Item Type Metadata', 'Interview Date'), array('all' => true, 'no_filter' => true)),
                'interview_image_display' => metadata($item, array('Item Type Metadata', 'Interview Featured Image'), array('no_filter' => true)),
            );
            $metadata = array_merge($metadata, array(
                'cachefile_display' => metadata($item, array('Item Type Metadata', 'Interview Cache File')),
                'keyword_display' => $keywords,
                'keyword_t' => $keywords,
            ));
            $metadata['restriction_display'] = "No Restrictions";
        }
        else {
            $metadata = array(
                'id' => $this->ark(),
                'recordtype_display' => 'interview',
                'recordtype_t' => 'interview',
                'restriction_t' => $restriction,
                'interviewee_display' => $interviewees_lfm,
                'interviewee_t' => $interviewees_lfm,
                'interviewee_facet' => $interviewees_lfm,
                'author_display' => $interviewees_fml,
                'author_facet' => $interviewees_fml,
                'author_t' => $interviewees_fml,
                'interviewer_display' => $interviewers,
                'interviewer_t' => $interviewers,
                'accession_number_display' => $accession,
                'accession_number_s' => $accession,
                'accession_number_t' => $accession,
                'related_series_display' => array(),
                'title_display' => $title,
                'title_t' => $title,
                'date_display' => metadata($item, array('Item Type Metadata', 'Interview Date'), array('all' => true, 'no_filter' => true)),
                'date_t' => metadata($item, array('Item Type Metadata', 'Interview Date'), array('all' => true, 'no_filter' => true)),
            );
            $metadata['restriction_display'] = "Restrictions";
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
            $checker = new SuppressionChecker($subitem);
            if ($checker->exportable()) {
                $label = metadata($subitem, array('Dublin Core', 'Title'), array('no_filter' => true));
                $metadata['related_series_display'][] = array(
                    'id' => metadata($subitem, array('Item Type Metadata', 'Series ARK Identifier'), array('no_filter' => true)),
                    'label' => $label,
                );
                $relatedSeries[] = $label;
            }
        }
        $metadata['related_series_t'] = $metadata['related_series_display'];
        $metadata['series_display'] = implode('', $relatedSeries);

        return $metadata;
    }

    public function toJson()
    {
        return json_encode($this->_metadata);
    }

    public function getNames($list, $order = FIRST_MIDDLE_LAST) {
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
            if ($order == FIRST_MIDDLE_LAST) {
                $people[] = implode(' ', $pieces);
            }
            else {
                $lastName = array_pop($pieces);
                $suffix = implode(' ', $pieces);
                $people[] = $lastName . ', ' . $suffix;
            }
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
