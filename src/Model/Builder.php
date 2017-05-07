<?php

namespace MaxBucknell\Gulp\Model;

use MaxBucknell\Gulp\Api\BuilderInterface;

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