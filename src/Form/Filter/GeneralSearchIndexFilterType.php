<?php

namespace Sc\CoreBundle\Form\Filter;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


use Lexik\Bundle\FormFilterBundle\Filter\FilterOperands;

/**
 * This is a generic search filter class which implements a text base search
 * filter on a 'search_index' field.  This Filter form can be used with any
 * QueryBuilder on an Entity that has a search_index field.
 */
class GeneralSearchIndexFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('search_index', 'filter_text',
            array(
                // Making this filter perform a like '%term%' search
                'condition_pattern' => FilterOperands::STRING_BOTH,
                
                'label' => 'Search'
            )
        );
    }

    public function getName()
    {
        return 'sc_core_form_filter_general_search_index_filter';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection'   => false,
            'validation_groups' => array('filtering') // avoid NotBlank() constraint-related message
        ));
    }
}
