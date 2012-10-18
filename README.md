#WP JSON Posts#

Echoes posts as JSON instead of using the template. 
Works for the front page, static pages, single posts, custom post types, archives etc.

Update: Now with [WP Duplicate Post Prevention](https://github.com/ahultgren/WP-Duplicate-Plugin-Prevention) integrated!
This means you can use WP JSON Posts untouched in your plugins without fear of any complications 
if another plugin is also using WP JSON Posts.

_Note: Currently this plugin does not support custom queries that might be added in the tempalate. 
Only built in url's and their original results may be fetched._

##Feature overview

* Get JSON data for any post, page, archive, the front page etc.
* It works even for custom posts and taxonomy archives.
* Exclude the data you don't need.
* Customize number of posts and offset.
* All this are enabled completely automagically.
* No bloat. No unnecessary functions. You are free to do whatever you want.
* It’s free! Licensed under GPLv3.

##Basic usage

* Download WP JSON Posts
* Upload the plugin to your Wordpress installation
* Install WP JSON Posts
* Visit any page or post
* Add `?jsonposts=1` (or `&jsonposts=1`, if there's already a question mark in the URL)
* Voila, you got JSON!

##Parameters

All paramters are sent as a query string in the URL.

###Exclude

Most of the time you don't want all post data but only parts of it.
To save banwitdh you can customize what data to return. This is done using the parameter `&exclude`.
What to exclude is specified using the same name as wordpress uses for it's functions and are also
those found in the returned data. Multiple values are dash-separated (-). 
Thus if you see that you don't need `the_post_thumbnail` or `custom_fields` you simply send 
`&exclude=the_post_thumbnail-custom_fields`.

###Exclude custom fields

Sometimes you might only want some of the custom fields. Just use `&exclude_custom_fields=fieldname`.
Multiple values are dash-separated just like in the case of exclude.

###Posts per page

If you want a different number of posts returned than what the default wp_query would give you,
simply append `&posts_per_page=2` to the URL. Of course this will not work for a single post or page.

###Offset

If you wanna get for example just a few posts at the time from the front page, you have to change the
starting post each time. This is done by adding `&offset=5' to the URL. Default is 0.

###Post thumbnail size

If you want the image thumbnail you might have to specify what size you want the featured image to be. 
`&imgsize=case` would return the image size named "case". Default is "small".

###Terms and categories

Since version 1.0.2 the_category have been replaced with the_terms['category'] and will be removed shortly.
Thus if you want the categories of the posts you need to append `&the_terms=category` to the URL.
As whith the exlude parameter, multiple values are dash-separated (-). 