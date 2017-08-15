#Description

This module fixes the problem of the **maxClauseCount is set to 1024** Elasticsearch (ES) issue when a category or search request returns product results of more than 1024. The new version can be used with an unlimited number of clauses.

Updates the dynamic query for price aggregations to use a _filter terms query_ instead of _boolean must terms query_ inside ES.

The added benefit of using this different approach is that the query is cacheable and as well as being 2 to 3 times faster at first run.

**NB - This module will work with the stock ES implementation as well as the new Limitess ES implementation**
