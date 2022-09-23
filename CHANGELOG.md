# Changelog

All notable changes to the package will be documented in this file.

## WIP

- Fix spelling error in Upmind\ProvisionBase\Provider\Helper\Api\Response::getDefaultMessage()

## v2.1.2 - 2022-08-26

- Update ProviderJob always return result with `execution_time` debug

## v2.1.1 - 2022-08-12

- Implement international_phone validation rule workaround for new
  Nigeria 091 phone number format

## v2.1 - 2022-08-05

- Add new `certificate_pem` validation rule

## v2.0.5 - 2022-07-25

- Prevent type errors resulting from passing non-string values to preg_match in
  ValidationServiceProvider

## v2.0.4 - 2022-07-13

- FIX (Issue #2): Implement international_phone validation rule workaround for
  new Zimbabwe, Ivory Coast & Morocco phone number formats

## v2.0.3 - 2022-07-06

- FIX (Issue #1): Make Registry serialization compatible with PHP 7.4

## v2.0.2 - 2022-07-05

- Explicitly require `psr/log:^1.1`

## v2.0.1 - 2022-04-29

- FIX: Bind provision Registry as a singleton in ProvisionServiceProvider

## v2.0 - 2022-04-29

Initial public release
