<?php
/*
 * This file is part of the sfWindguruPlugin package.
 * Tomasz Zdanowski <tomasz@mikran.pl>
 * 
 */

/**
 * sfWindguruBrowser extends a basic HTTP client. 
 * sfWebBrowser plugin must be installed
 *
 * @package    sfWindguruPlugin
 * @author     Tomasz Zdanowski <tomasz@mikran.pl>
 * @version    0.1
 */

class sfWindguruBrowser extends sfWebBrowser
{
  public function __construct($defaultHeaders = array(), $adapterClass = null, $adapterOptions = array())
  {
    parent::__construct($defaultHeaders = array(), $adapterClass = null, $adapterOptions = array());

    $this->cache = new sfFileCache(array('cache_dir' => sfConfig::get('sf_config_cache_dir')));
  }

  public function getWindguru($user_code,$spot_id,$spot_code, $parameters = array(), $headers = array(),$use_cache = true)
  {
    $this->key = 'sfWeatherPlugin_spot_'.sha1($spot_id);

    if(!$this->cache->has($this->key) && $use_cache == true)
      {
	$uri = "http://www.windguru.cz/int/distr2.php";
	
	$parameters['u'] = $user_code;
	$parameters['s'] = $spot_id;
	$parameters['c'] = $spot_code;
	
	$retval = parent::get($uri,$parameters,$headers);
	
	return $retval;
      }
  }

  public function getResponseText()
  {
    if($this->cache->has($this->key))
      {
	$retval = unserialize($this->cache->get($this->key));
      }
    else
      {
	$retval = parent::getResponseText();
	$this->cache->set($this->key, serialize($retval), 900); // 15 minutes of cache
      }

    return $retval;
  }
}