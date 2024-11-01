=== WP Flickity ===
Contributors: xpol555
Tags: slider, shortcode, responsive, easy-to-use, admin-pages, metafizzy, flickity, banner, best image slider, best slider, best slider plugin, carousel, carousel slider, content slider, content slideshow, flickity, flickity slider, flickity.js, free slider, gallery, gallery slider, Horizontal slider, HTML5 slider, image, image slider, image slider plugin, image slideshow, images, images slider, javascript, jquery, jquery slider, jquery slideshow, photo, Photo Slider, Photo Slideshow, photos, picture, picture slider, pictures, post slider, posts slider, responsive, responsive slider, responsive slideshow, revolution slider, shortcode, slide, slide show, slider, slider image, slider images, slider plugin, slider shortcode, slider widget, slides, slideshow, slideshow image, slideshow images, slideshow plugin, swipe, thumbnails slider, touch slider, wordpress slider, wordpress slideshow
Requires at least: 3.5.1
Tested up to: 4.7.4
Stable tag: 4.7.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

First Official Wordpress of Flickity Slider Plugin. Touch, responsive, flickable carousels.

== Description ==

This plugin provide you the core system of flickity slider with core wordpress functionalities and shortcodes

WATCH DEMO or Wordpress Flickity Slider In action [HERE](http://blog.paolofalomo.it/wordpress-flickity-slider-demo/ "WP Flickity DEMO")

_Want to contribute? Contact me! [info@paolofalomo.it](mailto:info@paolofalomo.it)_ or visit the official [gitlab repo](http://gitlab.com/paolofalomo/wp-flickity/)


**Plugin Features**:

* Fast and Responsive Slider
* Easy to Use Slider

== Installation ==

Here's what you need for installation:

1. Upload the plugin files to the `/wp-content/plugins/wp-flickity` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Use the "WP Flickity" admin menu page to configure the plugin and add sliders

Use the shortcode to display at frontend:
`[wp_flickity id="1"]`

== Screenshots ==

1. Easy reorder and delete your Flickity Sliders' images.
2. Simple interface to admin you Flickity Sliders.
3. Settings on each slider.

== Changelog ==

= 0.5.1 =
* Fixed Demo link on this readme file
* Fixed iframe previewer on backend forcing jquery to be present

= 0.5.0 =
* **New Feature!** [Use Posts as Slides #8](https://gitlab.com/paolofalomo/wp-flickity/issues/8) with a query builder
* **New Feature!** Added the preview at backend
* Fixed issue [ISSUE #9](https://gitlab.com/paolofalomo/wp-flickity/issues/9) to expand the support on newer php version
* Removed useless libraries
* Added help button
* Changed the level access to this plugin (admin section) from `read` capability to `upload_files`
* Added some 'wp-flickity' as alternative shortcode

= 0.4.6 =
* Hotfix on backend slide delete feature (x): [ISSUE #7](https://gitlab.com/paolofalomo/wp-flickity/issues/7)

= 0.4.5 =
* Hotfix on enqueuing files: [ISSUE #6](https://gitlab.com/paolofalomo/wp-flickity/issues/6)

= 0.4.4 =
* Hotfix on code of shortcode for someone having issues on frontend (thx to [wormboss](http://profiles.wordpress.org/wormboss/)).

= 0.4.3 =
* Hotfix on code for some systems. For someone should fix the [ISSUE #4](https://gitlab.com/paolofalomo/wp-flickity/issues/4)

= 0.4.2 =
* Added Some admin ui stuff
* [NEW FEATURE] Added settings for each slider. (More settings/Full setting will be available soon)

= 0.4.1 =
* Revisited the code structure and splitted functions in separated and good-named files.
* Now flickity images are orderable and removable. (see screenshot)
* Now flickity sliders are deletable
* Added "delete" button to remove old flickities
* Database structure has been updated to version 2 with support for delete (trash) operation.
* General Bug Fixes
* Minor UI changes

= 0.3.1 =
* Added tiny fix on code

= 0.3 =
* Credits Updated with a dedicated section
* Header Image Added

= 0.2 =
* Tested on Last Wordpress Version

= 0.1 =
* Initial release
* Admin page
* Shortcode
* Database model
* Upgradeable Database
* Use of wp.media for selecting images from backend

== Credits ==

All JS/CSS Frameworks credits goes to [Metafizzy](http://metafizzy.co/ "Metafizzy").

Documentation of external libraries can be found [here](http://flickity.metafizzy.co/ "Flickity Docs").
