
GROUP := dad-group-12
VERSION_LARAVEL := 1.0.14
VERSION_VUE := 1.0.14
VERSION_WS := 1.0.14


kubectl-pods:
	kubectl get pods

kubectl-apply:
	kubectl apply -f deployment

kubectl-update:
	kubectl delete -f deployment
	kubectl apply -f deployment

laravel-build:
	docker build -t registry.172.22.21.107.sslip.io/${GROUP}/api:v${VERSION_LARAVEL} \
    -f ./deployment/DockerfileLaravel ./laravel \
    --build-arg GROUP=${GROUP}

laravel-push:
	docker push registry.172.22.21.107.sslip.io/${GROUP}/api:v${VERSION_LARAVEL}


vue-build:
	docker build -t registry.172.22.21.107.sslip.io/${GROUP}/web:v${VERSION_VUE} -f ./deployment/DockerfileVue ./vue

vue-push:
	docker push registry.172.22.21.107.sslip.io/${GROUP}/web:v${VERSION_VUE}

ws-build:
	docker build -t registry.172.22.21.107.sslip.io/${GROUP}/ws:v${VERSION_WS} -f ./deployment/DockerfileWS ./websockets

ws-push:
	docker push registry.172.22.21.107.sslip.io/${GROUP}/ws:v${VERSION_WS}

fin-do-everithin:
	docker build -t registry.172.22.21.107.sslip.io/${GROUP}/api:v${VERSION_LARAVEL} \
    -f ./deployment/DockerfileLaravel ./laravel \
    --build-arg GROUP=${GROUP}
	docker build -t registry.172.22.21.107.sslip.io/${GROUP}/web:v${VERSION_VUE} -f ./deployment/DockerfileVue ./vue
	docker build -t registry.172.22.21.107.sslip.io/${GROUP}/ws:v${VERSION_WS} -f ./deployment/DockerfileWS ./websockets
	docker push registry.172.22.21.107.sslip.io/${GROUP}/api:v${VERSION_LARAVEL}
	docker push registry.172.22.21.107.sslip.io/${GROUP}/web:v${VERSION_VUE}
	docker push registry.172.22.21.107.sslip.io/${GROUP}/ws:v${VERSION_WS}
	kubectl delete -f deployment
	kubectl apply -f deployment
