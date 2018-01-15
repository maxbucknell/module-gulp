<?php

namespace MaxBucknell\Prefab\Model;

use MaxBucknell\Prefab\Api\BuilderInterface;

class Builder implements BuilderInterface
{
    /**
     * @var array
     */
    private $builders;

    public function __construct(
        $builders = []
    ) {
        $this->builders = $builders;
    }

    public function build()
    {
        foreach ($this->builders as $builder) {
            /** @var BuilderInterface $builder */
            $builder->build();
        }
    }
}