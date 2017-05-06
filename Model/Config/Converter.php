<?php
namespace MaxBucknell\Gulp\Model\Config;


use Magento\Framework\Config\ConverterInterface;

class Converter implements ConverterInterface
{
    public function convert($source)
    {
        return [];
    }
}