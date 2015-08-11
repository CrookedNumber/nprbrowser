<?php

class nprbrowser {

  function __construct() {
    
  }

}

class NPRStory {
  function __construct($query_result) {
    $this->version = $query_result->version;
    $this->href = $query_result->href;
    $this->attributes = clone $query_result->attributes;
    $this->links = clone $query_result->links;
    $this->guid = $this->attributes->guid;
    $this->profile_href = $this->links->profile[0]->href;
    $pieces = explode('/', $this->profile_href);
    $this->profile = array_pop($pieces);
    // make this better ^^^^^^^^^^^^
  }

  function hasEnclosure() {
    return !empty($this->links->enclosure);
  }

  function getHREF() {
    if ($this->hasEnclosure()) {
      return $this->links->enclosure[0]->href;
    }
  }
  
  function getGUID() {
    return $this->attributes->guid;
  }
}

class NPRCall {

  function __construct($url, $key, $cache = TRUE) {
    $this->query = '';
    $this->results = [];
    $this->base = $url;
    $this->key = $key;
    $this->errors = [];

    // Cache
    $this->from_cache = FALSE;
    $this->caching = $cache;
  }

  function pull($options, $method = 'query') {
    //http://api.npr.org/query?id=1149,58
    $params = [
      'output' => 'json',
      'apiKey' => $this->key,
      'id' => [],
    ];

    foreach ($options as $label => $option) {
      $use_id = [
        'topic',
        'program',
        'blog',
        'bio',
        'genre',
        'artist',
        'column',
        'series',
      ];
      if (in_array($label, $use_id)) {
        $key = 'id';    
      }
      else if ($label == 'station') {
        $key = 'orgId';
      }
      else if ($label == 'text') {
        $key ='searchTerm';
      }
      else {
        $key = $label;
      }
      if (is_array($option)) {
        $params[$key] = array_merge($params[$key], $option);
      }
      elseif ($option) {
        $params[$key][] = $option;
      }
    }
    
    $query_string = '';
    $qs = [];
    
    foreach ($params as $key => $param) {
      if (is_array($param) && !empty($param)) {
        if (count($param) > 1) {
          $qs[] = "$key=" . implode(',', $param);
        }
        else {
          $qs[] = "$key=" . array_shift($param);
        }
      }
      elseif (is_string($param) && $param) {
        $qs[] = "$key=" . urlencode($param);
      }
    }
    
    if (!empty($qs)) {
      $query_string = '?' . implode('&', $qs);  
    }

    $url = $this->base . '/' . $method . $query_string;
    $this->query = $url;
    
    $key = $this->cacheKey($qs);
    $cache = $this->cacheGet($key);
    if ($cache) {
      $json = json_decode($cache);
    }
    else {
      $raw = file_get_contents($url);
      $this->cacheSet($key, $raw);
      $json = json_decode($raw);
    }

    if (isset($json->item) && !isset($json->subcategory)) {
      $this->results = $json->item;
    }
    else if (isset($json->subcategory)) { // freakin' bios list API
      foreach ($json->subcategory as $sub) {
        if (isset($sub->item)) {
          foreach ($sub->item as $i) {
            $this->results[] = $i;
          }
        }
      }  
    }
    else if (!isset($json->list) && $method == 'v2/stations') { // probably station
      $this->results = $json;
    }
    else {
      $this->results = $json->list->story;
    }

  }

  function cacheSet($key, $value, $lifetime = 300) {
    if ($this->caching) {
      // If/when this method is overridden
      // Save your cache row here
      // $key, $value, $this->cache_bin, $lifetime + time()
      // return TRUE/FALSE, if cache save was a success or not
      // default = dirt-simple file cache
      $expires = time() . $lifetime;
      $filename = 'cache/' . $key . '.txt';
      return file_put_contents($filename, $expires . "\n" . serialize($value));
    }
  }

  function cacheGet($key) {
    if ($this->caching) {
      // If/when this method is overridden
      // grab row from cache where key==$key
      // make sure it hasn't expired
      // return $cache->value;
      // default = dirt-simple file cache
      $filename = 'cache/' . $key . '.txt';
      if (file_exists($filename) && $file = file_get_contents($filename)) {
        $pieces = explode("\n", $file);
        $expires = array_shift($pieces);
        $value = unserialize(implode("\n", $pieces));
        $now = time();
        if ($expires && $value && $now < $expires) {
          return $value;
        }
        if ($expires >= $now) {
          unlink($filename);
        }
      }
    }
  }

  function cacheKey($values) {
    // sample key generator (not great; not terrible)
    ksort($values);
    return 'nprbrowser|' . md5(implode('|', $values));
  }

}
