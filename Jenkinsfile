pipeline {
  agent any
  stages {
    stage('Build Backend') {
      when {
        branch 'main'
      }
      steps {
        sh 'php composer.phar install'
        sh './vendor/bin/phpstan analyze src'
        sh './vendor/bin/phpcs --standard=PSR2 src'
        sh './vendor/bin/phpunit tests'
        sh 'php vendor/bin/parallel-lint src tests'
      }
    }
    stage('Run Backend Tests') {
      when {
        branch 'main'
      }
      steps {
        sh 'php vendor/bin/phpunit tests'
      }
    }
    stage('Backend Linter') {
      when {
        branch 'main'
      }
      steps {
        sh 'php vendor/bin/parallel-lint src tests'
      }
    }
    stage('Build Frontend') {
      steps {
        sh 'npm install'
        sh 'npm run build'
        sh 'npm run eslint'
        sh 'npm run test'
      }
    }
    stage('Run Frontend Tests') {
      steps {
        sh 'npm run test'
      }
    }
    stage('Frontend Linter') {
      steps {
        sh 'npm run eslint'
      }
    }
  }
}