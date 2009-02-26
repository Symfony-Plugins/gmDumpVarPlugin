<?php
/**
 *
 *
 * @author  Masamoto Miyata <miyata@able.ocn.ne.jp>
 * @create  2009/02/09
 * @copyright 2007 Sunrise Digital Corporation.
 * @version  v 1.0 2009/02/09 16:55:07 Miyata
 **/
class GmDebug
{
  /**
   * 
   * @param mixed $var
   * @param string $title
   * @param bool $echo
   * @return string html
   */
  public static function dump($var, $title = "", $echo = true)
  {
    $var = self::getBody($var);
  	$var =  self::decorateHtml($var, $title, $echo);
    if($echo)
    {
      echo $var;
    }
    return $var;
  }
  
  /**
   * 
   * @param mixed $var
   * @param string $title
   */
  public static function dumpEnd($var, $title = "")
  {
    self::dump($var, $title);
    die();
  }
  
  /**
   * 
   * @param mixed $var
   * @param string $title
   * @param string $filename
   */
  public static function dumpFile($var, $title = "", $filename = null)
  {
    $file = sfConfig::get('sf_gm_dump_var_plugin_file_dir', sfConfig::get('sf_log_dir'));
    
    if(!$filename)
    {
      $filename = sfConfig::get('sf_gm_dump_var_plugin_filename', 'var_dump.log');
    }
    
    $file .= (DIRECTORY_SEPARATOR.$filename);
    
    $fp = @fopen($file, "a+");
    if (!$fp)
    {
      throw new sfFileException('Can\'t write '.$file.'.');
    }
    else
    {
      $var = self::getBody($var);
      fwrite($fp, self::decorateText($var, $title, false));
      fclose($fp);
      chmod($file, 0666);
    }
  }
  
  /**
   * 
   * @param string $body
   * @param string $title
   * @param string $echo
   * @return string html
   */
  protected static function decorateHtml(&$body, $title, $echo)
  {
  	if(empty($title))
    {
      $title = '&nbsp';
    }
    
    $return = '<div id="gm_dump_var_outer_'.$title.'" class="gm_dump_var_outer" style="'.self::getStyleOuter().'">';
    $return .= '<p id="gm_dump_var_title_'.$title.'" class="gm_dump_var_title" style="'.self::getStyleTitle().'">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$title.' ['.self::getCurrentTime().']&nbsp;&nbsp;&nbsp;</p>';
    
    $title_att = $echo ? ' title="'.$title.'"' : '';
    $return .= '<pre class="gm_dump_var_pre" id="gm_dump_var_pre_'.$title.'"'.$title_att.' style="'.self::getStylePre().'">'.$body.'</pre>';
    
    $return .= '</div>';
    return $return;
  }
  
  /**
   * 
   * @param string $body
   * @param string $title
   * @param string $echo
   * @return string
   */
  protected static function decorateText(&$body, $title, $echo)
  {
    $return = '####['.self::getCurrentTime().'] '.$title.'####'.PHP_EOL;
    $return .= $body.PHP_EOL;
    $return .= PHP_EOL;
    return $return;
  }
  
  /**
   * 
   * @return string
   */
  protected static function getCurrentTime()
  {
    return date('Y-m-d G:i:s');
  }
  
  /**
   * 
   * @param mixed $var
   * @return string
   */
  protected static function getBody(&$var)
  {
    self::analyzeVar($var);
  	$pattern = array(
  	  '/\["gm_dump_var_func_name_([^"]+)"\]=>\n +array\([0-9]+\) {\n +\["param"\]=>\n +string\([0-9]+\) ""\n +\["return"\]=>\n +string\([0-9]+\) "([^"]+)"\n +}/',
  	  '/\["gm_dump_var_func_name_([^"]+)"\]=>\n +array\([0-9]+\) {\n +\["param"\]=>\n +string\([0-9]+\) ""\n +\["return"\]=>\n +([a-z]+\([^)]+\))\n +}/',
      '/\["gm_dump_var_func_name_([^"]+)"\]=>\n +array\([0-9]+\) {\n +\["param"\]=>\n +string\([0-9]+\) ""\n +}/',
  	  '/\["gm_dump_var_func_name_([^"]+)"\]=>\n +array\([0-9]+\) {\n +\["param"\]=>\n +string\([0-9]+\) "([^"]+)"\n +}/',
      '/\["gm_dump_var_func_list"\]=>\n +array\([0-9]+\) {/',
      '/array\(2\) {\n +\["gm_dump_var_class"\]=>\n +string\([0-9]+\) "([^"]+)"/'
    );
    $replace = array(
      '$1() $2',
      '$1() $2',
      '$1()',
      '$1( $2 )',
      'function {',
      'Object : class $1',
    );
    ob_start();
    var_dump($var);
    $output = ob_get_clean();
    
    return preg_replace($pattern, $replace, $output);
  }
  
  /**
   * 
   * @return string style
   */
  protected static function getStyleOuter()
  {
    if(sfConfig::get('sf_gm_dump_var_plugin_style_outer'))
    {
      return sfConfig::get('sf_gm_dump_var_plugin_style_outer');
    }
    return 
      'font-size:12px;'.
      'border:1px solid #E4E4E4;'.
      'margin:5px auto;'.
      'font-family:sans-serif;'
    ;
  }
  
  /**
   * 
   * @return string style
   */
  protected static function getStyleTitle()
  {
    if(sfConfig::get('sf_gm_dump_var_plugin_style_title'))
    {
      return sfConfig::get('sf_gm_dump_var_plugin_style_title');
    }
    
    return
      'font-size:12px;'.
      'padding:3px 10px 3px 0px;'.
      'background-color:#676767;'.
      'color:#FFFFFF;'.
      'font-weight:bold;'.
      'font-family:sans-serif;'.
      'background-image:url(/gmDumpVarPlugin/images/check-icon.png);'.
      'background-repeat: no-repeat;'.
      'background-position: 2px 2px;'.
      'margin: 0px;'
    ;
  }
  
  /**
   * 
   * @return string style
   */
  protected static function getStylePre()
  {
    if(sfConfig::get('sf_gm_dump_var_plugin_style_pre'))
    {
      return sfConfig::get('sf_gm_dump_var_plugin_style_pre');
    }
    
    return
      'font-size:12px;'.
      'padding:5px;'.
      'font-family:sans-serif;'.
      'background-color:#EFEFEF;'.
      'margin: 0px;'
    ;
  }
  
  /**
   * 
   * @param mixed &$var
   */
  protected static function analyzeVar(&$var)
  {
    if(is_array($var))
    {
      foreach($var as &$value)
      {
        self::analyzeVar($value);
      }
    }
    elseif(is_object($var))
    {
      $var = self::getObject($var);
    }
  }
  
  /**
   * 
   * @param object $var
   * @return array
   */
  protected static function getObject($var)
  {
    $class = new ReflectionClass(get_class($var));
    $methods = $class->getMethods();
    
    $return['gm_dump_var_class'] = get_class($var);
    foreach($methods as $obj)
    {
       if($obj->isPublic())
       {
         $function = $obj->getName();
         $parameters = $obj->getParameters();
         $return['gm_dump_var_func_list']['gm_dump_var_func_name_'.$function]['param'] = self::getFunctionParameter($parameters);
         $execute = sfContext::getInstance()->getRequest()->getParameter('gm_exec');
         if(!$execute)
         {
           continue;
         }
         $exec_pass = sfConfig::get('sf_gm_dump_var_plugin_exec_pass');
         if(!$exec_pass)
         {
           continue;
         }
         if($execute == $exec_pass && strpos($function,'get') === 0 && empty($parameters))
         {
           $return['gm_dump_var_func_list']['gm_dump_var_func_name_'.$function]['return'] = self::executeMethod($var, $function);
         }
       }
    }
    ksort($return['gm_dump_var_func_list']);
    return $return;
  }
  
  /**
   * 
   * @param object $object
   * @param string $function
   * @return mixed
   */
  protected static function executeMethod($object, $function)
  {
    try
    {
      $value = $object->$function();
    }
    catch(Exception $e)
    {
      return "    @throw ".get_class($e);
    }
    
    switch(true)
    {
      case is_object($value):
        $value = "Class:".get_class($value);
        break;
      case is_array($value):
        $value = PHP_EOL.preg_replace('/^/m','        ',var_export($value, true));
        break;
      case is_null($value):
        $value = "NULL";
        break;
      default:
        $value =  var_export($value, true);
        break;
    }
    
    return '    @return '.$value;
  }
  
  /**
   * 
   * @param array $parameters
   * @return string
   */
  protected static function getFunctionParameter(array &$parameters)
  {
    $return = "";
    foreach($parameters as $parameter)
    {
      $return .= '$'.$parameter->getName().', ';
    }
    return trim($return, ', ');
  }
}