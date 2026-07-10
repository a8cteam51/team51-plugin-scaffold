#!/bin/bash
# wp-env's "wordpress" service image doesn't ship pdo_mysql; install it and reload
# Apache so PHP picks up the new extension.
#
# Scoped to this project's own container via the compose service label plus the
# current directory's basename (wp-env derives its project/container names from the
# cwd) — a bare `docker ps` grep for a container-name substring can match an
# unrelated wp-env stack running on the same host, or match nothing at all when
# testsEnvironment is off (the default) and no "tests-*" containers exist. This
# project itself runs multiple concurrent wp-env configs (default/tests/belowfloor)
# whose container names all share the same basename prefix, so `--last 1` picks the
# one this invocation just started rather than an older sibling stack.
set -euo pipefail

CONTAINER_ID="$(docker ps --filter "label=com.docker.compose.service=wordpress" --filter "name=$(basename "$PWD")" --last 1 --format '{{.ID}}')"

if [ -z "$CONTAINER_ID" ]; then
	echo "wp-env-install-pdo_mysql.sh: no running wordpress container found for $(basename "$PWD")" >&2
	exit 1
fi

docker exec "$CONTAINER_ID" docker-php-ext-install pdo_mysql
docker exec "$CONTAINER_ID" service apache2 reload
