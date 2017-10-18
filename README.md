WebComparision
==============

Install:
`php composer.phar install`.

Usage: 
`php app:websites-compare website1 website2 websiteN`

Instructions:
`website1` is website that other ones will be compared to. Time is measured using cURL.

This app is only a sample of a code, do not use it anywhere (bad performance).

Docs
=====

`WebsitesLoadTimeCompareCommand` is main command. This command triggers services to provide final report.

`CurlManager` contains curl logic.

`DummyMessageApi` dummy sms client - do nothing.

`WebsitesChecker` is using `CurlManager` to check if provided websites are fine.

`WebsitesComparator` in this service you can find logic of comparision websites and generating report.

In `tests` directory you will find tests.