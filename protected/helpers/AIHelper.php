<?php

class AIHelper
{
    const MANUSCRIPTS = 6;
    const PROTOCOLS = 5;
    const _3D_IMAGES = 7;
    const CODES = 4;
    const SOURCES = 3;

    public static function getTypeName($type)
    {
        switch ($type) {
            case AIHelper::MANUSCRIPTS:
                return 'manuscript';
            case AIHelper::PROTOCOLS:
                return 'protocol';
            case AIHelper::_3D_IMAGES:
                return '3d image';
            case AIHelper::CODES:
                return 'code';
            default:
                return 'source';
        }
    }

    public static function getRegExp($type)
    {
        switch ($type) {
            case self::MANUSCRIPTS:
                return '/^doi:[0-9]+\.[0-9]+\/gigascience\/[a-z]+[0-9]+$/i';
            case self::PROTOCOLS:
                return '/^doi:[0-9]+\.[0-9]+\/protocols\.io\.[a-z0-9]+$/i';
            case self::_3D_IMAGES:
                return '/^https:\/\/skfb\.ly\/[a-z0-9]+$/i';
            case self::CODES:
                return '/^<script[\s\S]*?>[\s\S]*?<\/script>$/i';
            default:
                return '/^doi:[0-9]+\.[0-9]+\/[0-9]+\.[0-9]+$/i';
        }
    }
}