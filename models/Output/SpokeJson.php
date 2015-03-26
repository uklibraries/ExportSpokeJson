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
            $metadata = $this->getSeriesFields($item);
            break;
        case "interviews":
            $metadata = $this->getInterviewFields($item);
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

    public function getSeriesFields($item)
    {
        $metadata = array(
            'id' => metadata($item, array('Dublin Core', 'Identifier')),
            'rights_display' => metadata($item, array('Dublin Core', 'Rights')),
            'format_display' => metadata($item, array('Dublin Core', 'Format')),
            'SeriesExternalLinkR_display' => metadata($item, array('Item Type Metadata', 'Series External Link')),
            'SeriesInterviewR_display' => metadata($item, array('Item Type Metadata', 'Series Interview')),
            'SeriesNumberofInterviews_display' => metadata($item, array('Item Type Metadata', 'Series Number of Interviews')),
            'SeriesTitle_display' => metadata($item, array('Item Type Metadata', 'Series Title')),
            'SeriesLCSubjectR_display' => metadata($item, array('Item Type Metadata', 'Series LC Subject')),
            'SeriesKeywordR_display' => metadata($item, array('Item Type Metadata', 'Series Keyword')),
            'SeriesThemeR_display' => metadata($item, array('Item Type Metadata', 'Series Theme')),
            'SeriesAccession_display' => metadata($item, array('Item Type Metadata', 'Series Accession')),
            'SeriesCollection_display' => metadata($item, array('Item Type Metadata', 'Series Collection')),
            'SeriesSummary_display' => metadata($item, array('Item Type Metadata', 'Series Summary')),
            'SeriesMasterType_display' => metadata($item, array('Item Type Metadata', 'Series Master Type')),
        );

        return $metadata;
    }

    public function getInterviewFields($item)
    {
        $metadata = array(
            'id' => metadata($item, array('Dublin Core', 'Identifier')),
            'rights_display' => metadata($item, array('Dublin Core', 'Rights')),
            'format_display' => metadata($item, array('Dublin Core', 'Format')),
            'InterviewUsageStatement_display' => metadata($item, array('Item Type Metadata', 'Interview Usage Statement')),
            'InterviewExternalLinkR_display' => metadata($item, array('Item Type Metadata', 'Interview External Link')),
            'InterviewYear_display' => metadata($item, array('Item Type Metadata', 'Interview Year')),
            'InterviewRestrictionDetails_display' => metadata($item, array('Item Type Metadata', 'Interview Restriction Details')),
            'InterviewMonth_display' => metadata($item, array('Item Type Metadata', 'Interview Month')),
            'InterviewCacheFile_display' => metadata($item, array('Item Type Metadata', 'Interview Cache File')),
            'InterviewKeywordsR_display' => metadata($item, array('Item Type Metadata', 'Interview Keywords')),
            'InterviewDate_display' => metadata($item, array('Item Type Metadata', 'Interview Date')),
            'InterviewerR_display' => metadata($item, array('Item Type Metadata', 'Interviewer')),
            'IntervieweeR_display' => metadata($item, array('Item Type Metadata', 'Interviewee')),
            'InterviewAccessionNumber_display' => metadata($item, array('Item Type Metadata', 'Interview Accession Number')),
            'InterviewTitle_display' => metadata($item, array('Item Type Metadata', 'Interview Title')),
            'InterviewDay_display' => metadata($item, array('Item Type Metadata', 'Interview Day')),
            'UseRestrictions_display' => metadata($item, array('Item Type Metadata', 'Use Restrictions')),
            'OnlineIdentifier_display' => metadata($item, array('Item Type Metadata', 'Online Identifier')),
            'InterviewMasterType_display' => metadata($item, array('Item Type Metadata', 'Interview Master Type')),
            'InterviewRightsStatement_display' => metadata($item, array('Item Type Metadata', 'Interview Rights Statement')),
            'InterviewLCSubjectR_display' => metadata($item, array('Item Type Metadata', 'Interview LC Subject')),
            'InterviewCollection_display' => metadata($item, array('Item Type Metadata', 'Interview Collection')),
            'InterviewSeries_display' => metadata($item, array('Item Type Metadata', 'Interview Series')),
        );

        return $metadata;
    }

    public function toJson()
    {
        return json_encode($this->_metadata);
    }

    private $_metadata;
}
