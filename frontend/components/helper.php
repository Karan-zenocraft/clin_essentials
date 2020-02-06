<?php

function p($obj, $f = 1)
{
    print "<pre>";
    print_r($obj);
    print "</pre>";
    if ($f == 1) {
        die;
    }

}
