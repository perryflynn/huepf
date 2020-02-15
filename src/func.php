<?php

function cleanAlias($alias)
{
    $clean = preg_replace('/[^A-Za-z0-9\-\_]/', '_', $alias);
    while(strpos($clean, "__") !== false)
    {
        $clean = str_replace("__", "_", $clean);
    }
    
    $clean = trim($clean, "_");
    
    if (empty($clean))
    {
        return null;
    }
    
    return $clean;
}

function cleanUrl($url)
{
    $temp = trim($url);
    
    if (preg_match('/^http(s)?:\/\//', $temp) !== 1)
    {
        return null;
    }
  
    $temp = preg_replace('/["<>]/', "", $temp);    
    return $temp;
}
