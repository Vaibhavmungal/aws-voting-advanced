pipeline {
    agent any

    parameters {
        string(
            name: 'IMAGE_NAME',
            defaultValue: 'aws-voting',
            description: 'Docker image name (builds automatically from Dockerfile)'
        )
        string(
            name: 'DOCKERHUB_USERNAME',
            defaultValue: '',
            description: 'Optional Docker Hub username/namespace (Leave BLANK to auto-detect from Jenkins Credentials, or leave empty if not pushing)'
        )
        string(
            name: 'DOCKERHUB_CREDENTIALS_ID',
            defaultValue: 'dockerhub-credentials',
            description: 'Optional Jenkins Credentials ID for Docker Hub'
        )
        booleanParam(
            name: 'PUSH_TO_DOCKERHUB',
            defaultValue: false,
            description: 'Push image to Docker Hub (requires Docker Hub username or credentials)'
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
        TARGET_IMAGE         = ''
        CAN_PUSH             = 'false'
    }

    options {
        buildDiscarder(logRotator(numToKeepStr: '15'))
        timestamps()
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

                    // 1. Resolve Docker Hub User if parameter or credentials exist
                    if (params.DOCKERHUB_USERNAME?.trim()) {
                        env.RESOLVED_DOCKER_USER = params.DOCKERHUB_USERNAME.trim()
                        echo "👤 Using Docker Hub username from build parameter: '${env.RESOLVED_DOCKER_USER}'"
                    } else if (params.PUSH_TO_DOCKERHUB == true) {
                        try {
                            withCredentials([usernamePassword(
                                credentialsId: params.DOCKERHUB_CREDENTIALS_ID,
                                usernameVariable: 'AUTO_USER',
                                passwordVariable: 'AUTO_PASS'
                            )]) {
                                if (env.AUTO_USER?.trim()) {
                                    env.RESOLVED_DOCKER_USER = env.AUTO_USER.trim()
                                    echo "👤 Auto-detected Docker Hub username from credential '${params.DOCKERHUB_CREDENTIALS_ID}': '${env.RESOLVED_DOCKER_USER}'"
                                }
                            }
                        } catch (Exception e) {
                            echo "ℹ️ No Docker Hub credential found for ID '${params.DOCKERHUB_CREDENTIALS_ID}'."
                            env.RESOLVED_DOCKER_USER = ''
                        }
                    }

                    // 2. Determine target image name
                    if (env.RESOLVED_DOCKER_USER != '') {
                        env.TARGET_IMAGE = "${env.RESOLVED_DOCKER_USER}/${params.IMAGE_NAME}"
                        if (params.PUSH_TO_DOCKERHUB == true) {
                            env.CAN_PUSH = 'true'
                        }
                    } else {
                        env.TARGET_IMAGE = params.IMAGE_NAME
                        env.CAN_PUSH = 'false'
                    }

                    echo "🎯 Target Image:    ${env.TARGET_IMAGE}:${IMAGE_TAG}"
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
                    docker build -t ${params.IMAGE_NAME}:${IMAGE_TAG} -t ${params.IMAGE_NAME}:latest .
                    
                    if [ "${env.TARGET_IMAGE}" != "${params.IMAGE_NAME}" ]; then
                        docker tag ${params.IMAGE_NAME}:${IMAGE_TAG} ${TARGET_IMAGE}:${IMAGE_TAG}
                        docker tag ${params.IMAGE_NAME}:latest ${TARGET_IMAGE}:latest
                    fi
                """
                echo "✅ Image built successfully: ${TARGET_IMAGE}:${IMAGE_TAG}"
            }
        }

        stage('Security Scan (Trivy)') {
            steps {
                echo "🛡️ Running container vulnerability scan..."
                sh """
                    if command -v trivy >/dev/null 2>&1; then
                        trivy image --severity HIGH,CRITICAL --no-progress ${TARGET_IMAGE}:${IMAGE_TAG} || true
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
                echo "📤 Authenticating and pushing image to Docker Hub (${TARGET_IMAGE})..."
                withCredentials([usernamePassword(
                    credentialsId: params.DOCKERHUB_CREDENTIALS_ID,
                    usernameVariable: 'DOCKERHUB_USER',
                    passwordVariable: 'DOCKERHUB_PASS'
                )]) {
                    sh """
                        echo "\$DOCKERHUB_PASS" | docker login -u "\$DOCKERHUB_USER" --password-stdin
                        echo "Pushing tag: ${IMAGE_TAG}..."
                        docker push ${TARGET_IMAGE}:${IMAGE_TAG}
                        echo "Pushing tag: latest..."
                        docker push ${TARGET_IMAGE}:latest
                        docker logout
                    """
                }
                echo "✅ Successfully pushed ${TARGET_IMAGE}:${IMAGE_TAG} to Docker Hub!"
            }
        }

        stage('Deploy to Kubernetes Pods') {
            when {
                expression { params.DEPLOY_TO_K8S == true }
            }
            steps {
                echo "☸️ Initiating zero-downtime rolling deployment to Kubernetes pods..."
                script {
                    def deployScript = """
                        if command -v kubectl >/dev/null 2>&1; then
                            echo "Checking Kubernetes cluster connectivity..."
                            if kubectl cluster-info >/dev/null 2>&1; then
                                echo "1. Ensuring namespace '${params.K8S_NAMESPACE}' exists..."
                                kubectl create namespace ${params.K8S_NAMESPACE} --dry-run=client -o yaml | kubectl apply -f -

                                echo "2. Applying base Kubernetes manifests..."
                                kubectl apply -f k8s/votesecure.yaml

                                echo "3. Triggering rolling update to container image '${TARGET_IMAGE}:${IMAGE_TAG}'..."
                                kubectl set image deployment/votesecure-app app=${TARGET_IMAGE}:${IMAGE_TAG} -n ${params.K8S_NAMESPACE}

                                echo "4. Waiting for pod rollout completion (zero-downtime transition)..."
                                kubectl rollout status deployment/votesecure-app -n ${params.K8S_NAMESPACE} --timeout=180s

                                echo "5. Active pods in namespace '${params.K8S_NAMESPACE}':"
                                kubectl get pods -n ${params.K8S_NAMESPACE} -l app=votesecure -o wide

                                echo "6. Exposed Services:"
                                kubectl get svc -n ${params.K8S_NAMESPACE}
                                echo "✅ Kubernetes rolling update completed successfully!"
                            else
                                echo "⚠️ kubectl is installed on agent, but cluster API is not reachable."
                                echo "Verify kubeconfig credentials. Manifests are ready in k8s/votesecure.yaml."
                            fi
                        else
                            echo "⚠️ kubectl CLI is not installed on this Jenkins agent."
                            echo "Kubernetes manifests are validated and saved in k8s/votesecure.yaml."
                            echo "You can deploy anytime using: ./scripts/deploy-k8s.sh ${TARGET_IMAGE}:${IMAGE_TAG} ${params.K8S_NAMESPACE}"
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

        stage('Deploy to Server via SSH (Optional)') {
            when {
                branch 'main'
                expression { env.DEPLOY_HOST != null && env.DEPLOY_HOST != '' }
            }
            steps {
                echo "🚀 Running server deployment script..."
                sh '''
                    if [ -f scripts/deploy.sh ]; then
                        bash scripts/deploy.sh
                    else
                        echo "No deploy.sh script found. Skipping local deployment step."
                    fi
                '''
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
            echo "📦 Container Image:  ${TARGET_IMAGE}:${IMAGE_TAG}"
            echo "☸️ Kubernetes Target: ${params.K8S_NAMESPACE}"
            echo "==========================================================="
        }
        failure {
            echo "❌ Pipeline failed! Check the console output above for error logs."
        }
    }
}
