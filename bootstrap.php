<?php
require_once 'app/Mage.php';
umask(0);
Mage::app();
Mage::getSingleton('core/session', array('name' => 'frontend'));