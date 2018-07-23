# OpenFaaS and PHP - PHP Meetup August 2018#
## Introduction ##



## My Dev Setup ##
* Windows 10 
* Docker for Windows - Kubernetes Enabled
* Visual Studio Code

## Links: ##

* [Installing OpenFaas](http://docs.openfaas.com/deployment/)
* [OpenFaaS PHP Template](https://github.com/itscaro/openfaas-template-php)

## Slides ##
* [Serverless PHP - Powerpoint](/sildes/slides.pptx)
* [Serverless PHP - PDF](/sildes/slides.pdf)


## Requirements

* Kubernetes
* Helm for Kubernetes
* [OpenFaas](https://github.com/openfaas/faas-netes/blob/master/chart/openfaas/README.md)

## Adding Redis as Storage
We will use Redis as our todo store.

### Deploy Redis

```
helm install stable/redis --name todo-db --namespace openfaas-fn --set persistence.enabled=false
```

`helm upgrade openfaas --install openfaas/openfaas --namespace openfaas  --set functionNamespace=openfaas-fn`

## Issues with pulling local images

If you get issues with pulling your local image, you can set up a repository.
to see logs of a function `kubectl logs -n openfaas-fn deploy/FUNCTIONNAME`.

### Using Local Repository

Setting up a Repository
`$ docker run -d -p 5000:5000 --restart always --name registry registry:2`

To push to the local repository do:

* `faas-cli build -f .\stack.yml`
* `faas-cli push -f .\stack.yml`

Change image names from  `phpmeetup/function` to `localhost:5000/function`  

### Setting image_pull_policy

`[Environment]::SetEnvironmentVariable("image_pull_policy", "IfNotPresent", "Process")`

`$ export image_pull_policy=IfNotPresent`

## Template for OpenFaas and PHP

`$ faas-cli template pull https://github.com/monosolutions/openfaas-template-php`

## Stack

### To deploy

`$ faas-cli build -f .\stack.yml`
`f$ aas-cli push -f .\stack.yml`
`$ faas-cli deploy -f .\stack.yml`

## Gloo - Gateway

 glooctl route create --http-method get --path-exact /todo --upstream openfaas-gateway-8080 --function readtodos