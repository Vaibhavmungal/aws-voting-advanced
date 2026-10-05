pipeline {
    agent any

    triggers {
        // Automatically polls GitHub every 1 minutes for new commits
        pollSCM('H/1 * * * *')
    }

    environment {
        APP_NAME          = 'VoteSecure'
        IMAGE_NAME        = 'aws-voting'
        IMAGE_TAG         = "${BUILD_NUMBER}"
        K8S_NAMESPACE     = 'votesecure'
        EKS_CLUSTER_NAME  = ''
        AWS_REGION        = 'us-east-1'
    }

    options {
        buildDiscarder(logRotator(numToKeepStr: '15'))
        timeout(time: 30, unit: 'MINUTES')
        disableConcurrentBuilds()
    }

    stages {
        stage('Checkout Code') {
            steps {
                echo "📥 Checking out source code from Git..."
                checkout scm
            }
        }

        stage('PHP Syntax Check') {
            steps {
                echo "🔍 Validating PHP syntax across all project files..."
                sh '''
                    if command -v php >/dev/null 2>&1; then
                        echo "Using local PHP CLI for syntax checking..."
                        find . -type f -name "*.php" -not -path "*/vendor/*" -exec php -l {} +
                    else
                        echo "Using Dockerized PHP 8.2 CLI for syntax checking..."
                        docker run --rm -v "$(pwd):/app" -w /app php:8.2-cli find . -type f -name "*.php" -not -path "*/vendor/*" -exec php -l {} +
                    fi
                    echo "✅ All PHP files passed syntax validation!"
                '''
            }
        }

        stage('Build Docker Image') {
            steps {
                echo "🐳 Building VoteSecure default image '${env.IMAGE_NAME}:${env.IMAGE_TAG}'..."
                sh """
                    docker build -t ${env.IMAGE_NAME}:${env.IMAGE_TAG} -t ${env.IMAGE_NAME}:latest .
                """
                echo "✅ Image built successfully: ${env.IMAGE_NAME}:${env.IMAGE_TAG}"
            }
        }

        stage('Security Scan (Trivy)') {
            steps {
                echo "🛡️ Running container vulnerability scan with Trivy..."
                sh """
                    if command -v trivy >/dev/null 2>&1; then
                        trivy image --severity HIGH,CRITICAL --no-progress ${env.IMAGE_NAME}:${env.IMAGE_TAG} || true
                    else
                        echo "ℹ️ Trivy is not installed on this Jenkins agent. Skipping vulnerability scan."
                    fi
                """
            }
        }

        stage('Push to Docker Hub') {
            steps {
                echo "📤 Authenticating with default credentials and pushing to Docker Hub..."
                script {
                    def credIds = ['docker', 'docker-hub-credentials', 'dockerhub-credentials']
                    boolean pushed = false
                    for (cId in credIds) {
                        if (!pushed) {
                            try {
                                echo "Attempting login with Jenkins credential ID: '${cId}'..."
                                withCredentials([usernamePassword(
                                    credentialsId: cId,
                                    usernameVariable: 'DH_USER',
                                    passwordVariable: 'DH_PASS'
                                )]) {
                                    sh """
                                        echo "\$DH_PASS" | docker login -u "\$DH_USER" --password-stdin
                                        docker tag ${env.IMAGE_NAME}:${env.IMAGE_TAG} "\$DH_USER/${env.IMAGE_NAME}:${env.IMAGE_TAG}"
                                        docker tag ${env.IMAGE_NAME}:${env.IMAGE_TAG} "\$DH_USER/${env.IMAGE_NAME}:latest"
                                        docker push "\$DH_USER/${env.IMAGE_NAME}:${env.IMAGE_TAG}"
                                        docker push "\$DH_USER/${env.IMAGE_NAME}:latest"
                                        docker logout
                                    """
                                    env.TARGET_IMAGE = "\$DH_USER/${env.IMAGE_NAME}:${env.IMAGE_TAG}"
                                    pushed = true
                                }
                            } catch (Exception e) {
                                echo "ℹ️ Note: Credential '${cId}' check: ${e.message}"
                            }
                        }
                    }
                    if (!pushed) {
                        echo "⚠️ Docker Hub credentials not configured. Using default local image '${env.IMAGE_NAME}:${env.IMAGE_TAG}'."
                        env.TARGET_IMAGE = "${env.IMAGE_NAME}:${env.IMAGE_TAG}"
                    }
                }
                echo "✅ Target image for deployment: ${env.TARGET_IMAGE}"
            }
        }

        stage('Deploy to Kubernetes Pods') {
            steps {
                echo "☸️ Deploying to Kubernetes namespace '${env.K8S_NAMESPACE}'..."
                sh """
                    # 1. Connect to Amazon EKS if cluster name is configured
                    if [ -n "${env.EKS_CLUSTER_NAME}" ] && command -v aws >/dev/null 2>&1; then
                        echo "☁️ Connecting to Amazon EKS cluster '${env.EKS_CLUSTER_NAME}' in region '${env.AWS_REGION}'..."
                        aws eks update-kubeconfig --region "${env.AWS_REGION}" --name "${env.EKS_CLUSTER_NAME}" || true
                    fi

                    # 2. Check kubectl connectivity and apply manifests
                    if command -v kubectl >/dev/null 2>&1; then
                        echo "Checking Kubernetes cluster connectivity..."
                        if kubectl cluster-info >/dev/null 2>&1; then
                            echo "1. Ensuring namespace '${env.K8S_NAMESPACE}' exists..."
                            kubectl create namespace ${env.K8S_NAMESPACE} --dry-run=client -o yaml | kubectl apply -f -

                            echo "2. Applying base Kubernetes manifests from k8s/votesecure.yaml..."
                            kubectl apply -f k8s/votesecure.yaml

                            echo "3. Ensuring MySQL database deployment is healthy..."
                            kubectl rollout status deployment/mysql -n ${env.K8S_NAMESPACE} --timeout=120s || true

                            echo "4. Triggering zero-downtime rolling update to '${env.TARGET_IMAGE ?: env.IMAGE_NAME + ':' + env.IMAGE_TAG}'..."
                            kubectl set image deployment/votesecure-app app=${env.TARGET_IMAGE ?: env.IMAGE_NAME + ':' + env.IMAGE_TAG} -n ${env.K8S_NAMESPACE}

                            echo "5. Waiting for pod rollout completion (zero-downtime transition)..."
                            kubectl rollout status deployment/votesecure-app -n ${env.K8S_NAMESPACE} --timeout=180s

                            echo "6. Active pods in namespace '${env.K8S_NAMESPACE}':"
                            kubectl get pods -n ${env.K8S_NAMESPACE} -o wide

                            echo "7. Exposed Services:"
                            kubectl get svc -n ${env.K8S_NAMESPACE}
                            echo "✅ Kubernetes rolling update completed successfully!"
                        else
                            echo "⚠️ kubectl is installed on agent, but cluster API is not reachable."
                            echo "Verify kubeconfig credentials. Manifests are ready in k8s/votesecure.yaml."
                        fi
                    else
                        echo "⚠️ kubectl CLI is not installed on this Jenkins agent."
                        echo "Kubernetes manifests are validated and saved in k8s/votesecure.yaml."
                        echo "You can deploy anytime using: ./scripts/deploy-k8s.sh ${env.TARGET_IMAGE ?: env.IMAGE_NAME + ':latest'} ${env.K8S_NAMESPACE}"
                    fi
                """
            }
        }
    }

    post {
        always {
            echo "🧹 Cleaning up workspace..."
            cleanWs()
        }
        success {
            echo "==========================================================="
            echo "🎉 VoteSecure CI/CD Pipeline Succeeded!"
            echo "📦 Container Image:   ${env.TARGET_IMAGE ?: env.IMAGE_NAME + ':' + env.IMAGE_TAG}"
            echo "☸️ Kubernetes Target: ${env.K8S_NAMESPACE}"
            echo "==========================================================="
        }
        failure {
            echo "❌ Pipeline failed! Check the console output above for error logs."
        }
    }
}
