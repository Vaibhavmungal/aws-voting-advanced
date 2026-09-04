#!/bin/bash
# ==============================================================================
# VoteSecure — Cloud-Init Bootstrap Script for AWS EC2
# ==============================================================================
set -euo pipefail
exec > >(tee -a /var/log/votesecure_bootstrap.log) 2>&1

echo "====================================================="
echo "🚀 Bootstrapping VoteSecure at: $(date)"
echo "====================================================="

export DEBIAN_FRONTEND=noninteractive

# Update system packages
apt-get update -y
apt-get install -y \
    ca-certificates \
    curl \
    gnupg \
    lsb-release \
    git \
    docker.io \
    docker-compose-v2

# Start and enable Docker service
systemctl enable --now docker
usermod -aG docker ubuntu

# Discover EC2 Public IP via IMDSv2
TOKEN=$$(curl -s -X PUT "http://169.254.169.254/latest/api/token" -H "X-aws-ec2-metadata-token-ttl-seconds: 60" || true)
if [ -n "$$TOKEN" ]; then
    PUBLIC_IP=$$(curl -s -H "X-aws-ec2-metadata-token: $$TOKEN" http://169.254.169.254/latest/meta-data/public-ipv4 || echo "localhost")
else
    PUBLIC_IP="localhost"
fi

# Prepare application directory
APP_DIR="/opt/aws-voting-advanced"
mkdir -p "$$APP_DIR/uploads"
cd "$$APP_DIR"

# Write production .env
cat <<EOF > "$$APP_DIR/.env"
DB_HOST=${db_host}
DB_PORT=${db_port}
DB_USER=${db_user}
DB_PASS=${db_pass}
DB_NAME=${db_name}
APP_NAME=VoteSecure
APP_URL=http://$${PUBLIC_IP}
APP_PORT=80
ALLOWED_EMAIL_DOMAIN=all
DOCKERHUB_IMAGE=${docker_image}
IMAGE_TAG=${image_tag}
EOF

# Fetch deployment files from repository
echo "📥 Cloning VoteSecure repository..."
git clone https://github.com/Vaibhavmungal/aws-voting-advanced.git /tmp/repo-clone || true
if [ -d "/tmp/repo-clone" ]; then
    cp /tmp/repo-clone/docker-compose.prod.yml "$$APP_DIR/"
    mkdir -p "$$APP_DIR/scripts"
    cp /tmp/repo-clone/scripts/deploy.sh "$$APP_DIR/scripts/"
    chmod +x "$$APP_DIR/scripts/deploy.sh"
    rm -rf /tmp/repo-clone
fi

# Set proper ownership for www-data inside container
chown -R ubuntu:ubuntu "$$APP_DIR"
chmod -R 775 "$$APP_DIR/uploads"

# Start application containers
echo "🚀 Starting VoteSecure production stack..."
docker compose -f "$$APP_DIR/docker-compose.prod.yml" pull || true
docker compose -f "$$APP_DIR/docker-compose.prod.yml" up -d

echo "====================================================="
echo "✅ VoteSecure bootstrap completed successfully: $(date)"
echo "🌐 App is accessible at: http://$${PUBLIC_IP}"
echo "====================================================="
