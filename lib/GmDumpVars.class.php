<?php
/**
 *
 *
 * @author  Masamoto Miyata <miyata@able.ocn.ne.jp>
 * @create  2008/12/18
 * @copyright 2007 Sunrise Digital Corporation.
 * @version  v 1.0 2008/12/18 18:32:23 Miyata
 **/
use_helper('Javascript');

class GmDumpVars
{
  protected
    $vars = array();
    
  public function addVar(&$vars, $key)
  {
    $this->vars[$key] = &$vars;
  }
  
  public function getHtml()
  {
    $return = '';
    foreach($this->vars as $key=>&$var)
    {
      $return .= GmDebug::dump($var, $key, false);
    }
    $return = $this->decorateHtml($return);
    $return .= $this->getJavascript();
    
    return $return;
  }
  
  protected function decorateHtml(&$body)
  {
    $return = '<hr noshade="noshade" color="#AAAAAA" size="1" id="gm_dump_var_separator_hr" />';
    $return .= '<div id="gm_dump_var_wrapper">';
    $return .= '<p id="gm_dump_var_open_all">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;OPEN ALL</p>';
    $return .= $body;
    $return .= '</div>';
    return $return;
  }
  
  protected function getJavascript()
  {
    return ' '.javascript_tag
    ("
      function gmDisplayVarInit()
      {
        var plus_icon = new Image();
        plus_icon.src = '/gmDumpVarPlugin/images/plus-icon.png';
        
        var minus_icon = new Image();
        minus_icon.src = '/gmDumpVarPlugin/images/minus-icon.png';
        
        var list = $$('p.gm_dump_var_title');
        list.each
        (
          function(title_dom)
          {
            title_dom.style.cursor = 'pointer';
            
            var id = title_dom.id;
            var target_id = id.replace('gm_dump_var_title_', 'gm_dump_var_pre_');
            
            title_dom.target_pre = $(target_id);
            
            if(!title_dom.target_pre.title)
            {
              gmClosePre(title_dom);
            }
            else
            {
              gmOpenPre(title_dom);
            }
            
            Event.observe
            (
              title_dom,
              'click',
              function(e)
              {
                var clicked_dom = Event.element(e);
                switch(clicked_dom.target_pre.style.display)
                {
                  case 'none':
                    gmOpenPre(clicked_dom);
                    break;
                  case 'block':
                    gmClosePre(clicked_dom);
                    break;
                }
              }
            );
          }
        );
        
        var all_button = $('gm_dump_var_open_all');
        all_button.current_status = 'close';
        Event.observe(
          all_button,
          'click',
          function(e)
          {
            list.each
            (
              function(title_dom)
              {
                gmOpenPre(title_dom);
              }
            );
          }
        );
      }
      
      function gmOpenPre(dom)
      {
        dom.target_pre.style.display = 'block';
        dom.style.backgroundImage = 'url(\'/gmDumpVarPlugin/images/minus-icon.png\')';
      }
      
      function gmClosePre(dom)
      {
        dom.target_pre.style.display = 'none';
        dom.style.backgroundImage = 'url(\'/gmDumpVarPlugin/images/plus-icon.png\')';
      }
      
      gmDisplayVarInit();
    ");
  }
}