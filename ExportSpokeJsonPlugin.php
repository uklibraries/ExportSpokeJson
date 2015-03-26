<?php
/**
 * Export SPOKEdb JSON
 *
 * @copyright 2015 Eric C. Weig
 * @license http://opensource.org/licenses/MIT MIT
 * @package Omeka\Plugins\ExportSpokeJson
 */

class ExportSpokeJsonPlugin extends Omeka_Plugin_AbstractPlugin
{
    protected $_filters = array(
        'response_contexts',
        'action_contexts',
    );

    public function filterResponseContexts($contexts)
    {
        $contexts['spoke-json'] = array(
            'suffix' => 'spoke-json',
            'headers' => array('Content-Type' => 'application/json')
        );
        return $contexts;
    }

    public function filterActionContexts($contexts, $args)
    {
        if ($args['controller'] instanceof ItemsController) {
            $contexts['show'][] = 'spoke-json';
        }
        return $contexts;
    }
}
