<?php

function getRestsFromPage($type,$page)
{
    $subject = file_get_contents('https://restoran.kz/' . $type . '?page='.$page);

    $pattern = '/<div class="mb-5">/u';
    $blocks = preg_split($pattern,$subject);
    unset($blocks[0]);
    
    $rests = [];
    foreach ($blocks as $block) {
        
        $pattern = '/<a class="link-inherit-color" href="(\/.{1,}?\/.{1,}?)">(.{1,}?)<\/a>/u';
        $result = [];
        preg_match_all($pattern , $block , $result);
        
        $rest = [
                'name' => $result[2][0],
                'link' => $result[1][0],
        ];
       
        $pattern = '/<li class="d-flex mr-5 mb-3"><svg class="icon icon_md flex-none mr-3" aria-hidden="true"><use xlink:href="(.+?)"><\/use><\/svg>(.+?)<\/li>/u';
        $result = [];
        preg_match_all($pattern,$block,$result);
        
        
        $paramsMap = [
            '#icon-plate' => 'cuisine',
            '#icon-kz-tenge-in-circle' => 'price',
            '#icon-lightning-in-circle' => 'options',
        ];
    
        foreach ($paramsMap as $k => $v) {
            
            $index = array_search($k,$result[1]);
            if ($index !== false) {
                $rest[$v] = $result[2][$index];
            }
        }
    
        $rest['price'] = $rest['price'] ?? '';
        $pattern ='/[0-9]+/';
        $digits = [];
        $count = preg_match_all($pattern,$rest['price'],$digits);
        
        
        if ($count == 1) {
            $rest['price'] = [
                  'min' => $digits[0][0],
                  'max' => $digits[0][0],
            ];
        } elseif($count == 2) {
            $rest['price'] = [
                'min' => $digits[0][0],
                'max' => $digits[0][1],
          ];
        } else {
            $rest['price'] = [
                'min' => 0,
                'max' => 0,
          ];
        }
    
        $rest['cuisine'] =  preg_split('/, /',$rest['cuisine'] ?? '');
        
        $rest['category'] = $type;

        $rests[] =  $rest;
    
    }
    
      return $rests;
}

function getMaxPage($type,$page)
{   

    return 1;
    $subject = file_get_contents('https://restoran.kz/' .$type . '?page='.$page);
    $pattern = '/<a.+?href="\/' .$type. '\?page=([0-9]+)">[0-9]+<\/a>/u';
    $result = [];
    preg_match_all($pattern,$subject,$result);
    $maxPage = max($result[1]);
    if ($maxPage <= $page) {
        return $page;
    }else {  
        return getMaxPage($type,$maxPage);
    }
}