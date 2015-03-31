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
        #$item = $this->_helper->db->findById($itemId, 'Item');
        #$item = $this->_helper->db->findById();
        Zend_Registry::get('bootstrap')->getResource('jobs')->sendLongRunning(
            'ExportSpokeJson_Job_ExportItem', array(
                'itemId' => $itemId,
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
}
