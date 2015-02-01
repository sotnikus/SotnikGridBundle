<?php
namespace Sotnik\GridBundle\Grid;

use Doctrine\ORM\Query;

class GridHelper
{
    /**
     * @param $queryMapper
     * @param $hydrationMode
     * @return callable
     * @throws \InvalidArgumentException
     */
    public static function getQueryMapperClosure($queryMapper, $hydrationMode)
    {
        if (!in_array($hydrationMode, [Query::HYDRATE_OBJECT || Query::HYDRATE_ARRAY])) {
            throw new \InvalidArgumentException('Invalid hydration mode. Should be HYDRATE_OBJECT or HYDRATE_ARRAY');
        }

        if ($queryMapper instanceof \Closure) {
            $result = $queryMapper;
        } else {
            $parts = explode('.', $queryMapper);

            $result = function ($row) use ($parts, $hydrationMode) {
                $result = $row;
                foreach ($parts as $part) {
                    if ($hydrationMode == Query::HYDRATE_OBJECT) {
                        $getter = 'get' . ucfirst($part);
                        $result = $result->$getter();
                    } else {
                        $result = $result[$part];
                    }

                    if (empty($result)) {
                        return null;
                    }

                }

                return $result;
            };
        }

        return $result;
    }
}
