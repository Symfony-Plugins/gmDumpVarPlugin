<?php
/**
 *
 *
 * @author  Masamoto Miyata <miyata@able.ocn.ne.jp>
 * @create  2008/12/18
 * @copyright 2007 Sunrise Digital Corporation.
 * @version  v 1.0 2008/12/18 16:00:25 Miyata
 **/
class GmDumpAssignVarFilter extends sfFilter
{
  public function execute ($filterChain)
  {
    $need_display =
    (
      $this->getContext()->getConfiguration()->isDebug()
      &&
      'dev' == $this->getContext()->getConfiguration()->getEnvironment()
      &&
      !$this->getContext()->getRequest()->isXmlHttpRequest()
    );
    
    if($need_display)
    {
      $response = $this->getContext()->getResponse();
      $response->addJavascript(sfConfig::get('sf_prototype_web_dir').'/js/prototype');
      $response->addStylesheet('/gmDumpVarPlugin/css/gm_assign');
    }
    
    $filterChain->execute();
    
    if($need_display)
    {
      $action = $this->getContext()->getActionStack()->getLastEntry()->getActionInstance();
      $var = $action->getVarHolder()->getAll();
      
      $display = new GmDumpVars();
      foreach($var as $key=>&$value)
      {
        $display->addVar($value, $key);
      }
      
    
      $response->setContent(str_ireplace('</body>', $display->getHtml().'</body>',$response->getContent()));
    }
  }
  

}
