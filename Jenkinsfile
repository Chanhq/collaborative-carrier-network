pipeline {
  agent any
  
  stages {
    stage('Build Backend') {
      steps {
        sh 'cd backend && ./build.sh'
      }
    }
    
    stage('PHPStan') {
      steps {
        sh 'cd backend && phpstan analyze src'
      }
    }
    
    stage('CodeSniffer') {
      steps {
        sh 'cd backend && phpcs src'
      }
    }
    
    stage('Tests') {
      steps {
        sh 'cd backend && ./vendor/bin/phpunit'
      }
    }
    
    stage('Linter') {
      steps {
        sh 'cd backend && ./vendor/bin/phpcs --standard=PSR2 src/'
      }
    }
    
    stage('Build Frontend') {
      steps {
        sh 'cd frontend && npm install'
        sh 'cd frontend && npm run build'
      }
    }
    
    stage('ESLint') {
      steps {
        sh 'cd frontend && npx eslint .'
      }
    }
    
    stage('Frontend Tests') {
      steps {
        sh 'cd frontend && npm test'
      }
    }
  }
}
