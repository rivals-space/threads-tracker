# Changelog

## [1.0.4](https://github.com/rivals-space/threads-tracker/compare/v1.0.3...v1.0.4) (2024-03-31)


### Bug Fixes

* **notifications-fetch:** remove index from generated query string ([#11](https://github.com/rivals-space/threads-tracker/issues/11)) ([5e7b92d](https://github.com/rivals-space/threads-tracker/commit/5e7b92dbfe453cf19647e547277f91ccdb5fcb29))
* only use deprecation channel in dev/test ([4828ea6](https://github.com/rivals-space/threads-tracker/commit/4828ea617806ae9c2df1583c678209fc9b22b232))
* **sentry:** ignore CommandNotFoundException ([579a4e0](https://github.com/rivals-space/threads-tracker/commit/579a4e01499e7d0913299f661020e5b303382853))

## [1.0.3](https://github.com/rivals-space/threads-tracker/compare/v1.0.2...v1.0.3) (2024-03-31)


### Bug Fixes

* **messenger:** completly remove console logger in production ([339be92](https://github.com/rivals-space/threads-tracker/commit/339be92998fbae44829ab717c76e426497fa2aed))
* **notifications-handler:** dismiss notifications in case of error too ([50cb6ba](https://github.com/rivals-space/threads-tracker/commit/50cb6ba1f14b4cfeff40833c11195ce033585251))

## [1.0.2](https://github.com/rivals-space/threads-tracker/compare/v1.0.1...v1.0.2) (2024-03-31)


### Bug Fixes

* avoid mentioning newly federated user ([016804f](https://github.com/rivals-space/threads-tracker/commit/016804fb7e24b95d94bef4a289599bf56ebf311b))
* disable console message ([29195fe](https://github.com/rivals-space/threads-tracker/commit/29195feaa34edd33fa01a187f294129a394cca71))
* **messenger:** add doctrine middlewares for long-running messengers ([a5d6709](https://github.com/rivals-space/threads-tracker/commit/a5d67090dbdb8e7711ede2a821c50e219bd9d233))
* only dismiss notification when handled successfully ([07975d1](https://github.com/rivals-space/threads-tracker/commit/07975d1cfb030455f1d91d1eea77405f35f40481))

## [1.0.1](https://github.com/rivals-space/threads-tracker/compare/v1.0.0...v1.0.1) (2024-03-31)


### Bug Fixes

* add missing messenger migrations ([0ab84b2](https://github.com/rivals-space/threads-tracker/commit/0ab84b2ca1678f2a3f357ee56a4021d01b31b719))
* **dev:** fix dev container build ([4418a8b](https://github.com/rivals-space/threads-tracker/commit/4418a8be25bd9b85ba3a28d324c5b0ad9cda77ce))
* now correctly checking if already tracking a thread user ([59fca69](https://github.com/rivals-space/threads-tracker/commit/59fca696939a0dc279f62e492cd7970452b06819))

## 1.0.0 (2024-03-31)


### Features

* add force check commands ([d0aca94](https://github.com/rivals-space/threads-tracker/commit/d0aca94df042ccfc9748e540564d3f5b4cce1890))
* add log level configuration ([16e5933](https://github.com/rivals-space/threads-tracker/commit/16e5933d260d8b3cd79a26edd29dc0ccc2b48b83))
* add proper production compose file ([340bde9](https://github.com/rivals-space/threads-tracker/commit/340bde90011b36bf244e6451563c6eb03d012b1d))
* **chart:** add helm chart ([e2558c0](https://github.com/rivals-space/threads-tracker/commit/e2558c0b6c74165b7d7d7a220fc0b188f7c46c43))
* init project ([58e3fca](https://github.com/rivals-space/threads-tracker/commit/58e3fca83fc9be59d67bee2ea168f5392383ad9a))
* **sentry:** add sentry ([3052edd](https://github.com/rivals-space/threads-tracker/commit/3052edd65ba5043fe8503bbf4821f3f47602c7ab))


### Bug Fixes

* **chart:** add missing sentry config ([b6cf970](https://github.com/rivals-space/threads-tracker/commit/b6cf97050862c21f519728f8735476a020a72705))
* **chart:** mastodon token env var name ([cafbe33](https://github.com/rivals-space/threads-tracker/commit/cafbe33fefb0b4d340d476fb42de432d3b516574))
* **compose-prod:** add missing lock DSN ([2cbdaf3](https://github.com/rivals-space/threads-tracker/commit/2cbdaf3e29ba2dbeb87fbdf7455def7b615deae8))
* **scheduler:** typo ([5d400da](https://github.com/rivals-space/threads-tracker/commit/5d400da56ff6ddf0657e02ea4cf127317da72809))
* **sentry:** wrong release name ([ab033ca](https://github.com/rivals-space/threads-tracker/commit/ab033ca29d95f8653b156e82e17513ba3a221e1b))
* threads queue name ([c6394de](https://github.com/rivals-space/threads-tracker/commit/c6394de4d9f28b5f2ca954cd39d2c97fd62c8866))
