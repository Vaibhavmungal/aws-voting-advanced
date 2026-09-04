#!/usr/bin/env bash
# ==============================================================================
# VoteSecure — Production Deployment Script (AWS EC2 / VPS)
# ==============================================================================
set -euo pipefail

DEPLOY_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
COMPOSE_FILE="${DEPLOY_DIR}/docker-compose.prod.yml"
ENV_FILE="${DEPLOY_DIR}/.env"

echo "====================================================="
echo "🚀 Starting VoteSecure Deployment: $(date)"
echo "📁 Deployment Directory: ${DEPLOY_DIR}"
echo "====================================================="

# Check prerequisites
command -v docker >/dev/null 2>&1 || { echo "❌ Docker is not installed. Aborting." >&2; exit 1; }
docker compose version >/dev/null 2>&1 || { echo "❌ Docker Compose is not installed. Aborting." >&2; exit 1; }

# Verify .env exists
if [ ! -f "${ENV_FILE}" ]; then
    echo "⚠️  .env file not found in ${DEPLOY_DIR}!"
    if [ -f "${DEPLOY_DIR}/.env.example" ]; then
        echo "ℹ️  Copying .env.example to .env..."
        cp "${DEPLOY_DIR}/.env.example" "${ENV_FILE}"
    fi
fi

cd "${DEPLOY_DIR}"

# Pull latest Docker image from Docker Hub
echo "📥 Pulling latest images from Docker Hub..."
docker compose -f "${COMPOSE_FILE}" pull app

# Recreate and start updated containers
echo "🔄 Starting updated containers..."
docker compose -f "${COMPOSE_FILE}" up -d --remove-orphans

# Health Check verification
echo "⏳ Waiting for application health check..."
APP_PORT=$(grep -E '^APP_PORT=' "${ENV_FILE}" 2>/dev/null | cut -d '=' -f2 || echo "80")
[ -z "${APP_PORT}" ] && APP_PORT="80"

MAX_RETRIES=12
RETRY_COUNT=0
HEALTHY=0

while [ ${RETRY_COUNT} -lt ${MAX_RETRIES} ]; do
    RETRY_COUNT=$((RETRY_COUNT + 1))
    echo "   Checking http://localhost:${APP_PORT}/health.php (attempt ${RETRY_COUNT}/${MAX_RETRIES})..."
    HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" "http://localhost:${APP_PORT}/health.php" || echo "000")
    
    if [ "${HTTP_CODE}" = "200" ]; then
        HEALTHY=1
        echo "✅ Application health check passed! (HTTP 200)"
        break
    fi
    sleep 5
done

if [ ${HEALTHY} -ne 1 ]; then
    echo "⚠️  Health check did not respond with 200 within timeout."
    echo "📜 Recent container logs:"
    docker compose -f "${COMPOSE_FILE}" logs --tail 30 app
fi

# Cleanup old dangling images
echo "🧹 Cleaning up dangling images..."
docker image prune -f >/dev/null 2>&1 || true

echo "====================================================="
echo "🎉 Deployment completed successfully at $(date)"
echo "====================================================="
