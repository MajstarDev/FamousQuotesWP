# Famous Quotes WP plugin

Andy, Chris:

When enabled, this plugin adds two links to the "Settings" menu in WP: "Famous Quotes" & "Famous Quotes settings".
![screenshot](https://raw.githubusercontent.com/MajstarDev/FamousQuotesWP/master/wpmenudemo.png)

The former is a CRUD page to manage quotes, and the latter is a place to enter API key, or generate a new key.
Plugin talks to Symfony JSON API at http://pavel.bootcamp.architechlabs.com:8000 - see API reference at http://github.com/MajstarDev/FamousQuotesAPI , be sure to check comments at the bottom of README for tradeoffs in Symfony app.
All API users must have API key to store/retrieve quotes; if you know somebody's key you'll be able to view/CRUD their collection of quotes and display their collection of quotes on your WP.
On the first run, plugin will suggest to generate a new API key, go ahead and generate, or you can try my key: TEST to see my collection of quotes.
No quotes are stored in WP DB for sure, but API key is stored in WP settings, and it's the only value stored by plugin in WP. I think it's pretty common to store keys to remote APIs in local storage.

Plugin displays random quote on the frontend at the bottom of each public page. I didn't enable RAND() in Doctrine as numeric function - instead, it fetches count and then uses Doctrine's setFirstResult to fetch Nth where N is random.
In Symfony app, user authentication is done by Guard Authenticator and pretty much is a Ctrl-C Ctrl-V from corresponding manual page on symfony.com.

### Validation (for the quote form) is done in two places: JS/jQuery in WP on form submit & then when persisting to Symfony API. It could have been done in three 
(JS, WP, and Symfony), but what's the point of performing server-side validation in WP if API checks it anyway. I'm sure if you disable JS nothing bad will happen.

### DB schema: Symfony API stores authors as separate entity so Author to Quote is OneToMany.

### WP plugin implementation: it's a classic WP mess of layout and logic. I was thinking about building it on WP_MVC (which is nice and true MVC way of building WP apps),
but then we'd end up with two plugins delivered and you only wanted one. I isolated WP event handlers in a class so that it's not that ugly as it could have been,
but still, mixing HTML & logic is not good and feels like doing PHP 10 or 15 years ago. Perhaps if I had more time, I'd plugged in Smarty or Twig and at least isolated layout.

### Deployment: deployed to Apache 2.4, MySQL 5.7, PHP 7.2, separate virtual hosts for WP (:80) & Symfony (:8000). Apache's mod_dir not good in checking directory specified in 
<Directory> and eats non-existing path, took me some 3 hours to figure or so. Symfony's app environment is properly set up as prod and composer class map has been optimized.
I've firewalled the droplet and only allowed 22, 80, 8000 with ufw. Local development was done on PHP 7.3 and SF dev server and various MySQL versions (old MySQL 5.6 for WP and
newer Maria DB 10.2 for SF since Doctrine migrations need utf8mb4 encoding).

## Authors

* **Pavel Kolas** - pk@majstar.com
