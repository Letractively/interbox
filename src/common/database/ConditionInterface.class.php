<?php

/**
 * 
 * @version 0.1.20110313
 * @author Zhiji Gu <gu_zhiji@163.com>
 * @copyright &copy; 2010-2011 InterBox Core 1.1.4 for PHP, GuZhiji Studio
 * @package interbox.core1.common.database
 */
interface ConditionInterface {
    public function AddCondition($c, $l=IBC1_LOGICAL_AND);

    public function ClearConditions();

    public function ConditionCount();
}

?>
