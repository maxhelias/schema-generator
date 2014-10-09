<?php

/*
 * (c) Kévin Dunglas <dunglas@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace SchemaOrgModel\Config;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Types Generator Configuration
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
class TypesGeneratorConfiguration implements ConfigurationInterface
{
    const SCHEMA_ORG_RDFA_URL = 'https://raw.githubusercontent.com/rvguha/schemaorg/master/data/schema.rdfa';
    const GOOD_RELATIONS_OWL_URL = 'http://purl.org/goodrelations/v1.owl';

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('config');
        $rootNode
            ->children()
                ->arrayNode('rdfa')
                    ->info('RDFa files to use')
                    ->defaultValue([
                        self::SCHEMA_ORG_RDFA_URL,
                    ])
                    ->prototype('scalar')->end()
                ->end()
                ->arrayNode('relations')
                    ->info('OWL relation files to use')
                    ->defaultValue([
                        self::GOOD_RELATIONS_OWL_URL,
                    ])
                    ->prototype('scalar')->end()
                ->end()
                ->booleanNode('debug')->defaultFalse()->info('Debug mode')->end()
                ->booleanNode('useRte')->defaultFalse()->info('Use Doctrine\'s Resolve Target Entity feature')->end()
                ->booleanNode('checkIsGoodRelations')->defaultFalse()->info('Emit a warning if a property is not derived from GoodRelations')->end()
                ->scalarNode('header')->defaultFalse()->info('A license or any text to use as header of generated files')->example('// (c) Kévin Dunglas <dunglas@gmail.com>')->end()
                ->arrayNode('namespaces')
                    ->addDefaultsIfNotSet()
                    ->info('PHP namespaces')
                    ->children()
                        ->scalarNode('entity')->defaultValue('SchemaOrg\Entity')->info('The namespace of the generated entities')->example('Acme\Entity')->end()
                        ->scalarNode('enum')->defaultValue('SchemaOrg\Enum')->info('The namespace of the generated enumerations')->example('Acme\Enum')->end()
                        ->scalarNode('interface')->defaultValue('SchemaOrg\Model')->info('The namespace of the generated interfaces')->example('Acme\Model')->end()
                    ->end()
                ->end()
                ->scalarNode('author')->defaultFalse()->info('The value of the phpDoc\'s @author annotation')->example('Kévin Dunglas <dunglas@gmail.com>')->end()
                ->enumNode('fieldVisibility')->values(['private', 'protected', 'public'])->defaultValue('private')->cannotBeEmpty()->info('Visibility of entities fields')->end()
                ->arrayNode('types')
                    ->info('Schema.org\'s types to use')
                    ->useAttributeAsKey('id')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('namespace')->defaultNull()->info('The namespace for the generated class (override any other defined namespace)')->end()
                            ->arrayNode('doctrine')
                                ->addDefaultsIfNotSet()
                                ->children()
                                    ->scalarNode('inheritanceMapping')->defaultNull()->info('The Doctrine inheritance mapping type')->end()
                                ->end()
                            ->end()
                            ->scalarNode('parent')->defaultNull()->info('The parent class')->end()
                            ->arrayNode('properties')
                                ->info('Properties of this type to use')
                                ->useAttributeAsKey('id')
                                ->prototype('array')
                                    ->children()
                                        ->scalarNode('range')->defaultNull()->info('The property range')->example('Offer')->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('annotationGenerators')
                    ->info('Annotation generators to use')
                    ->defaultValue([
                        'SchemaOrgModel\AnnotationGenerator\PhpDocAnnotationGenerator',
                        'SchemaOrgModel\AnnotationGenerator\ConstraintAnnotationGenerator',
                        'SchemaOrgModel\AnnotationGenerator\DoctrineAnnotationGenerator',
                    ])
                    ->prototype('scalar')->end()
                ->end()
            ->end()
            ;

        return $treeBuilder;
    }
}