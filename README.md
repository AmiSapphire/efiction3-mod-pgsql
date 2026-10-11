# eFiction 3 Mod

This version is essentially a version of 'future 3.5.9' wiith some of my internal modifications. Support of bcrypt passwords with old MD5 fallback, new user registrations control, and more choices of tinyMCE are a few of my modifications.


## Requirements

- Minimum: PHP 7.4, PostgreSQL 10
- Recommended: PHP 8.4, PostgreSQL >10, but likely around 16
- Tested Working: PHP: 7.4.33, 8.5.8; PostgreSQL: 18.6


## Installation

Same as the usual eFiction 3.x installs.

<br>

# PostgreSQL Conversion

It's... functional, but not entirely usable... yet, if ever. It's entirely experimental, and parts of it were clearly written for old MySQL (around 4.0). Most difficult to wrangle is the LAST_INSERT_ID function, second most difficult would be SHOW TABLES, but that one is mostly solved here.

This entire thing is mostly for fun, so it's not too serious; may learn a bit more of PostgreSQL from this.
