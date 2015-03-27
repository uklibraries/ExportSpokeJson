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
            'id' => metadata($item, array('Dublin Core', 'Identifier'), array('no_filter' => true)),
            'rights_display' => metadata($item, array('Dublin Core', 'Rights'), array('all' => true, 'no_filter' => true)),
            'format_display' => metadata($item, array('Dublin Core', 'Format'), array('all' => true, 'no_filter' => true)),
            'CollectionExternalLinkR_display' => metadata($item, array('Item Type Metadata', 'Collection External Link'), array('all' => true, 'no_filter' => true)),
            'CollectionKeywordR_display' => metadata($item, array('Item Type Metadata', 'Collection Keyword'), array('all' => true, 'no_filter' => true)),
            'CollectionLCSubjectR_display' => metadata($item, array('Item Type Metadata', 'Collection LC Subject'), array('all' => true, 'no_filter' => true)),
            'CollectionTitle_display' => metadata($item, array('Item Type Metadata', 'Collection Title'), array('all' => true, 'no_filter' => true)),
            'CollectionSeriesR_display' => metadata($item, array('Item Type Metadata', 'Collection Series'), array('all' => true, 'no_filter' => true)),
            'CollectionMasterType_display' => metadata($item, array('Item Type Metadata', 'Collection Master Type'), array('all' => true, 'no_filter' => true)),
            'CollectionAccession_display' => metadata($item, array('Item Type Metadata', 'Collection Accession'), array('all' => true, 'no_filter' => true)),
            'CollectionNumberofInterviews_display' => metadata($item, array('Item Type Metadata', 'Collection Number of Interviews'), array('all' => true, 'no_filter' => true)),
            'CollectionDIP_display' => metadata($item, array('Item Type Metadata', 'Collection DIP'), array('all' => true, 'no_filter' => true)),
            'CollectionSummary_display' => metadata($item, array('Item Type Metadata', 'Collection Summary'), array('all' => true, 'no_filter' => true)),
            'CollectionThemeR_display' => metadata($item, array('Item Type Metadata', 'Collection Theme'), array('all' => true, 'no_filter' => true)),
        );

        return $metadata;
    }

    public function getSeriesFields($item)
    {
        $metadata = array(
            'id' => metadata($item, array('Dublin Core', 'Identifier'), array('no_filter' => true)),
            'rights_display' => metadata($item, array('Dublin Core', 'Rights'), array('all' => true, 'no_filter' => true)),
            'format_display' => metadata($item, array('Dublin Core', 'Format'), array('all' => true, 'no_filter' => true)),
            'SeriesExternalLinkR_display' => metadata($item, array('Item Type Metadata', 'Series External Link'), array('all' => true, 'no_filter' => true)),
            'SeriesInterviewR_display' => metadata($item, array('Item Type Metadata', 'Series Interview'), array('all' => true, 'no_filter' => true)),
            'SeriesNumberofInterviews_display' => metadata($item, array('Item Type Metadata', 'Series Number of Interviews'), array('all' => true, 'no_filter' => true)),
            'SeriesTitle_display' => metadata($item, array('Item Type Metadata', 'Series Title'), array('all' => true, 'no_filter' => true)),
            'SeriesLCSubjectR_display' => metadata($item, array('Item Type Metadata', 'Series LC Subject'), array('all' => true, 'no_filter' => true)),
            'SeriesKeywordR_display' => metadata($item, array('Item Type Metadata', 'Series Keyword'), array('all' => true, 'no_filter' => true)),
            'SeriesThemeR_display' => metadata($item, array('Item Type Metadata', 'Series Theme'), array('all' => true, 'no_filter' => true)),
            'SeriesAccession_display' => metadata($item, array('Item Type Metadata', 'Series Accession'), array('all' => true, 'no_filter' => true)),
            'SeriesCollection_display' => metadata($item, array('Item Type Metadata', 'Series Collection'), array('all' => true, 'no_filter' => true)),
            'SeriesSummary_display' => metadata($item, array('Item Type Metadata', 'Series Summary'), array('all' => true, 'no_filter' => true)),
            'SeriesMasterType_display' => metadata($item, array('Item Type Metadata', 'Series Master Type'), array('all' => true, 'no_filter' => true)),
        );

        return $metadata;
    }

    public function getInterviewFields($item)
    {
        $metadata = array(
            'id' => metadata($item, array('Dublin Core', 'Identifier'), array('no_filter' => true)),
            'rights_display' => metadata($item, array('Dublin Core', 'Rights'), array('all' => true, 'no_filter' => true)),
            'format_display' => metadata($item, array('Dublin Core', 'Format'), array('all' => true, 'no_filter' => true)),
            'InterviewUsageStatement_display' => metadata($item, array('Item Type Metadata', 'Interview Usage Statement'), array('all' => true, 'no_filter' => true)),
            'InterviewExternalLinkR_display' => metadata($item, array('Item Type Metadata', 'Interview External Link'), array('all' => true, 'no_filter' => true)),
            'InterviewYear_display' => metadata($item, array('Item Type Metadata', 'Interview Year'), array('all' => true, 'no_filter' => true)),
            'InterviewRestrictionDetails_display' => metadata($item, array('Item Type Metadata', 'Interview Restriction Details'), array('all' => true, 'no_filter' => true)),
            'InterviewMonth_display' => metadata($item, array('Item Type Metadata', 'Interview Month'), array('all' => true, 'no_filter' => true)),
            'InterviewCacheFile_display' => metadata($item, array('Item Type Metadata', 'Interview Cache File'), array('all' => true, 'no_filter' => true)),
            'InterviewKeywordsR_display' => metadata($item, array('Item Type Metadata', 'Interview Keywords'), array('all' => true, 'no_filter' => true)),
            'InterviewDate_display' => metadata($item, array('Item Type Metadata', 'Interview Date'), array('all' => true, 'no_filter' => true)),
            'InterviewerR_display' => metadata($item, array('Item Type Metadata', 'Interviewer'), array('all' => true, 'no_filter' => true)),
            'IntervieweeR_display' => metadata($item, array('Item Type Metadata', 'Interviewee'), array('all' => true, 'no_filter' => true)),
            'InterviewAccessionNumber_display' => metadata($item, array('Item Type Metadata', 'Interview Accession Number'), array('all' => true, 'no_filter' => true)),
            'InterviewTitle_display' => metadata($item, array('Item Type Metadata', 'Interview Title'), array('all' => true, 'no_filter' => true)),
            'InterviewDay_display' => metadata($item, array('Item Type Metadata', 'Interview Day'), array('all' => true, 'no_filter' => true)),
            'UseRestrictions_display' => metadata($item, array('Item Type Metadata', 'Use Restrictions'), array('all' => true, 'no_filter' => true)),
            'OnlineIdentifier_display' => metadata($item, array('Item Type Metadata', 'Online Identifier'), array('all' => true, 'no_filter' => true)),
            'InterviewMasterType_display' => metadata($item, array('Item Type Metadata', 'Interview Master Type'), array('all' => true, 'no_filter' => true)),
            'InterviewRightsStatement_display' => metadata($item, array('Item Type Metadata', 'Interview Rights Statement'), array('all' => true, 'no_filter' => true)),
            'InterviewLCSubjectR_display' => metadata($item, array('Item Type Metadata', 'Interview LC Subject'), array('all' => true, 'no_filter' => true)),
            'InterviewCollection_display' => metadata($item, array('Item Type Metadata', 'Interview Collection'), array('all' => true, 'no_filter' => true)),
            'InterviewSeries_display' => metadata($item, array('Item Type Metadata', 'Interview Series'), array('all' => true, 'no_filter' => true)),
        );

        return $metadata;
    }

    public function toJson()
    {
        return json_encode($this->_metadata);
    }

    private $_metadata;
}
