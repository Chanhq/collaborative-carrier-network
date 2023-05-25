pipeline {
  agent any
      triggers {
              gitlab(triggerOnPush: true, triggerOnMergeRequest: true, branchFilterType: 'All')
          }

  stages {
    stage('Build Backend') {
      steps {
        sh 'cd backend && ./build.sh'
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
        sh 'cd frontend && npx eslint --fix'
      }
    }
  }
  }


