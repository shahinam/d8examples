# Migrating Data from CSV

## Setup Data
copy .csv files from sample date into the files folder.

## Install the module
```drush en migrate_movies_csv -y```

## Migrate data
```drush migrate-import --group=movies_csv```


TODO:
- I was not able to migrate the taxonomy terms by automatically creating them with nodes, so for now I am migrating them
to text field.