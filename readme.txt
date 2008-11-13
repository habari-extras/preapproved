Plugin: PreApproved
URL: http://habariproject.org
Version: 1.3
Author: Habari Project

Purpose 

PreApproved will automatically approve comments from commenters who already have the specified number of approved comments in your blog's database. The exception is comments that a spamchecker plugin has marked as spam. These will not be approved.

Requirements 

None.

Installation

1. Copy the plugin directory into your user/plugins directory or the site's plugins directory.
2. Go to the plugins page of your Habari admin panel.
3. Click on the Activate button for Meta SEO.

Usage

Configuration for PreApproved is performed by clicking the Configure button in its listing on your plugins page. The configuration screen has only one field - the number of comments the commenter must already have in the database to have new comments automatically approved. If you enter the number zero in this field, all comments will be automatically approved, unless a spamchecker plugin has run previously and marked the comment as spam.

After you're done making your configuration changes, click the 'Save' button at the bottom of the form to save the changes. Click the 'Close' button to completely close the form.

Uninstallation

1. Got to the plugins page of your Habari admin panel.
2. Click on the Deactivate button.
3. Delete the 'preapproved' directory from your user/plugins directory.

Cleanup

The plugin places one item in your Options table. It is preceded by the word preapproved. You can safely delete this entry after you uninstall the plugin.

Changelog

Version 1.2
Change: Added configuration dialog to the plugin.
Change: Removed content filter from the plugin since the content is filtered before it reaches the plugin.
