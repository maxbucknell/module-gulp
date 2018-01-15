<?php
namespace MaxBucknell\Prefab\Model\Config;

use Magento\Framework\Config\ConverterInterface;

class Converter implements ConverterInterface
{
    /**
     * @param \DOMDocument $source
     * @return array
     */
    public function convert($source)
    {
        $xpath = new \DOMXPath($source);
        $result = [];

        $result['scripts'] = $this->collectScripts($xpath);
        $result['dependencies'] = $this->collectDependencies($xpath);

        return $result;
    }

    public function collectScripts(\DOMXPath $xpath)
    {
        $result = [];

        foreach ($xpath->query('task') as $taskNode) {
            /** @var \DOMElement $taskNode */

            $taskName = $taskNode->getAttribute('name');
            $command = null;

            foreach ($taskNode->childNodes as $childNode) {
                /** @var \DOMNode $childNode */
                switch ($childNode->nodeName) {
                    case 'command':
                        $command = $childNode->textContent;
                    case 'dependencies':
                    case '#text':
                    case '#comment':
                        break;
                    default:
                        throw new \Exception('Bad XML ' . $childNode->nodeName);
                        break;
                }
            }

            $result[$taskName] = $command;
        }

        return $result;
    }

    public function collectDependencies(\DOMXPath $xpath)
    {
        $result = [];

        foreach ($xpath->query('task/dependencies/package') as $package) {
            /** @var \DOMElement $package */
            $name = $package->getAttribute('name');
            $version = $package->getAttribute('version');

            $result[$name] = $version;
        }

        return $result;
    }
}