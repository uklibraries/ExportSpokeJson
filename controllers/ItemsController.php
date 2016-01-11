<?php
/**
 * Export SPOKEdb JSON
 *
 * @copyright 2015 Michael Slone <m.slone@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @package Omeka\Plugins\ExportSpokeJson */

class ExportSpokeJson_ItemsController extends Omeka_Controller_AbstractActionController
{
    public function exportAction()
    {
        $itemId = $this->_getParam('id');
        $recursive = $this->_getParam('recursive');
        Zend_Registry::get('bootstrap')->getResource('jobs')->sendLongRunning(
            'ExportSpokeJson_Job_ExportItem', array(
                'itemId' => $itemId,
                'recursive' => $recursive,
            )
        );
        return $this->_helper->redirector->gotoRoute(
            array(
                'controller' => 'items',
                'action' => 'show',
                'id' => $itemId
            ),
            'default'
        );
    }

    public function unindexAction()
    {
        $itemId = $this->_getParam('id');
        $recursive = $this->_getParam('recursive');
        Zend_Registry::get('bootstrap')->getResource('jobs')->sendLongRunning(
            'ExportSpokeJson_Job_UnindexItem', array(
                'itemId' => $itemId,
                'recursive' => $recursive,
            )
        );
        return $this->_helper->redirector->gotoRoute(
            array(
                'controller' => 'items',
                'action' => 'show',
                'id' => $itemId,
            ),
            'default'
        );
    }
}
