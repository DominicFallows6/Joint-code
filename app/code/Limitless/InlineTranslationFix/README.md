This module merges the js-translation.json file and the values from the 'translation' table together. 

Currently, without this module installed we can not use inline translate on the frontend. This is because the content in the js-translation.json file (which is generated when setup:static-content:deploy is run) is overwritten with the values from the 'translation' table in the database (where the inline translations are stored).

The module includes an around plugin which merges the two files together whenever inline translate is used. 