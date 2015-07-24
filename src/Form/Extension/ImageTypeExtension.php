<?php
// This class implemented as per: http://symfony.com/doc/current/cookbook/form/create_form_type_extension.html#override-the-file-widget-template-fragment
namespace Sc\CoreBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * This Extension allows us to enhance File Form field types to display
 * thumbnails for image file fields.  Note tha this class is applied
 * to all File input fields, so it should probably actually be called
 * FileTypeExtension
 */
class ImageTypeExtension extends AbstractTypeExtension
{
    /**
     * Returns the name of the type being extended.
     *
     * @return string The name of the type being extended
     */
    public function getExtendedType()
    {
        return 'file';
    }
    
    /**
     * Add the image_path option
     *
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setOptional(array('image_attribute'));
    }

    /**
     * Pass the image URL to the view
     *
     * @param FormView $view
     * @param FormInterface $form
     * @param array $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        // by default we will not want to display an image thumbnail for this
        // file field
        $view->vars['display_image'] = false;
        
        // if the image_attribute parameter has been sent through (It should
        // represent a file attribute on the entity)
        if (array_key_exists('image_attribute', $options)) {
            $parentData = $form->getParent()->getData();
            
            // send the data we need to the view (to see these in action, check out
            // ScCoreBundle:Form:fields.html.twig for our overridden file_widget
            $view->vars['image_attribute'] = $options['image_attribute'];
            $view->vars['parent_data'] = $parentData;
            
            // we will want to display a thumbnail if we have an entity
            // and the entity has a getter for the attribute and the getter
            // returns a value (theoretically an image object!)
            // @todo 'getImage' needs to be replaced with a variable that is sent through that defines the function name
            // NOTE: I don't think we even use this any more?
            $view->vars['display_image'] = null !== $parentData && call_user_func_array(array($parentData, 'getImage'), array());
            
        }
    }
}