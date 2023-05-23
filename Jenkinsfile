pipeline {
  agent any
  
  stages {
    stage('Build Backend') {
      steps {
        sh 'cd backend && ./build.sh'
      }
    }
    
    stage('Build Frontend') {
      steps {
        sh 'cd frontend/my-app && npm install'
        sh 'cd frontend/my-app && npm run build'
      }
    }
    
    stage('ESLint') {
      steps {
        sh 'cd frontend/my-app && npx eslint --fix'
      }
    }
    
    stage('Frontend Tests') {
      steps {
        sh 'cd frontend/my-app && npm test'
      }
    }
  }
}
