<?php
/**
 * This is part of the solution for the custom form field type 'entity_hidden'.
 * From here: https://gist.github.com/bjo3rnf/4061232
 */
namespace Sideclick\CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Sideclick\CoreBundle\Form\DataTransformer\EntityToIdTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\Common\Persistence\ObjectManager;

class EntityHiddenType extends AbstractType
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformer = new EntityToIdTransformer($this->objectManager, $options['class']);
        $builder->addModelTransformer($transformer);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
                'class' => null,
                'invalid_message' => 'The entity does not exist.',
            ));
    }

    public function getParent()
    {
        return 'hidden';
    }

    public function getName()
    {
        return 'entity_hidden';
    }
}