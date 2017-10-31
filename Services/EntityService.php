<?php

namespace Zurd2\SmallCrudBundle\Services;

use Doctrine\Common\Annotations\AnnotationReader;

class EntityService
{
    /**
     * Return the properties of the entity
     *
     * @param $entityName
     * @return array
     */
    public function getProperties($entityName)
    {
        $properties = array();
        $docReader = new AnnotationReader();
        $entityReflection = new \ReflectionClass($this->getPath($entityName));

        $entityReflectionProperties = $entityReflection->getProperties();

        foreach ($entityReflectionProperties as $property) {
            $type = null;

            if ($entityReflection->hasProperty($property->getName())) {
                $docInfos = $docReader->getPropertyAnnotations($entityReflection->getProperty($property->getName()));

                if (isset($docInfos[0]) && is_a($docInfos[0], 'Doctrine\ORM\Mapping\Column')) {
                    $type = $docInfos[0]->type;
                } else if (isset($docInfos[1])){
                    $type = 'entity';
                }
            }

            $properties[] = array(
                'type' => $type,
                'name' => $property->getName(),
            );
        }

        return $properties;
    }

    /**
     * Return the complete route of class
     *
     * @param $entityName: name of entity
     * @param null $subDir
     * @param null $bundle
     * @return mixed
     */
    public function getPath($entityName, $subDir = null, $bundle = null)
    {
        $bundle = ($bundle != null)? $bundle : 'BackendBundle';
        $subDir = ($subDir != null)? $subDir : 'Entity';

        $path = str_replace('<bundle>', $bundle, '<bundle>\<entity>\<entity_name>');
        $path = str_replace('<entity>', $subDir, $path);
        $path = str_replace('<entity_name>', $entityName, $path);

        return $path;
    }
}