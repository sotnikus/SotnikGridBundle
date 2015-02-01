<?php
namespace Sotnik\GridBundle\Twig;

class SotnikGridExtension extends \Twig_Extension
{

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return array(
            'sotnik_test' => new \Twig_Function_Method($this, 'getTest', array('is_safe' => array('html'))),
        );
    }

    public function getTest()
    {

    }

    public function getName()
    {
        return 'sotnik_grid_extension';
    }
}
