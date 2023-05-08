# RequestCollector

Library to collect the request/response sent via Symfony HttpClient or Guzzle.

## Why and main goal

In one of commercial projects we had more than 70 integrations with external services.
For many of them we didn't have neither test accounts nor test environments.
Even when we had credentials still there were problems to test edge-case issues or prepare fake data.
The goal of this library is to help debugging those integrations even on production environments.
Each sent request and response can be logged and sanitized from private/personal data.
