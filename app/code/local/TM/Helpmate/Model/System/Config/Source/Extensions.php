<?php

class TM_Helpmate_Model_System_Config_Source_Extensions
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $array = array(
            "txt", "csv", "xml", "css","doc","xls","rtf","ppt", "pdf",
            "flv","avi", "wmv","mov",
            "jpg","jpeg","gif","png",
            'zip', 'rar', 'tar.gz'
        );
        $return = array();
        foreach ($array as $item) {
            $return[] = array(
                'value' => $item,
                'label' => ucfirst($item)
            );
        }
        return $return;
    }

}
