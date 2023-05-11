pipeline {
    agent any
    stages {
        stage('Build Backend') {
            steps {
                sh 'composer install' // build app
                sh 'vendor/bin/phpstan analyse src/' // run PHPStan
                sh 'vendor/bin/phpcs --standard=PSR2 src/' // run cs-checker
                sh 'vendor/bin/phpunit' // run tests
                sh 'vendor/bin/phpcs --standard=PSR2 src/' // run linter
            }
        }
        stage('Build Frontend') {
            steps {
                sh 'npm install' // install dependencies
                sh 'npm run build' // build app
                sh 'npm run eslint' // run eslint
                sh 'npm run test' // run tests
            }
        }
    }
}