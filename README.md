# levtowords
Converts a number to BGN (New Bulgarian Lev) words description. Useful for generating invoices. 

### 1. Requirements

Make sure you are running PHP >= 7.4

### 2. Installation
 `composer require northbridg3/levtowords`
 
### 3. Usage

`$levConverter = new Lev(12.34);`

`echo $lev->toWords(); // "дванадесет лева и тридесет и четири стотинки`