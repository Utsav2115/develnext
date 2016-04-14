<?php
namespace php\gui\framework\behaviour\custom;

use ParseException;
use php\format\ProcessorException;
use php\io\IOException;
use php\io\Stream;
use php\lib\fs;
use php\lib\str;
use php\xml\DomDocument;
use php\xml\DomElement;
use php\xml\XmlProcessor;

/**
 * Class BehaviourLoader
 * @package behaviour\custom
 */
class BehaviourLoader
{
    protected static $documents = [];

    public static function loadOne($targetId, DomElement $behaviours, BehaviourManager $manager, $newTargetId = null)
    {
        $targets = $behaviours->findAll("./target[@id='$targetId']");

        /** @var DomElement $domTarget */
        foreach ($targets as $domTarget) {
            $targetId = $domTarget->getAttribute('id');

            /** @var DomElement $domBehaviour */
            foreach ($domTarget->findAll('./behaviour') as $domBehaviour) {
                $type = $domBehaviour->getAttribute('type');

                if (class_exists($type)) {
                    $attributes = $domBehaviour->getAttributes();
                    unset($attributes['type']);

                    foreach ($attributes as &$value) {
                        if ($value[0] == '[' && str::endsWith($value, ']')) {
                            $value = str::split(str::sub($value, 1, str::length($value) - 2), ',');
                        }
                    }

                    /** @var AbstractBehaviour $behaviour */
                    $behaviour = new $type();

                    $behaviour->setProperties($attributes);

                    $manager->apply($newTargetId === null ? $targetId : $newTargetId, $behaviour);
                }
            }
        }
    }

    public static function loadFromDocument(DomDocument $document, BehaviourManager $manager)
    {
        $targets = $document->findAll('/behaviours/target');

        /** @var DomElement $domTarget */
        foreach ($targets as $domTarget) {
            $targetId = $domTarget->getAttribute('id');

            /** @var DomElement $domBehaviour */
            foreach ($domTarget->findAll('./behaviour') as $domBehaviour) {
                $type = $domBehaviour->getAttribute('type');

                if (class_exists($type)) {
                    $attributes = $domBehaviour->getAttributes();
                    unset($attributes['type']);

                    foreach ($attributes as &$value) {
                        if ($value[0] == '[' && str::endsWith($value, ']')) {
                            $value = str::split(str::sub($value, 1, str::length($value) - 1), ',');
                        }
                    }

                    /** @var AbstractBehaviour $behaviour */
                    $behaviour = new $type();
                    $behaviour->setProperties($attributes);

                    $manager->apply($targetId, $behaviour);
                }
            }
        }
    }

    public static function load($file, BehaviourManager $manager, $cached = false)
    {
        try {
            if (!$cached || !($document = static::$documents[$file])) {
                $xml = new XmlProcessor();
                $content = Stream::getContents($file);

                if ($content) {
                    $document = $xml->parse($content);
                } else {
                    $document = new DomDocument();
                }

                static::$documents[$file] = $document;
            }

            static::loadFromDocument($document, $manager);

            return true;
        } catch (IOException $e) {
            if (fs::exists($file)) {
                return false;
            }

            return true;
        } catch (ProcessorException $e) {
            ;
        }

        return false;
    }
}