<?php

class Yireo_RalOption_Observer_AddLayoutHandle
{
    /**
     * Listen to the event controller_action_layout_load_before
     *
     * @param Varien_Event_Observer $observer
     *
     * @return $this
     */
    public function execute($observer)
    {
        $action = $observer->getEvent()->getAction();

        if (!$action instanceof Mage_Adminhtml_Sales_Order_CreateController) {
            return $this;
        }

        $observer->getEvent()->getLayout()->getUpdate()
            ->addHandle('raloption');

        return $this;
    }
}