# responsivefeedreader
A layer on top of FreshRSS to interact with postings directly in my feedreader.

freshreads2.php interacts with the Fever API of a FreshRSS instance I run on a VPS.
I use this script to read my RSS feeds. It provides me with a response form underneath each RSS item. With that form I can post to several of my sites (as bookmark, favourite, or reply), to the Hypothes.is API (as page note), or to my own notes (as a local markdown file in my Obsidian vault).
The processing of responses is not done in this script. This script only makes the form available.

It loads the groups (folders) and the feed ids belonging to each group, and then the unread item ids. From the unread items it takes the feed id, and constructs an array of all unread items in each group. Then per group it shows every unread item. Groups and items are at first not visible. Click on a group name to reveal item titles. Click on a green button next to a title to reveal the item. Click on the respons button to reveal the response form.

At the top there are buttons to mark a group or all groups read. This happens in the markread.php script that is called when a button is clicked.

When the response form is submitted the script verwerkfeed2.php is called. That processes the webform and handles the response. It is called in the background, so that you can keep reading your feeds. It in turn calls my micropub client (https://github.com/tonzyl/barebones_micropub_client) to post to my websites, and a script to post to Hypothes.is (hypothis.php).

The processing is hard coded to fit my own individual use case and the templates and structure I use and prefer to shape a posting, annotate a page or create a note. This means that you need to adapt things to your preferred workflow.

I chatted with GitHub Copilot writing this to understand how to do some things. The code, as you can see by its messiness, was written by myself. Because of the use of Copilot I put the code in the public domain.

THIS CODE IS MEANT TO RUN ON A LOCALHOST on your laptop. DO NOT RUN ON THE OPEN INTERNET, as no precautions of any kind have been taken. It is meant as an example for you to take ideas or inspiration from, as it has been created for my individual use case only.
