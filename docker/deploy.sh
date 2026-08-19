#!/bin/bash
# Deploys whatever is currently on GitHub's `master` to the Hostinger VPS.
# Does NOT touch local git state - commit/push separately, whenever you want.
#
# The VPS's git "origin" remote is Bitbucket and has been frozen on an
# old commit since 2016 - it is NOT used to ship code anymore. Instead,
# the VPS repo has a "github" remote (authenticated via a read-only
# deploy key) that this script pulls from. The app container bind-mounts
# the repo root, so a plain git update on the VPS is enough to go live;
# no image rebuild is needed unless the Dockerfile itself changed.
set -euo pipefail

REMOTE_HOST="${PRAGTICO_DEPLOY_HOST:-179.198.119.160}"
REMOTE_USER="${PRAGTICO_DEPLOY_USER:-mradosta}"
REMOTE_PATH="${PRAGTICO_DEPLOY_PATH:-/home/mradosta/pragtico}"
BRANCH="master"
RESTART_CONTAINER=0
BUILD_CONTAINER=0

for arg in "$@"; do
    case "$arg" in
        --restart) RESTART_CONTAINER=1 ;;
        --build) BUILD_CONTAINER=1 ;;
        *) echo "Unknown option: $arg" >&2; exit 1 ;;
    esac
done

echo "==> Deploying on $REMOTE_HOST"
ssh "${REMOTE_USER}@${REMOTE_HOST}" bash -s -- "$REMOTE_PATH" "$BRANCH" "$RESTART_CONTAINER" "$BUILD_CONTAINER" <<'REMOTE_SCRIPT'
set -euo pipefail
REMOTE_PATH="$1"
BRANCH="$2"
RESTART_CONTAINER="$3"
BUILD_CONTAINER="$4"

cd "$REMOTE_PATH"
git fetch github
git checkout "$BRANCH"
git merge --ff-only "github/$BRANCH"

echo "==> Now running: $(git log -1 --format='%h %s (%ci)')"

if [[ "$BUILD_CONTAINER" == "1" ]]; then
    echo "==> Rebuilding and restarting container (Dockerfile changed)"
    (cd docker && docker compose up -d --build app)
elif [[ "$RESTART_CONTAINER" == "1" ]]; then
    echo "==> Restarting container"
    (cd docker && docker compose restart app)
fi
REMOTE_SCRIPT

echo "==> Done"
