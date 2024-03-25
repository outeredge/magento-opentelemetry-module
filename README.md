# magento-opentelemetry-module

A Magento 2 module that adds support for sending Logs to an OTLP endpoint.

Examples for some well known providers:

| Provider                                             | Endpoint                                              | Headers                                           | Resources                                             |
|------------------------------------------------------|-------------------------------------------------------|---------------------------------------------------|-------------------------------------------------------|
| [Middleware](https://middleware.io)                  | `https://{project}.middleware.io`                     | -                                                 | `mw.account_key={account_key},mw.resource_key=custom` |
| [Honeycomb](https://www.honeycomb.io)                | `https://api.honeycomb.io`                            | `x-honeycomb-team={api_key}`                      |                                                       |
| [OpenObserve](https://openobserve.ai)                | `https://api.openobserve.ai/api/{your_org_url}`       | `Authorization=Basic {token},stream-name=default` |                                                       |
| [Dynatrace](https://www.dynatrace.com)               | `https://{env}.live.dynatrace.com/api/v2/otlp`        | `Authorization=Api-Token {token}`                 |                                                       |
| [Grafana Cloud](https://grafana.com/products/cloud/) | `https://otlp-gateway-prod-{region}.grafana.net/otlp` | `Authorization=Basic {base64_token}`              |                                                       |
