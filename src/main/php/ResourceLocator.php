<?php

namespace SUBHH\VuFind\SolrMarc;

final class ResourceLocator
{
    public static function getResourceDirectory () : string
    {
        return __DIR__ . '/../resources';
    }
}
