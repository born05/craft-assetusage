# Craft Asset Usage plugin

Adds a column to the assets overview to see which assets are used or unused. For Craft 4.

The "Usage" field shows all relations and the "Current Usage" field shows relations excluding revisions and deleted elements.

## Setup
- Go to `admin/assets`
- Click the "sprocket" icon
- Check the "Usage" or "Current Usage" column
- Save
- The assets table should now show a "Usage" or "Current Usage" column indicating usage

### Support
- Everything using an asset field or the relations table, including matrix fields
- SuperTable

### Does NOT support (assets not connected through relations table)
- LinkIt
- Redactor
- ether/seo

## Commandline usage

```sh
craft assetusage/default/delete-unused  # Deletes all unused assets.
craft assetusage/default/list-unused    # Lists all unused assets.
```

## License

Copyright © [Born05](https://www.born05.com/)

See [license](https://github.com/born05/craft-assetusage/blob/master/LICENSE.md)
