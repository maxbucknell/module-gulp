<?php

namespace MaxBucknell\Gulp\Model\Generator;


class Gulpfile
{
    public function generate($config = [])
    {
        $tasks = $this->generateTasks($config);

        return <<<JS
const gulp = require('gulp');
const minimist = require('minimist');

const magento = JSON.parse(minimist(
    process.argv.slice(1),
    {
        string: ['magento'],
        default: {
            magento: '{}'
        }
    }
).magento);

{$tasks}
JS;

    }

    /**
     * @param $config
     * @return string
     */
    public function generateTasks($config)
    {
        $taskScripts = \array_map(
            function ($data, $taskName) {
                $subtasks = $this->formatSubtasks($data['subtasks']);
                $sourceInclude = $this->formatSourceInclude($data['source']);

                return <<<JS
gulp.task('{$taskName}', {$subtasks}, function () {
    return {$sourceInclude};
});
JS;
            },
            array_values($config),
            array_keys($config)
        );

        return \implode("\n\n", $taskScripts);
    }

    public function formatSubtasks($subtasks)
    {
        return \implode(
            \implode(
                ', ',
                \array_map(
                    function ($subtask) {
                        return \implode(
                            $subtask,
                            ['\'', '\'']
                        );
                    },
                    $subtasks
                )
            ),
            ['[', ']']
        );
    }

    public function formatSourceInclude($source)
    {
        if (\is_null($source)) {
            return 'true';
        } else {
            return "require('{$source}')(magento)";
        }
    }
}