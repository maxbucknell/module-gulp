/**
 * Lodash. Obviously.
 */
const _ = require('lodash');

/**
 * Stream processing.
 */
const through = require('through2');

/**
 * Virtual file creation
 */
const vinylFile = require('vinyl-file');

const path = require('path');

module.exports = findLessFiles;

/**
 * Find css declarations inside layout files.
 *
 * Given a stream of layout files, find all css declarations
 * and emit a stream of less files.
 */
function findLessFiles ({ build_dir }) {
    const cssPattern = /<css\s+src="([^"]+)\.css"/;
    const removePattern = /<remove\s+src="([^"]+)\.css"/;

    const sources = {};
    const removals = {};


    function process (file, encoding, cb) {
        if (file.isNull()) {
            return cb();
        }

        findCssDeclarations(sources, removals, file);

        cb();
    }

    function finish (cb) {
        const files = [];
        function push (f) {
            "use strict";
            files.push(f);
        }
        const promises = _.map(sources, function (v, source) {
            "use strict";
            if (removals[source]) {
                return;
            }

            const filename = path.join(
                build_dir,
                'flatten',
                `${source.replace('::', '/')}.less`
            );

            return vinylFile.read(filename)
                .then(push.bind(this))
                .catch(_.noop);
        }.bind(this));

        return Promise.all(promises)
            .then(
                function () {
                    "use strict";
                    _.each(files, (file) => this.push(file));
                    cb();
                }.bind(this)
            ).catch((e) => console.log(e));
    }

    function findCssDeclarations (sources, removals, file) {
        const contents = file.contents.toString();
        const matches = contents.match(new RegExp(cssPattern, 'g'));
        const removeMatches = contents.match(new RegExp(removePattern, 'g'));

        matches && matches.map(_.partial(extractSource, cssPattern)).forEach(pushSource);
        removeMatches && removeMatches.map(_.partial(extractSource, removePattern)).forEach(pushRemoval);

        function extractSource (pattern, match) {
            return match.match(pattern)[1];
        }

        function pushSource (source) {
            sources[source] = true;
        }

        function pushRemoval (source) {
            removals[source] = true;
        }
    }

    return through.obj(process, finish);
};
