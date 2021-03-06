# FastyBird gateway node

[![Build Status](https://img.shields.io/travis/com/FastyBird/gateway-node.svg?style=flat-square)](https://travis-ci.com/FastyBird/gateway-node)
[![Code coverage](https://img.shields.io/coveralls/FastyBird/gateway-node.svg?style=flat-square)](https://coveralls.io/r/FastyBird/gateway-node)
![PHP](https://img.shields.io/packagist/php-v/fastybird/gateway-node?style=flat-square)
[![Licence](https://img.shields.io/packagist/l/FastyBird/gateway-node.svg?style=flat-square)](https://packagist.org/packages/FastyBird/gateway-node)
[![Downloads total](https://img.shields.io/packagist/dt/FastyBird/gateway-node.svg?style=flat-square)](https://packagist.org/packages/FastyBird/gateway-node)
[![Latest stable](https://img.shields.io/packagist/v/FastyBird/gateway-node.svg?style=flat-square)](https://packagist.org/packages/FastyBird/gateway-node)
[![PHPStan](https://img.shields.io/badge/PHPStan-enabled-brightgreen.svg?style=flat-square)](https://github.com/phpstan/phpstan)

## What is FastyBird gateway node?

Gateway node is a microservice for accessing to FastyBird ecosystem services via [{JSON:API}](https://jsonapi.org/) interface.

FastyBird gateway node is an [Apache2 licensed](http://www.apache.org/licenses/LICENSE-2.0) distributed microservice, developed in PHP with [Nette framework](https://nette.org).

## Requirements

FastyBird gateway node is tested against PHP 7.4 and [ReactPHP http](https://github.com/reactphp/http) 0.8 event-driven, streaming plaintext HTTP server and [RabbitMQ](https://www.rabbitmq.com/) 3.7 message broker

## Getting started

> **NOTE:** If you don't want to install it manually, try [docker image](#install-with-docker)

The best way to install **fastybird/gateway-node** is using [Composer](http://getcomposer.org/). If you don't have Composer yet, [download it](https://getcomposer.org/download/) following the instructions.
Then use command:

```sh
$ composer create-project --no-dev fastybird/gateway-node path/to/install
$ cd path/to/install
```

Everything required will be then installed in the provided folder `path/to/install`

This microservice is composed from one console command.

##### HTTP server

```sh
$ vendor/bin/fb-console fb:web-server:start
```

This command is to start build-in web server which is listening for incoming http api request messages from clients and is listening for new data from exchange bus from other microservices. 

## Install with docker

![Docker Image Version (latest by date)](https://img.shields.io/docker/v/fastybird/gateway-node?style=flat-square)
![Docker Image Size (latest by date)](https://img.shields.io/docker/image-size/fastybird/gateway-node?style=flat-square)
![Docker Cloud Build Status](https://img.shields.io/docker/cloud/build/fastybird/gateway-node?style=flat-square)

Docker image: [fastybird/gateway-node](https://hub.docker.com/r/fastybird/gateway-node/)

### Use docker hub image

```bash
$ docker run -d -it --name gateway fastybird/gateway-node:latest
```

### Generate local image

```bash
$ docker build --tag=gateway-node .
$ docker run -d -it --name gateway-node gateway-node
```

## Configuration

This microservices is preconfigured for default connections, but your infrastructure could be different.

Configuration could be made via environment variables:

| Environment Variable | Description |
| ---------------------- | ---------------------------- |
| `FB_APP_PARAMETER__DATABASE_VERSION=5.7` | MySQL server version |
| `FB_APP_PARAMETER__DATABASE_HOST=127.0.0.1` | MySQL host address |
| `FB_APP_PARAMETER__DATABASE_PORT=3306` | MySQL access port |
| `FB_APP_PARAMETER__DATABASE_DBNAME=gateway_node` | MySQL database name |
| `FB_APP_PARAMETER__DATABASE_USERNAME=root` | Username |
| `FB_APP_PARAMETER__DATABASE_PASSWORD=` | Password |
| | |
| `FB_APP_PARAMETER__SERVER_ADDRESS=0.0.0.0` | HTTP server host address |
| `FB_APP_PARAMETER__SERVER_PORT=8000` | HTTP server access port |
| | |
| `FB_APP_PARAMETER__SECURITY_CORS_ORIGIN=*` | HTTP server allowed origins |
| `FB_APP_PARAMETER__SECURITY_CORS_PROTOCOL=http` | HTTP server origin protocol |
| `FB_APP_PARAMETER__SECURITY_CORS_DOMAIN=localhost` | HTTP server origin domain |
| `FB_APP_PARAMETER__SECURITY_CORS_PORT=8000` | HTTP server origin port |

> **NOTE:** In case you are not using docker image or you are not able to configure environment variables, you could edit configuration file `./config/default.neon`

## Initialization

This microservice is using database, and need some initial data to be inserted into it. This could be done via shell command:

```sh
$ vendor/bin/fb-console fb:initialize
```

This console command is interactive and will ask for all required information.

After this steps, microservice could be started with [server command](#http-server)

## Feedback

Use the [issue tracker](https://github.com/FastyBird/gateway-node/issues) for bugs or [mail](mailto:code@fastybird.com) or [Tweet](https://twitter.com/fastybird) us for any idea that can improve the project.

Thank you for testing, reporting and contributing.

## Changelog

For release info check [release page](https://github.com/FastyBird/gateway-node/releases)

## Maintainers

<table>
	<tbody>
		<tr>
			<td align="center">
				<a href="https://github.com/akadlec">
					<img width="80" height="80" src="https://avatars3.githubusercontent.com/u/1866672?s=460&amp;v=4">
				</a>
				<br>
				<a href="https://github.com/akadlec">Adam Kadlec</a>
			</td>
		</tr>
	</tbody>
</table>

***
Homepage [https://www.fastybird.com](https://www.fastybird.com) and repository [http://github.com/fastybird/gateway-node](http://github.com/fastybird/gateway-node).
