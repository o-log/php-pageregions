<?php

namespace OLOG\PageRegions;

class FilterFactory {
    static public function getFilter($filter_str){
        return new Filter($filter_str);
    }
}