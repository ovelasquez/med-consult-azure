<?php
namespace AppBundle\Utils;

/**
 * Description of stringProcessing
 *
 * @author Mariana
 */
class StringProcessing {
    
    public function cleanUp($string,$tam=0) {
        
        $conservar = 'A-Za-z0-9'; // juego de caracteres a conservar
        $regex = sprintf('~[^%s]++~i', $conservar); // case insensitive
        $string = strtolower(preg_replace($regex, '_', $string));      
        if ($tam!==0 && strlen($string)>$tam):
            $string = substr ($string, 0,$tam);
        endif;                    
        
        return $string;
        
    }
}
