<?php

declare(strict_types=1);

namespace SimpleCQRS\UI\ParamConverter;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * Class Command.
 *
 * @Annotation
 */
class Command extends ParamConverter
{
    private $uuid;
    private $routeParams = [];
    private $only = [];
    private $exclude = [];

    public function setUuid($uuid)
    {
        $options = $this->getOptions();
        $options['uuid'] = $uuid;

        $this->setOptions($options);
    }

    public function setRouteParams($routeParams)
    {
        $options = $this->getOptions();
        $options['route_params'] = $routeParams;

        $this->setOptions($options);
    }

    public function setOnly($only)
    {
        $options = $this->getOptions();
        $options['only'] = $only;

        $this->setOptions($options);
    }

    public function setExclude($exclude)
    {
        $options = $this->getOptions();
        $options['exclude'] = $exclude;

        $this->setOptions($options);
    }
}
