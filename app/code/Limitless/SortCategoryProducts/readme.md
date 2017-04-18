
# Category Products Sort Order Batch Updates

This module adds a new admin backend interface to batch update the product sort order within categories using a CSV file.

The new page can be found in the admin interface at  
*Products > Batch Update Product Positions*.

The CSV file format is:

```
Numeric Category ID, Product SKU, Position
```
The CSV file should not contain column headers, only values.

The initial page contains a file upload form.

After selecting the file and clicking "Continue", the batch data is validated.

If any invalid records are found, the detected errors are listed.  
The "<- back" Button can be used to return to the file upload form to select an updated version of the file.

If there are any valid records, the "Continue" button can be used to process the valid records.  
If there are any invalid records they will be ignored.
