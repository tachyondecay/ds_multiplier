# Data Source Multiplier

Inspired by [this discussion](http://getsymphony.com/discuss/thread/91778/1/), DataSource Multiplier allows you to execute a data source for *each* value in a filter's list rather than filtering on any of the values in that list.

All you have to do is tick a checkbox while setting up the filters on the data source page!

- Version: 0.1
- Author: Ben Babcock <ben@tachyondecay.net>
- Updated: August 18, 2012
- GitHub Repository: https://github.com/tachyondecay/ds_multiplier

## Installation & Use

You can always install the latest version through git: `git clone git://github.com/tachyondecay/ds_multiplier.git`

- Make sure that the extension is in a folder named `ds_multiplier`. Upload this to your Symphony `extensions` folder.
- Enable the extension from the **Extensions** page in the Symphony backend.

## Example

Suppose you have two sections, Categories and Articles, linked how one would expect, and you want to display the 5 most recent articles from *each* category. Here's how you would set up your data sources using data source multiplier.

First, create a data source for the Categories section. You may add some filters here if you don't want to display articles for *every* category. Output the system ID as an output parameter.

Then, create a data source for the Articles section:
  - Filter it on the field linked to the Categories section. For the value of the filter, enter the output parameter from the data source above. E.g., if you named the data source "Categories", then enter `{$ds-categories.system-id}`.
  - Limit the output of the data source to 5 entries per page (or however many entries you want per category).
  - You may also want to group the XML output by this field.
  - You have to save the second data source and then go back and edit it in order for the multiplier checkbox to appear. While editing the data source, tick "Execute for each value in this parameter" below the filter on the category field. Save your changes.

Add both of the above data sources to a page.

## Credits

Marcin's [Conditionalizer](http://symphonyextensions.com/extensions/conditionalizer/) extension was extremely helpful when it came to learning how to manipulate data sources from an extension.

## Changelog

### 0.1 (August 18, 2012)

- Very experimental version created on a whim.