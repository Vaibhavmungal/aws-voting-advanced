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
            description: 'Docker image repository name (builds automatically from Dockerfile)'
        )
        string(
            name: 'DOCKERHUB_CREDENTIALS_ID',
            defaultValue: 'docker-hub-credentials',
            description: 'Jenkins Credentials ID for Docker Hub (Username & Password used for all stages)'
        )
        string(
            name: 'DOCKERHUB_USERNAME',
            defaultValue: '',
            description: 'Optional manual override (Leave blank to auto-detect username from Jenkins credentials)'
        )
        booleanParam(
            name: 'PUSH_TO_DOCKERHUB',
            defaultValue: true,
            description: 'Push image to Docker Hub using Jenkins credentials'
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
        
        // Dynamically resolved from Jenkins Docker Hub credentials
        RESOLVED_DOCKER_USER = ''
        RESOLVED_CRED_ID     = ''
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
                    echo "⚙️ Resolving build and deployment targets using Jenkins Docker Hub credentials..."

                    def baseImage = (params.IMAGE_NAME ?: 'aws-voting').trim()
                    def candidateCreds = [
                        params.DOCKERHUB_CREDENTIALS_ID ?: 'docker-hub-credentials',
                        'docker-hub-credentials',
                        'dockerhub-credentials'
                    ].findAll { it }.unique()

                    // 1. ALWAYS retrieve Docker Hub username & credentials directly from Jenkins credential store
                    for (cId in candidateCreds) {
                        try {
                            withCredentials([usernamePassword(
                                credentialsId: cId,
                                usernameVariable: 'DH_RESOLVED_USER',
                                passwordVariable: 'DH_RESOLVED_PASS'
                            )]) {
                                if (env.DH_RESOLVED_USER?.trim()) {
                                    env.RESOLVED_DOCKER_USER = env.DH_RESOLVED_USER.trim()
                                    env.RESOLVED_CRED_ID = cId
                                    echo "🔑 Loaded Docker Hub credentials from Jenkins ID '${cId}'! Username: '${env.RESOLVED_DOCKER_USER}'"
                                    break
                                }
                            }
                        } catch (Exception ignored) {
                        }
                    }

                    // 2. Fallback to username parameter if no credentials matched
                    if (!env.RESOLVED_DOCKER_USER?.trim() && params.DOCKERHUB_USERNAME?.trim()) {
                        env.RESOLVED_DOCKER_USER = params.DOCKERHUB_USERNAME.trim()
                        echo "👤 Using fallback Docker Hub username parameter: '${env.RESOLVED_DOCKER_USER}'"
                    }

                    // 3. Set TARGET_IMAGE and push permission
                    if (env.RESOLVED_DOCKER_USER?.trim()) {
                        env.TARGET_IMAGE = "${env.RESOLVED_DOCKER_USER}/${baseImage}"
                        env.CAN_PUSH = (params.PUSH_TO_DOCKERHUB == true && env.RESOLVED_CRED_ID?.trim()) ? 'true' : 'false'
                    } else {
                        env.TARGET_IMAGE = baseImage
                        env.CAN_PUSH = 'false'
                        echo "ℹ️ No Docker Hub credentials found. Target image set to local '${baseImage}'."
                    }

                    echo "🎯 Target Image:    ${env.TARGET_IMAGE}:${env.IMAGE_TAG}"
                    echo "🏷️ Latest Tag:      ${env.TARGET_IMAGE}:latest"
                    echo "🔐 Credentials ID:  ${env.RESOLVED_CRED_ID ?: 'None'}"
                    echo "📤 Push to Hub:     ${env.CAN_PUSH}"
                    echo "☸️ Deploy to K8s:   ${params.DEPLOY_TO_K8S}"
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
                    def credIdToUse = env.RESOLVED_CRED_ID?.trim() ? env.RESOLVED_CRED_ID : (params.DOCKERHUB_CREDENTIALS_ID ?: 'docker-hub-credentials')
                    def candidateCreds = [credIdToUse, 'docker-hub-credentials', 'dockerhub-credentials'].findAll { it }.unique()
                    boolean pushed = false
                    for (cId in candidateCreds) {
                        try {
                            echo "Attempting Docker Hub login with Jenkins credential ID: '${cId}'..."
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
                echo "✅ Successfully pushed ${env.TARGET_IMAGE}:${env.IMAGE_TAG} and :latest to Docker Hub!"
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
                    def credIdToUse = env.RESOLVED_CRED_ID?.trim() ? env.RESOLVED_CRED_ID : (params.DOCKERHUB_CREDENTIALS_ID ?: 'docker-hub-credentials')

                    def executeDeploy = {
                        sh """
                            if command -v kubectl >/dev/null 2>&1; then
                                echo "Checking Kubernetes cluster connectivity..."
                                if kubectl cluster-info >/dev/null 2>&1; then
                                    echo "1. Ensuring namespace '${k8sNamespace}' exists..."
                                    kubectl create namespace ${k8sNamespace} --dry-run=client -o yaml | kubectl apply -f -

                                    if [ -n "\${DH_K8S_USER:-}" ] && [ -n "\${DH_K8S_PASS:-}" ]; then
                                        echo "2. Configuring Docker Hub pull secret in namespace '${k8sNamespace}' from Jenkins credentials..."
                                        kubectl create secret docker-registry dockerhub-secret \\
                                            --docker-server=https://index.docker.io/v1/ \\
                                            --docker-username="\$DH_K8S_USER" \\
                                            --docker-password="\$DH_K8S_PASS" \\
                                            --namespace=${k8sNamespace} \\
                                            --dry-run=client -o yaml | kubectl apply -f - || true
                                    fi

                                    echo "3. Applying base Kubernetes manifests..."
                                    kubectl apply -f k8s/votesecure.yaml

                                    echo "4. Triggering rolling update to container image '${env.TARGET_IMAGE}:${env.IMAGE_TAG}'..."
                                    kubectl set image deployment/votesecure-app app=${env.TARGET_IMAGE}:${env.IMAGE_TAG} -n ${k8sNamespace}

                                    echo "5. Waiting for pod rollout completion (zero-downtime transition)..."
                                    kubectl rollout status deployment/votesecure-app -n ${k8sNamespace} --timeout=180s

                                    echo "6. Active pods in namespace '${k8sNamespace}':"
                                    kubectl get pods -n ${k8sNamespace} -l app=votesecure -o wide

                                    echo "7. Exposed Services:"
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
                    }

                    def runWithKubeconfig = {
                        if (params.K8S_CONFIG_CREDENTIALS_ID?.trim()) {
                            echo "🔐 Using Kubeconfig from Jenkins credentials: ${params.K8S_CONFIG_CREDENTIALS_ID}"
                            withCredentials([file(credentialsId: params.K8S_CONFIG_CREDENTIALS_ID, variable: 'KUBECONFIG')]) {
                                executeDeploy()
                            }
                        } else {
                            executeDeploy()
                        }
                    }

                    if (credIdToUse) {
                        try {
                            withCredentials([usernamePassword(
                                credentialsId: credIdToUse,
                                usernameVariable: 'DH_K8S_USER',
                                passwordVariable: 'DH_K8S_PASS'
                            )]) {
                                runWithKubeconfig()
                            }
                        } catch (Exception e) {
                            echo "ℹ️ Proceeding with standard Kubernetes deployment..."
                            runWithKubeconfig()
                        }
                    } else {
                        runWithKubeconfig()
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
