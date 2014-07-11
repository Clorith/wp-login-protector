Login Protector
=========
---

Login Protector is intended as a [WordPress MU] [WPMU] (Must Use) plugin to help mitigate bruteforce attempts. It is not intended as a defintiey solution to an ever existent problem, but is merely a helper for those limited by their hosting providers in what they can do in such situations.

---


Installation
---

* Place the content of `login-protector.php` in your MU directory
* If desired, change the `$protected_login_fails` value to a value suitable to your needs
* You're done, things are rolling on their own!

---

What we're doing
---
So, what is it this plugin does?

When a login is attempts, it will check if the login failed, either by the username being wrong, or the password being wrong.

Once it detects an invalid login, it will bump the value of logins from that IP by one, once it exceeds the configurable limit of failed logins, it will store a value saying "block login from $IP".

We then have the second part of the plugin, which checks every time either an admin page, or the login page, is loaded and sees if the "block login fro $IP" value is set for the current user. If the users IP is set as blocked, we will send a [404 Not Found] [404] code to the client requesting the page, and then stop all processing (this is to limit the amount of data we waste on illegitimate requests).

We send the 404 code hoping the brute force script is "intelligent", if it is it will identify the code and know that it can't do anything else here and stop trying.

---

Caveats
----

Of course, there are some limitations to this;

* A request is still sent to your webserver
* You are making database queries using resources

Ideally the hosting provider will be able to deal with the brute force attempts in a much better manner than this, but in a bind this is an acceptable approach and less taxing/more secure than letting the attempts run until they grow tired.

[WPMU]:http://codex.wordpress.org/Must_Use_Plugins
[404]:http://en.wikipedia.org/wiki/HTTP_404
