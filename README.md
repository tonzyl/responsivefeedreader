# responsivefeedreader
A layer on top of FreshRSS to interact with postings directly in my feedreader.

I use this script to read my RSS feeds. It provides me with a response form underneath each RSS item. With that form I can post to several of my sites, to the Hypothes.is API, or to my own notes.
The processing of responses is not done in this script. This script only makes the form available.

It loads the groups (folders) and the feed ids belonging to each group, and then the unread item ids. From the unread items it takes the feed id, and constructs an array of all unread items in each group. Then per group it shows every unread item. Groups and items are at first not visible. Click on a group name to reveal item titles. Click on a green button next to a title to reveal the item. Click on the respons button to reveal the response form.

At the top there are buttons to mark a group or all groups read. This happens in the markread.php script that is called when a button is clicked.

When the response form is submitted the script verwerkfeed2.php is called. That processes the webform and handles the response. It is called in the background, so that you can keep reading your feeds.
