Clover.xml parser for TeamCity
==============================

This small php script integrates the code coverage information from PHPUnit into TeamCity. The script will create the `teamcity-info.xml` (or append the metrics to an already existing xml file), which will automatically be parsed by TeamCity.


Usage
-----
Run PHPUnit and make sure to use the `--coverage-clover` argument to create the clover.xml. Then add another build step to run the `teamcity-clover.php` script and provide the path to the clover.xml as the only argument, for example: `php path/to/teamcity-clover.php %system.teamcity.build.tempDir%/clover.xml`.


Mapping
-------
Since TeamCity and PHPUnit do not provide the same code coverage information, some changes had to be made. The code coverage mapping is as follows (see the [TeamCity documentation](http://confluence.jetbrains.com/display/TCD8/Custom+Chart#CustomChart-DefaultStatisticsValuesProvidedbyTeamCity) for the key names):

TeamCity | PHPUnit
-------- | ----------
Blocks   | Statements
Lines    | Elements
Methods  | Methods
Classes  | *N/A*


Custom statistics
-----------------
In addition to the code coverage, the following custom statistics values are reported to TeamCity:

Custom Statistic Key  | Description
--------------------- | -----------------------------------
Files                 | Amount of files
Classes               | Amount of classes
LinesOfCode           | Amount of lines of code
NonCommentLinesOfCode | Amount of non-comment lines of code


Custom Graphs
-------------
You can create graphs for the new statistics as well (see the table above for the key names).

Example:
```xml
<graph title="Metrics" defaultFilters="showFailed" seriesTitle="Type">
    <valueType key="Files" title="Files" />
    <valueType key="Classes" title="Classes" />
    <valueType key="CodeCoverageAbsMTotal" title="Methods" />
</graph>
<graph title="Lines" defaultFilters="showFailed" seriesTitle="Type">
    <valueType key="LinesOfCode" title="Lines of code" />
    <valueType key="NonCommentLinesOfCode" title="Non-Comment lines of code" />
</graph>
```

See the [TeamCity documentation](http://confluence.jetbrains.com/display/TCD8/Custom+Chart) for more information.
