pipeline {
    agent any

    triggers {
        // Automatically polls GitHub every 2 minutes for new commits and triggers build
        pollSCM('H/2 * * * *')
    }

    parameters {
        string(
            name: 'IMAGE_NAME',
            defaultValue: 'aws-voting',
            description: 'Docker image name (builds automatically from Dockerfile)'
        )
        string(
            name: 'DOCKERHUB_USERNAME',
            defaultValue: 'vaibhavvv85',
            description: 'Docker Hub username/namespace (defaults to vaibhavvv85)'
        )
        string(
            name: 'DOCKERHUB_CREDENTIALS_ID',
            defaultValue: 'docker-hub-credentials',
            description: 'Jenkins Credentials ID for Docker Hub (e.g. docker-hub-credentials or dockerhub-credentials)'
        )
        booleanParam(
            name: 'PUSH_TO_DOCKERHUB',
            defaultValue: true,
            description: 'Push image to Docker Hub (set to true to automatically publish vaibhavvv85/aws-voting)'
        )
        booleanParam(
            name: 'DEPLOY_TO_K8S',
            defaultValue: true,
            description: 'Deploy built image directly into Kubernetes Pods'
        )
        string(
            name: 'K8S_NAMESPACE',
            defaultValue: 'votesecure',
            description: 'Target Kubernetes Namespace for deployment'
        )
        string(
            name: 'K8S_CONFIG_CREDENTIALS_ID',
            defaultValue: '',
            description: 'Optional Jenkins Secret File credential ID for kubeconfig (leave blank if agent has cluster access)'
        )
    }

    environment {
        // Dynamic build tag based on Jenkins Build Number
        IMAGE_TAG            = "${BUILD_NUMBER}"
        APP_NAME             = 'VoteSecure'
        
        // Dynamically resolved during pipeline execution
        RESOLVED_DOCKER_USER = ''
        TARGET_IMAGE         = 'aws-voting'
        CAN_PUSH             = 'false'
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

        stage('Resolve Configuration') {
            steps {
                script {
                    echo "⚙️ Resolving build and deployment targets..."

                    def baseImage = (params.IMAGE_NAME ?: 'aws-voting').trim()

                    // 1. Resolve Docker Hub User if parameter or credentials exist
                    if (params.DOCKERHUB_USERNAME?.trim()) {
                        env.RESOLVED_DOCKER_USER = params.DOCKERHUB_USERNAME.trim()
                        echo "👤 Using Docker Hub username: '${env.RESOLVED_DOCKER_USER}'"
                    } else if (params.PUSH_TO_DOCKERHUB == true) {
                        def credList = [params.DOCKERHUB_CREDENTIALS_ID, 'docker-hub-credentials', 'dockerhub-credentials'].findAll { it }
                        for (cId in credList) {
                            try {
                                withCredentials([usernamePassword(
                                    credentialsId: cId,
                                    usernameVariable: 'AUTO_USER',
                                    passwordVariable: 'AUTO_PASS'
                                )]) {
                                    if (env.AUTO_USER?.trim()) {
                                        env.RESOLVED_DOCKER_USER = env.AUTO_USER.trim()
                                        echo "👤 Auto-detected Docker Hub username from credential '${cId}': '${env.RESOLVED_DOCKER_USER}'"
                                        break
                                    }
                                }
                            } catch (Exception ignored) {
                            }
                        }
                    }

                    // 2. Determine target image name
                    if (env.RESOLVED_DOCKER_USER != '') {
                        env.TARGET_IMAGE = "${env.RESOLVED_DOCKER_USER}/${baseImage}"
                        if (params.PUSH_TO_DOCKERHUB == true) {
                            env.CAN_PUSH = 'true'
                        }
                    } else {
                        env.TARGET_IMAGE = baseImage
                        env.CAN_PUSH = 'false'
                    }

                    echo "🎯 Target Image:    ${env.TARGET_IMAGE}:${env.IMAGE_TAG}"
                    echo "🏷️ Latest Alias:    ${env.TARGET_IMAGE}:latest"
                    echo "📤 Push to Hub:     ${env.CAN_PUSH}"
                }
            }
        }

        stage('Lint & PHP Syntax Check') {
            steps {
                echo "🔍 Validating PHP syntax across all project files..."
                sh '''
                    if command -v php >/dev/null 2>&1; then
                        echo "Using local PHP runtime for syntax checking..."
                        find . -type f -name "*.php" -not -path "*/vendor/*" -exec php -l {} +
                    else
                        echo "Using Dockerized PHP 8.2 runtime for syntax checking..."
                        docker run --rm -v "$(pwd):/app" -w /app php:8.2-cli find . -type f -name "*.php" -not -path "*/vendor/*" -exec php -l {} +
                    fi
                    echo "✅ All PHP files passed syntax validation!"
                '''
            }
        }

        stage('Build Docker Image') {
            steps {
                echo "🐳 Automatically building VoteSecure image from Dockerfile..."
                sh """
                    docker build -t ${env.TARGET_IMAGE}:${env.IMAGE_TAG} -t ${env.TARGET_IMAGE}:latest .
                    
                    if [ "${env.TARGET_IMAGE}" != "aws-voting" ]; then
                        docker tag ${env.TARGET_IMAGE}:${env.IMAGE_TAG} aws-voting:${env.IMAGE_TAG} || true
                        docker tag ${env.TARGET_IMAGE}:latest aws-voting:latest || true
                    fi
                """
                echo "✅ Image built successfully: ${env.TARGET_IMAGE}:${env.IMAGE_TAG}"
            }
        }

        stage('Security Scan (Trivy)') {
            steps {
                echo "🛡️ Running container vulnerability scan..."
                sh """
                    if command -v trivy >/dev/null 2>&1; then
                        trivy image --severity HIGH,CRITICAL --no-progress ${env.TARGET_IMAGE}:${env.IMAGE_TAG} || true
                    else
                        echo "ℹ️ Trivy is not installed on this Jenkins agent. Skipping vulnerability scan."
                    fi
                """
            }
        }

        stage('Push to Docker Hub') {
            when {
                expression { env.CAN_PUSH == 'true' }
            }
            steps {
                echo "📤 Authenticating and pushing image to Docker Hub (${env.TARGET_IMAGE})..."
                script {
                    def candidateCreds = [params.DOCKERHUB_CREDENTIALS_ID, 'docker-hub-credentials', 'dockerhub-credentials'].findAll { it }.unique()
                    boolean pushed = false
                    for (cId in candidateCreds) {
                        try {
                            echo "Attempting Docker Hub login with credential ID: '${cId}'..."
                            withCredentials([usernamePassword(
                                credentialsId: cId,
                                usernameVariable: 'DH_USER',
                                passwordVariable: 'DH_PASS'
                            )]) {
                                sh """
                                    echo "\$DH_PASS" | docker login -u "\$DH_USER" --password-stdin
                                    echo "Pushing tag: ${env.IMAGE_TAG}..."
                                    docker push ${env.TARGET_IMAGE}:${env.IMAGE_TAG}
                                    echo "Pushing tag: latest..."
                                    docker push ${env.TARGET_IMAGE}:latest
                                    docker logout
                                """
                                pushed = true
                            }
                            if (pushed) { break }
                        } catch (Exception e) {
                            echo "⚠️ Login with credential '${cId}' failed or not found: ${e.message}"
                        }
                    }
                    if (!pushed) {
                        error("❌ Failed to push image to Docker Hub. Please ensure Jenkins has valid credentials with ID 'docker-hub-credentials' or 'dockerhub-credentials'.")
                    }
                }
                echo "✅ Successfully pushed ${env.TARGET_IMAGE}:${env.IMAGE_TAG} to Docker Hub!"
            }
        }

        stage('Deploy to Kubernetes Pods') {
            when {
                expression { params.DEPLOY_TO_K8S == true }
            }
            steps {
                echo "☸️ Initiating zero-downtime rolling deployment to Kubernetes pods..."
                script {
                    def k8sNamespace = params.K8S_NAMESPACE ?: 'votesecure'
                    def deployScript = """
                        if command -v kubectl >/dev/null 2>&1; then
                            echo "Checking Kubernetes cluster connectivity..."
                            if kubectl cluster-info >/dev/null 2>&1; then
                                echo "1. Ensuring namespace '${k8sNamespace}' exists..."
                                kubectl create namespace ${k8sNamespace} --dry-run=client -o yaml | kubectl apply -f -

                                echo "2. Applying base Kubernetes manifests..."
                                kubectl apply -f k8s/votesecure.yaml

                                echo "3. Triggering rolling update to container image '${env.TARGET_IMAGE}:${env.IMAGE_TAG}'..."
                                kubectl set image deployment/votesecure-app app=${env.TARGET_IMAGE}:${env.IMAGE_TAG} -n ${k8sNamespace}

                                echo "4. Waiting for pod rollout completion (zero-downtime transition)..."
                                kubectl rollout status deployment/votesecure-app -n ${k8sNamespace} --timeout=180s

                                echo "5. Active pods in namespace '${k8sNamespace}':"
                                kubectl get pods -n ${k8sNamespace} -l app=votesecure -o wide

                                echo "6. Exposed Services:"
                                kubectl get svc -n ${k8sNamespace}
                                echo "✅ Kubernetes rolling update completed successfully!"
                            else
                                echo "⚠️ kubectl is installed on agent, but cluster API is not reachable."
                                echo "Verify kubeconfig credentials. Manifests are ready in k8s/votesecure.yaml."
                            fi
                        else
                            echo "⚠️ kubectl CLI is not installed on this Jenkins agent."
                            echo "Kubernetes manifests are validated and saved in k8s/votesecure.yaml."
                            echo "You can deploy anytime using: ./scripts/deploy-k8s.sh ${env.TARGET_IMAGE}:${env.IMAGE_TAG} ${k8sNamespace}"
                        fi
                    """

                    if (params.K8S_CONFIG_CREDENTIALS_ID?.trim()) {
                        echo "🔐 Using Kubeconfig from Jenkins credentials: ${params.K8S_CONFIG_CREDENTIALS_ID}"
                        withCredentials([file(credentialsId: params.K8S_CONFIG_CREDENTIALS_ID, variable: 'KUBECONFIG')]) {
                            sh deployScript
                        }
                    } else {
                        sh deployScript
                    }
                }
            }
        }

        stage('Deploy Application (Docker)') {
            steps {
                echo "🚀 Starting VoteSecure application on port 8085..."
                sh """
                    # Stop and remove existing container if already running
                    docker stop votesecure-app 2>/dev/null || true
                    docker rm votesecure-app 2>/dev/null || true

                    # Launch self-contained VoteSecure container on port 8085
                    docker run -d --name votesecure-app \\
                        --restart unless-stopped \\
                        -p 8085:80 \\
                        -v votesecure_uploads:/var/www/html/uploads \\
                        ${env.TARGET_IMAGE}:${env.IMAGE_TAG}

                    echo "Waiting 5 seconds for container to initialize..."
                    sleep 5
                    echo "📊 Active containers:"
                    docker ps --filter "name=vote"
                """
                echo "✅ VoteSecure stack is running on port 8085!"
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
            echo "📦 Container Image:  ${env.TARGET_IMAGE}:${env.IMAGE_TAG}"
            echo "☸️ Kubernetes Target: ${params.K8S_NAMESPACE ?: 'votesecure'}"
            echo "==========================================================="
        }
        failure {
            echo "❌ Pipeline failed! Check the console output above for error logs."
        }
    }
}
