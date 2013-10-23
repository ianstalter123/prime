<?php
/**
 *
 */
class  Newslog_Tools_Watchdog_Ping implements Interfaces_Observer {

    /**
     * @param Newslog_Models_Model_News $newsItem
     */
    public function notify($newsItem) {
        try {
            Newslog_Tools_Pinger::getInstance()->ping($newsItem);
        } catch (Exceptions_SeotoasterPluginException $spe) {
            if(Tools_System_Tools::debugMode()) {
                error_log($spe->getMessage());
            }
        }
    }
}
