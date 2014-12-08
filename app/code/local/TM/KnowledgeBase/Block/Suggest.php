<?php
class TM_KnowledgeBase_Block_Suggest extends TM_KnowledgeBase_Block_Result
{
    protected $_suggestData = null;

    protected function _toHtml()
    {
        $html = '';

        if (!$this->_beforeToHtml()) {
            return $html;
        }

        $suggestData = $this->getSuggestData();
        if (!($count = count($suggestData))) {
            return $html;
        }

        $count--;
        $index = 0;
        $html  = '<ul><li style="display:none"></li>';
        foreach ($suggestData as $item) {

            $rowClass = $item['row_class'];
            if ($index == 0) {
                $rowClass .= ' first';
            }

            if ($index == $count) {
                $rowClass .= ' last';
            }
            $title = $this->htmlEscape($item->getTitle());
            $href  = $this->getArticleUrl($item->getIdentifier());
            
            $html .= '<li title="' . $title . '" class="' . $rowClass . '">'
//                  . '<span class="amount">' . $item['score'] . '</span>'
                  . $title
//                  . '<a href="' . $href. '">'. $item->getTitle() . '</a>'
                  . '</li>';
            $index++;
        }
        $html.= '</ul>';

        return $html;
    }

    public function getSuggestData()
    {
        if (!$this->_suggestData) {
            $collection = $this->getCollection();
            $counter = 0;
            $data = array();
            foreach ($collection as &$item) {
                $item->setRowClass((++$counter) % 2 ? 'odd' : 'even');
            }
            $this->_suggestData = $collection;
        }
        return $this->_suggestData;
    }
/*
 *
*/
}
