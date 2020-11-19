# levtowords

[![PHP Version](https://img.shields.io/badge/php-%5E7.4-blue.svg)](https://img.shields.io/badge/php-%5E7.4-blue.svg)
[![Latest Stable Version](https://img.shields.io/packagist/v/northbridg3/levtowords)](https://packagist.org/packages/northbridg3/levtowords)

Converts a number to BGN (New Bulgarian Lev) words description. Useful for generating invoices. 

### 1. Requirements

Make sure you are running PHP >= 7.4

### 2. Installation
 `composer require northbridg3/levtowords`
 
### 3. Usage

`$levConverter = new Lev(12.34);`

`echo $levConverter->toWords(); // "дванадесет лева и тридесет и четири стотинки`