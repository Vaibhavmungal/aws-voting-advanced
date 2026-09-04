pipeline {
    agent any

    environment {
        // ID of Jenkins Username with Password credential in Manage Jenkins > Credentials
        DOCKERHUB_CREDENTIALS_ID = 'dockerhub-credentials'
        
        // Target Docker Hub Repository (e.g. username/repo-name)
        DOCKERHUB_REPO           = 'vaibhavmungal/aws-voting'
        
        // Dynamic build tag based on Jenkins Build Number
        IMAGE_TAG                = "${BUILD_NUMBER}"
        APP_NAME                 = 'VoteSecure'
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
                echo "🐳 Building VoteSecure production Docker image..."
                sh """
                    docker build -t ${DOCKERHUB_REPO}:${IMAGE_TAG} -t ${DOCKERHUB_REPO}:latest .
                """
                echo "✅ Image built successfully: ${DOCKERHUB_REPO}:${IMAGE_TAG} and ${DOCKERHUB_REPO}:latest"
            }
        }

        stage('Security Scan (Trivy)') {
            steps {
                echo "🛡️ Running container vulnerability scan..."
                sh '''
                    if command -v trivy >/dev/null 2>&1; then
                        trivy image --severity HIGH,CRITICAL --no-progress ${DOCKERHUB_REPO}:${IMAGE_TAG} || true
                    else
                        echo "ℹ️  Trivy is not installed on this Jenkins agent. Skipping vulnerability scan."
                    fi
                '''
            }
        }

        stage('Push to Docker Hub') {
            steps {
                echo "📤 Authenticating and pushing images to Docker Hub..."
                withCredentials([usernamePassword(
                    credentialsId: "${DOCKERHUB_CREDENTIALS_ID}",
                    usernameVariable: 'DOCKERHUB_USER',
                    passwordVariable: 'DOCKERHUB_PASS'
                )]) {
                    sh '''
                        echo "$DOCKERHUB_PASS" | docker login -u "$DOCKERHUB_USER" --password-stdin
                        echo "Pushing tag: ${IMAGE_TAG}..."
                        docker push ${DOCKERHUB_REPO}:${IMAGE_TAG}
                        echo "Pushing tag: latest..."
                        docker push ${DOCKERHUB_REPO}:latest
                        docker logout
                    '''
                }
                echo "✅ Successfully pushed ${DOCKERHUB_REPO}:${IMAGE_TAG} and ${DOCKERHUB_REPO}:latest to Docker Hub!"
            }
        }

        stage('Deploy to Server (Optional)') {
            when {
                branch 'main'
                expression { env.DEPLOY_HOST != null && env.DEPLOY_HOST != '' }
            }
            steps {
                echo "🚀 Running production deployment..."
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
            echo "📦 Docker Hub Image: ${DOCKERHUB_REPO}:${IMAGE_TAG}"
            echo "==========================================================="
        }
        failure {
            echo "❌ Pipeline failed! Check the console output above for error logs."
        }
    }
}
