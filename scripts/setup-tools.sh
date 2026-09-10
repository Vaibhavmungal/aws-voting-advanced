#!/usr/bin/env bash
# ==============================================================================
# VoteSecure — All-in-One Automated Tooling Setup for AWS EC2
# Installs: Docker, Jenkins, AWS CLI v2, kubectl, eksctl, Trivy, and PHP-CLI
# ==============================================================================
set -euo pipefail

echo "====================================================="
echo "🚀 Starting All-in-One Tool Installation: $(date)"
echo "====================================================="

export DEBIAN_FRONTEND=noninteractive

# 1. Update and Base Dependencies
echo "📦 Updating packages and installing prerequisites..."
sudo apt-get update -y
sudo apt-get install -y ca-certificates curl gnupg lsb-release apt-transport-https unzip git openjdk-17-jdk

# 2. Install Docker Engine & Compose Plugin
echo "🐳 Installing Docker & Docker Compose..."
sudo install -m 0755 -d /etc/apt/keyrings
curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo gpg --dearmor -o /etc/apt/keyrings/docker.gpg
sudo chmod a+r /etc/apt/keyrings/docker.gpg
echo "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.gpg] https://download.docker.com/linux/ubuntu $(. /etc/os-release && echo "$VERSION_CODENAME") stable" | sudo tee /etc/apt/sources.list.d/docker.list > /dev/null
sudo apt-get update -y
sudo apt-get install -y docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin

# 3. Install Jenkins LTS
echo "🏗️ Installing Jenkins LTS..."
curl -fsSL https://pkg.jenkins.io/debian-stable/jenkins.io-2023.key | sudo tee /usr/share/keyrings/jenkins-keyring.asc > /dev/null
echo "deb [signed-by=/usr/share/keyrings/jenkins-keyring.asc] https://pkg.jenkins.io/debian-stable binary/" | sudo tee /etc/apt/sources.list.d/jenkins.list > /dev/null
sudo apt-get update -y
sudo apt-get install -y jenkins

# Configure permissions for Docker
sudo usermod -aG docker ubuntu || true
sudo usermod -aG docker jenkins || true
sudo systemctl enable --now docker
sudo systemctl restart docker
sudo systemctl enable --now jenkins
sudo systemctl restart jenkins

# 4. Install AWS CLI v2
echo "☁️ Installing AWS CLI v2..."
curl -fsSL "https://awscli.amazonaws.com/awscli-exe-linux-x86_64.zip" -o "/tmp/awscliv2.zip"
unzip -q /tmp/awscliv2.zip -d /tmp
sudo /tmp/aws/install --update || true
rm -rf /tmp/aws /tmp/awscliv2.zip

# 5. Install kubectl
echo "☸️ Installing kubectl..."
curl -fsSL https://pkgs.k8s.io/core:/stable:/v1.29/deb/Release.key | sudo gpg --dearmor -o /etc/apt/keyrings/kubernetes-apt-keyring.gpg
sudo chmod 644 /etc/apt/keyrings/kubernetes-apt-keyring.gpg
echo 'deb [signed-by=/etc/apt/keyrings/kubernetes-apt-keyring.gpg] https://pkgs.k8s.io/core:/stable:/v1.29/deb/ /' | sudo tee /etc/apt/sources.list.d/kubernetes.list
sudo apt-get update -y
sudo apt-get install -y kubectl

# 6. Install eksctl
echo "🛠️ Installing eksctl..."
curl --silent --location "https://github.com/weaveworks/eksctl/releases/latest/download/eksctl_$(uname -s)_amd64.tar.gz" | tar xz -C /tmp
sudo mv /tmp/eksctl /usr/local/bin/

# 7. Install Trivy (Security Scanner)
echo "🛡️ Installing Trivy..."
curl -sfL https://raw.githubusercontent.com/aquasecurity/trivy/main/contrib/install.sh | sudo sh -s -- -b /usr/local/bin

# 8. Install PHP CLI (for Jenkinsfile 'php -l' lint stage)
echo "🐘 Installing PHP CLI & dependencies..."
sudo apt-get install -y php-cli php-xml php-mbstring php-zip php-gd php-mysql php-curl

echo "====================================================="
echo "✅ All tools installed successfully!"
echo "====================================================="
docker --version
jenkins --version
aws --version
kubectl version --client
eksctl version
trivy --version
php -v
echo "====================================================="
echo "🔑 Initial Jenkins Admin Password:"
sudo cat /var/lib/jenkins/secrets/initialAdminPassword || echo "Check /var/lib/jenkins/secrets/initialAdminPassword once Jenkins finishes first boot"
echo "====================================================="
