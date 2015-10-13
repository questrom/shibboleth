# Shibboleth
This is a fork of the official development repository for the [WordPress Shibboleth plugin](http://wordpress.org/plugins/shibboleth), originally by [Will Norris](https://github.com/willnorris). Forked from https://github.com/mitcho/shibboleth and https://github.com/szul/shibboleth

Allows WordPress to externalize user authentication and account creation
to a Shibboleth Service Provider.

# Description

This plugin is designed to support integrating your WordPress or
WordPress MU blog into your existing identity management infrastructure
using a [Shibboleth](https://wiki.shibboleth.net/confluence/display/SHIB2/Home) Service Provider.

WordPress can be configured so that all standard login requests will be
sent to your configured Shibboleth Identity Provider or Discovery
Service. Upon successful authentication, a new WordPress account will be
automatically provisioned for the user if one does not already exist.
User attributes (username, first name, last name, display name,
nickname, and email address) can be synchronized with your enterprise’s
system of record each time the user logs into WordPress.

Finally, the user’s role within WordPress can be automatically set (and
continually updated) based on any attribute Shibboleth provides. For
example, you may decide to give users with an eduPersonAffiliation value
of *faculty* the WordPress role of *editor*, while the
eduPersonAffiliation value of *student* maps to the WordPress role
*contributor*. Or you may choose to limit access to WordPress altogether
using a special eduPersonEntitlement value.

# Installation

First and foremost, you must have the Shibboleth Service Provider
[properly
installed](https://wiki.shibboleth.net/confluence/display/SHIB2/Installation) and
working. If you don’t have Shibboleth working yet, I assure you that you
won’t get this plugin to work. This plugin expects Shibboleth to be
configured to use “lazy sessions”, so ensure that you have Shibboleth
configured with requireSession set to “false”. Upon activation, the
plugin will attempt to set the appropriate directives in WordPress’s
.htaccess file. If it is unable to do so, you can add this manually:

    AuthType shibboleth
    Require shibboleth

The option to automatically login the users into WordPress also works
when not using the lazy session options as it will force login into
WordPress. In other words, if the user has an active session and you are
requiring authentication to access this WordPress site and they need to
be logged into WordPress, then they will be logged in without having to
use the WordPress login page.

This works very well for sites that use WordPress for internal ticketing
and helpdesk functions where any access to content requires
authentication. Consider the following .htaccess options when used in
conjunction with the automatic login feature

    AuthType shibboleth
    ShibRequestSetting requireSession 1
    Require valid-user

OR

    Authtype shibboleth
    ShibRequestSetting requireSession 1
    Require isMemberOf group1 group2
    Require sAMAccountName user1 user 2

NOTE: If the plugin is successful in updating your .htaccess file, it
will place the option between a marked block:

    BEGIN Shibboleth
    END Shibboleth

If you add more options, you may want to consider moving all
configuration options out of this block as they will be cleared out upon
deactivation of the plugin.

## For WordPress MU

Shibboleth works equally well with WordPress MU using either vhosts or
folders for blogs. Upload the `shibboleth` folder to your `mu-plugins`
folder (probably `/wp-content/mu-plugins`). Move the file
`shibboleth-mu.php` from the `shibboleth` folder up one directory so
that it is in `mu-plugins` alongside the `shibboleth` folder. No need to
activate it, just configure it from the Shibboleth settings page, found
under “Site Admin”.

In a multisite setup with subdirectories you’ll need to update .htaccess
to properly allow access to /Shibboleth.sso. [Ohio State University
shared](https://web.osu.edu/technical-support/wordpress/wordpress-shib/)
the example below for doing so:

    # Shibboleth quick-exit
    RewriteEngine on
    RewriteCond %{REQUEST_URI} ^/Shibboleth.sso($|/)
    RewriteRule . - [L]
