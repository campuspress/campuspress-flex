=== Secure Custom Fields ===
Contributors: wordpressdotorg, deliciousbrains, wpengine, elliotcondon, mattshaw, lgladdy, antpb, johnstonphilip, dalewilliams, polevaultweb
Tags: acf, fields, custom fields, meta, scf
Requires at least: 6.0
Tested up to: 6.6
Requires PHP: 7.4
Stable tag: 6.3.9
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Secure Custom Fields is a free fork of the Advanced Custom Fields plugin created originally for security updates, but now includes functionality improvements to make this plugin non-commercial in the plugin directory. If you'd like to get involved, submit some code! We want the 2M+ sites that will receive this update to have the best code and functionality possible.

== Description ==

Secure Custom Fields (SCF) turns WordPress sites into a fully-fledged content management system by giving you all the tools to do more with your data.

Use the SCF plugin to take full control of your WordPress edit screens, custom field data, and more.

**Add fields on demand.**
The SCF field builder allows you to quickly and easily add fields to WP edit screens with only the click of a few buttons! Whether it's something simple like adding an “author” field to a book review post, or something more complex like the structured data needs of an ecommerce site or marketplace, SCF makes adding fields to your content model easy.

**Add them anywhere.**
Fields can be added all over WordPress including posts, pages, users, taxonomy terms, media, comments and even custom options pages! It couldn't be simpler to bring structure to the WordPress content creation experience.

**Show them everywhere.**
Load and display your custom field values in any theme template file with our hassle-free, developer friendly functions! Whether you need to display a single value or generate content based on a more complex query, the out-of-the-box functions of SCF make templating a dream for developers of all levels of experience.

**Any Content, Fast.**
Turning WordPress into a true content management system is not just about custom fields. Creating new custom post types and taxonomies is an essential part of building custom WordPress sites. Registering post types and taxonomies is now possible right in the SCF UI, speeding up the content modeling workflow without the need to touch code or use another plugin.

**Simply beautiful and intentionally accessible.**
For content creators and those tasked with data entry, the field user experience is as intuitive as they could desire while fitting neatly into the native WordPress experience. Accessibility standards are regularly reviewed and applied, ensuring SCF is able to empower as close to anyone as possible.

**Documentation and developer guides.**
Over 10 plus years of vibrant community contribution alongside an ongoing commitment to clear documentation means that you'll be able to find the guidance you need to build what you want.

= Features =
* Simple & Intuitive
* Powerful Functions
* Over 30 Field Types
* Extensive Documentation
* Millions of Users


== Screenshots ==

1. Simple & Intuitive

2. Made for Developers

3. All About Fields

4. Registering Custom Post Types

5. Registering Taxonomies


== Changelog ==
= 6.3.9 =
*Release Date 22nd October 2024*

* Version update release

= 6.3.6.3 =
*Release Date 15th October 2024*

* Security - Editing a Field in the Field Group editor can no longer execute a stored XSS vulnerability. Thanks to Duc Luong Tran (janlele91) from Viettel Cyber Security for the responsible disclosure
* Security - Post Type and Taxonomy metabox callbacks no longer have access to any superglobal values, hardening the original fix from 6.3.6.2 even further
* Fix - SCF Fields now correctly validate when used in the block editor and attached to the sidebar

= 6.3.6.2 =
*Release Date 12th October 2024*

* Security - Harden fix in 6.3.6.1 to cover $_REQUEST as well.
* Fork - Change name of plugin to Secure Custom Fields.

= 6.3.6.1 =
*Release Date 7th October 2024*

* Security - SCF defined Post Type and Taxonomy metabox callbacks no longer have access to $_POST data. (Thanks to the Automattic Security Team for the disclosure)
