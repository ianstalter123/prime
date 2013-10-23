<?php

class Newslog_Tools_Misc {

    public static function getGplusProfile($newsItem = null) {
        if($newsItem) {
            $user             = Application_Model_Mappers_UserMapper::getInstance()->find($newsItem->getUserId());
            if($user instanceof Application_Model_Models_User) {
                $userGplusProfile = $user->getGplusProfile();
            }
        }

        if(isset($userGplusProfile) && $userGplusProfile) {
            return array(
                'name' => 'Goolge+',
                'url'  => $userGplusProfile
            );
        } else {
            $newsConfig = Newslog_Models_Mapper_ConfigurationMapper::getInstance()->fetchConfigParams();
            if(isset($newsConfig['gplusProfile']) && $newsConfig['gplusProfile']) {
                return array(
                    'name' => 'Goolge+',
                    'url'  => $newsConfig['gplusProfile']
                );
            }
        }
        return null;
    }

}